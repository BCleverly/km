<?php

declare(strict_types=1);

namespace App\Actions\Api\Auth;

use App\Models\User;
use App\TargetUserType;
use App\Enums\SubscriptionPlan;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterUser
{
    use AsAction;

    public function handle(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => ['required', 'integer', 'in:1,2,3'], // Male, Female, Couple
            'username' => ['nullable', 'string', 'max:255', 'unique:profiles,username'],
            'bdsm_role' => ['nullable', 'integer', 'in:1,2,3'], // Dominant, Submissive, Switch
            'about' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => TargetUserType::from($request->user_type),
            'subscription_plan' => SubscriptionPlan::Free,
        ]);

        // Create profile
        $user->profile()->create([
            'username' => $request->username,
            'bdsm_role' => $request->bdsm_role ? \App\Enums\BdsmRole::from($request->bdsm_role) : null,
            'about' => $request->about,
        ]);

        // Start trial if available
        if (!$user->has_used_trial) {
            $user->startTrial();
        }

        // Create API token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return [
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }
}