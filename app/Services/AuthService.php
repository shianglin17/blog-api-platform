<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid email or password');
        }

        $accessToken = $user->createToken('api-token')->plainTextToken;

        $refreshToken = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * @throws AuthenticationException
     */
    public function refresh(string $refreshToken): array
    {
        $hashedToken = hash('sha256', $refreshToken);

        $tokenRecord = RefreshToken::where('token', $hashedToken)->first();

        if (!$tokenRecord) {
            throw new AuthenticationException('Invalid or expired refresh token');
        }

        if ($tokenRecord->expires_at->isPast()) {
            $tokenRecord->delete();
            throw new AuthenticationException('Invalid or expired refresh token');
        }

        $user = $tokenRecord->user;

        $accessToken = $user->createToken('api-token')->plainTextToken;

        $tokenRecord->delete();

        $newRefreshToken = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
        ];
    }

    private function createRefreshToken(User $user): string
    {
        $plainToken = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(30),
        ]);

        return $plainToken;
    }
}
