<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use PragmaRX\Google2FA\Google2FA;

class UserController extends Controller
{
    // ─── Register ──────────────────────────────────
    public function userRegister(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|min:6|confirmed",
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ✅ Auto setup 2FA on register
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_enabled = false;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // ✅ Use Google Chart API to generate QR — no GD/Imagick needed
        $qrCodeImage = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);

        $accessToken = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $user->createRefreshToken();

        return response()->json([
            "status" => "success",
            "message" => "User registered successfully",
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "token_type" => "bearer",
            "expires_in" => 60 * 60,
            "secret" => $secret,
            "qr_code_url" => $qrCodeUrl,
            "qr_code_image" => $qrCodeImage,
            "user" => $user->makeHidden([
                'refresh_token',
                'refresh_token_expires_at',
                'two_factor_secret'
            ])
        ], 201);
    }
    // ─── Login ─────────────────────────────────────
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = Auth::user();

        // 🔥 Important Debug (temporary rakho)
        \Log::info('Login Attempt', [
            'email' => $user->email,
            'two_factor_enabled' => $user->two_factor_enabled,
            'has_secret' => !empty($user->two_factor_secret)
        ]);

        // Agar 2FA enabled hai to QR + OTP maango
        if ($user->two_factor_enabled && $user->two_factor_secret) {

            // Decrypt the stored secret (same as register — it's stored encrypted)
            $secret = decrypt($user->two_factor_secret);

            // Build the otpauth:// URL (same as register's getQRCodeUrl output)
            $google2fa   = new Google2FA();
            $qrCodeUrl   = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // Generate QR image via qrserver.com — no GD/Imagick needed (same as register)
            $qrCodeImage = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);

            // Short-lived temp token for the verify step (10 min)
            $tempToken = $user->createToken('2fa-login-temp', ['2fa:verify'], now()->addMinutes(10))
                ->plainTextToken;

            \Log::info('2FA Login - QR Generated', [
                'email'  => $user->email,
                'qr_url' => $qrCodeUrl,
            ]);

            return response()->json([
                'status'        => 'success',
                'message'       => 'Two-factor authentication required',
                'requires_2fa'  => true,
                'temp_token'    => $tempToken,
                'qr_code_url'   => $qrCodeUrl,    // otpauth:// URI
                'qr_code_image' => $qrCodeImage,  // Blade JS injects this into #qrFrame
                'secret_key'    => $secret,        // shown in the manual key input
            ]);
        }

        // Normal login (2FA off hai)
        $accessToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            "requires_2fa" => false,
            'access_token' => $accessToken,
            'redirect' => '/dashboard'
        ]);
    }

    // ─── Refresh Token ─────────────────────────────
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        $user = User::where('refresh_token', $request->refresh_token)
            ->where('refresh_token_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired refresh token'
            ], 401);
        }

        $newAccessToken = $user->createToken('api-token')->plainTextToken;
        $newRefreshToken = $user->createRefreshToken(); // token rotation

        return response()->json([
            'status' => 'success',
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'bearer',
            'expires_in' => 60 * 60
        ]);
    }

    // ─── Logout ────────────────────────────────────
    public function logout(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if ($user) {
                $user->revokeRefreshToken();
            }

            Auth::guard('api')->logout();

            return response()->json([
                "status" => "success",
                "message" => "Logged out successfully"
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout'
            ], 500);
        }
    }

    // ─── Simple Toggle 2FA (0 <-> 1) ─────────────────────
    // ─── Toggle 2FA ────────────────────────────────
    public function toggle2FA(Request $request)
    {
        $request->validate(['enable' => 'required|boolean']);

        $token = $request->bearerToken();
        $personalToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$personalToken) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $user = $personalToken->tokenable;
        $user->two_factor_enabled = $request->enable;
        $user->save();

        return response()->json([
            'status' => 'success',
            'enabled' => $user->two_factor_enabled,
            'message' => $request->enable ? '2FA enabled' : '2FA disabled'
        ]);
    }

    // ─── Get User Profile ──────────────────────────
    public function me()
    {
        $user = Auth::guard('api')->user() ?? Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user->makeHidden([
                'refresh_token',
                'refresh_token_expires_at',
                'two_factor_secret'
            ])
        ]);
    }

    // ─── Setup 2FA ─────────────────────────────────
    public function setup2FA()
    {
        $user = Auth::guard('api')->user();
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();

        // Store encrypted secret (not enabled yet)
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_enabled = false;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'status' => 'success',
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'message' => 'Scan the QR code with Google Authenticator'
        ]);
    }

    // ─── Enable 2FA ────────────────────────────────
    public function enable2FA(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        // Log everything useful
        \Log::info('2FA Enable Request', [
            'headers' => $request->headers->all(),
            'bearer_token' => $request->bearerToken(),
            'authorization_header' => $request->header('Authorization'),
            'all_input' => $request->all(),
        ]);

        // ✅ Authorization header se token nikalo
        $token = $request->bearerToken();


        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'No token provided'], 401);
        }

        // ✅ Sanctum token se user nikalo
        $personalToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$personalToken) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        $user = $personalToken->tokenable;

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 401);
        }

        $google2fa = new Google2FA();

        if (!$user->two_factor_secret) {
            return response()->json([
                'status' => 'error',
                'message' => '2FA setup not initiated'
            ], 400);
        }

        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid 2FA code'
            ], 422);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Two-factor authentication has been enabled successfully'
        ]);
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No token provided'
            ], 401);
        }

        // Debug log
        \Log::info('2FA Verify Attempt', [
            'bearer_token_present' => !empty($token),
            'code' => $request->code
        ]);

        $personalToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$personalToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ], 401);
        }

        if ($personalToken->expires_at && $personalToken->expires_at->isPast()) {
            $personalToken->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired'
            ], 401);
        }

        $user = $personalToken->tokenable;

        if (!$user || !$user->two_factor_secret) {
            return response()->json([
                'status' => 'error',
                'message' => 'User or 2FA not found'
            ], 401);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid 2FA code'
            ], 422);
        }

        // Success - delete temp token and create real one
        $personalToken->delete();

        $accessToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => '2FA verified successfully',
            'access_token' => $accessToken,
            'redirect' => '/dashboard'
        ]);
    }

    // ─── Optional: Revoke all tokens ───────────────
    public function revokeAllTokens()
    {
        $user = Auth::guard('api')->user();
        $user->revokeRefreshToken();

        return response()->json([
            'status' => 'success',
            'message' => 'All refresh tokens revoked successfully'
        ]);
    }
}