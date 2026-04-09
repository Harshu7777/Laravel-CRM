<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PragmaRX\Google2FA\Google2FA;

class UserController extends Controller
{
    // ─── Register ──────────────────────────────────────────────────────────────
    public function userRegister(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'sometimes|in:admin,staff,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'customer',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        // Auto-generate 2FA secret on register
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_enabled = false;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);
        $qrCodeImage = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);

        $accessToken = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $user->createRefreshToken();

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'qr_code_image' => $qrCodeImage,
            'user' => $user->makeHidden(['refresh_token', 'refresh_token_expires_at', 'two_factor_secret']),
        ], 201);
    }

    // ─── Login ─────────────────────────────────────────────────────────────────
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Regenerate session ID after login (security)
        $request->session()->regenerate();

        $user = Auth::user();

        \Log::info('Login OK', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'session_id' => session()->getId(),
        ]);

        // ── 2FA check ────────────────────────────────────────────────────────
        if ($user->two_factor_enabled && $user->two_factor_secret) {
            // Log out temporarily — login again only after OTP verified
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $secret = decrypt($user->two_factor_secret);
            $google2fa = new Google2FA();
            $qrCodeUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);
            $qrCodeImage = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);

            // Store user ID in session for the verify step
            session(['2fa:user:id' => $user->id]);

            return response()->json([
                'status' => 'success',
                'requires_2fa' => true,
                'qr_code_image' => $qrCodeImage,
                'qr_code_url' => $qrCodeUrl,
                'secret_key' => $secret,
            ]);
        }

        // ── Normal login — session is set, just redirect ──────────────────────
        return response()->json([
            'status' => 'success',
            'requires_2fa' => false,
            'redirect' => '/dashboard',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    // ─── Verify 2FA OTP ────────────────────────────────────────────────────────
    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = session('2fa:user:id');

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please log in again.',
            ], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Try again.',
            ], 422);
        }

        // ✅ OTP verified — now fully log in via session
        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('2fa:user:id');

        return response()->json([
            'status' => 'success',
            'redirect' => '/dashboard',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    // ─── Enable 2FA ────────────────────────────────────────────────────────────
    public function enable2FA(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if (!$user->two_factor_secret) {
            return response()->json(['status' => 'error', 'message' => '2FA setup not initiated'], 400);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json(['status' => 'error', 'message' => 'Invalid 2FA code'], 422);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Two-factor authentication enabled successfully',
        ]);
    }

    // ─── Toggle 2FA ────────────────────────────────────────────────────────────
    public function toggle2FA(Request $request)
    {
        $request->validate([
            'enable' => 'required|boolean',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please login again.'
            ], 401);
        }

        $user->two_factor_enabled = $request->enable;
        $user->save();

        return response()->json([
            'status' => 'success',
            'enabled' => $user->two_factor_enabled,
            'message' => $request->enable
                ? 'Two-factor authentication has been enabled successfully.'
                : 'Two-factor authentication has been disabled successfully.',
        ]);
    }

    // ─── Get User Profile ───────────────────────────────────────────────────────
    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user->makeHidden(['refresh_token', 'refresh_token_expires_at', 'two_factor_secret']),
        ]);
    }

    // ─── Logout ────────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            $user->revokeRefreshToken();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
            ]);
        }

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    // ─── Refresh Token ─────────────────────────────────────────────────────────
    public function refreshToken(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        $user = User::where('refresh_token', $request->refresh_token)
            ->where('refresh_token_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }

        $newAccessToken = $user->createToken('api-token')->plainTextToken;
        $newRefreshToken = $user->createRefreshToken();

        return response()->json([
            'status' => 'success',
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'bearer',
            'expires_in' => 3600,
        ]);
    }

    // ─── Users CRUD ────────────────────────────────────────────────────────────
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ]);
        return redirect()->route('users.index')->with('success', 'User updated');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted');
    }
}