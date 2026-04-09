<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasApiTokens;

    // ✅ FIX: Added role, phone, address, two_factor_* — were missing before
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'two_factor_secret',
        'two_factor_enabled',
        'refresh_token',
        'refresh_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'refresh_token_expires_at' => 'datetime',
            'two_factor_enabled'       => 'boolean',
        ];
    }

    // ── JWT ──────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // ── Refresh Token ────────────────────────────────
    public function createRefreshToken()
    {
        $refreshToken = bin2hex(random_bytes(40));
        $this->refresh_token            = $refreshToken;
        $this->refresh_token_expires_at = Carbon::now()->addDays(7);
        $this->save();
        return $refreshToken;
    }

    public function validateRefreshToken($token)
    {
        return $this->refresh_token === $token &&
               $this->refresh_token_expires_at &&
               Carbon::now()->lessThan($this->refresh_token_expires_at);
    }

    public function revokeRefreshToken()
    {
        $this->refresh_token            = null;
        $this->refresh_token_expires_at = null;
        $this->save();
    }
}