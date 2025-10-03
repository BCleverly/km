<?php

declare(strict_types=1);

namespace App\Actions\Api\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class LogoutUser
{
    use AsAction;

    public function handle(Request $request): array
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Logged out successfully',
        ];
    }
}