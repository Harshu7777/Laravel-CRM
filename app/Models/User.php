<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens; 
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable , HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'refresh_token',
        'refresh_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token', // Hide refresh token from JSON responses
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'refresh_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Create a refresh token for the user
     */
    public function createRefreshToken()
    {
        // Generate a unique refresh token
        $refreshToken = bin2hex(random_bytes(40));
        
        // Set expiration (7 days from now)
        $expiresAt = Carbon::now()->addDays(7);
        
        // Store in database
        $this->refresh_token = $refreshToken;
        $this->refresh_token_expires_at = $expiresAt;
        $this->save();
        
        return $refreshToken;
    }

    /**
     * Validate refresh token
     */
    public function validateRefreshToken($token)
    {
        return $this->refresh_token === $token && 
               $this->refresh_token_expires_at && 
               Carbon::now()->lessThan($this->refresh_token_expires_at);
    }

    /**
     * Revoke refresh token
     */
    public function revokeRefreshToken()
    {
        $this->refresh_token = null;
        $this->refresh_token_expires_at = null;
        $this->save();
    }
}