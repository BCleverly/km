<?php

declare(strict_types=1);

namespace App\Actions\Api\Auth;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginUser
{
    use AsAction;

    public function handle(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        // Create API token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }
}