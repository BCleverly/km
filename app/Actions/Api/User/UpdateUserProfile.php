<?php

declare(strict_types=1);

namespace App\Actions\Api\User;

use App\Enums\BdsmRole;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateUserProfile
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'unique:profiles,username,' . $user->profile?->id],
            'about' => ['sometimes', 'string', 'max:1000'],
            'bdsm_role' => ['sometimes', 'nullable', 'integer', 'in:1,2,3'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        // Update or create profile
        $profileData = [];
        if ($request->has('username')) {
            $profileData['username'] = $request->username;
        }
        if ($request->has('about')) {
            $profileData['about'] = $request->about;
        }
        if ($request->has('bdsm_role')) {
            $profileData['bdsm_role'] = $request->bdsm_role ? BdsmRole::from($request->bdsm_role) : null;
        }

        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);
        }

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()->load('profile'),
        ];
    }
}