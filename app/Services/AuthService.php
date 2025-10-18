<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function login(string $email, string $password): array|false
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        $accessToken = $user->createToken('api-token')->plainTextToken;

        $refreshToken = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
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
