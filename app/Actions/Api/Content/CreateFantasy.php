<?php

declare(strict_types=1);

namespace App\Actions\Api\Content;

use App\Models\Fantasy;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateFantasy
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'min:50'],
            'is_premium' => ['boolean'],
            'is_anonymous' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Check if user can create premium content
        if ($request->get('is_premium', false) && !$user->canAccessPremiumContent()) {
            return [
                'success' => false,
                'message' => 'Premium content creation is not available on your current plan',
            ];
        }

        $fantasy = Fantasy::create([
            'content' => $request->content,
            'user_id' => $user->id,
            'is_premium' => $request->get('is_premium', false),
            'is_anonymous' => $request->get('is_anonymous', false),
            'status' => 1, // Pending
        ]);

        // Attach tags if provided
        if ($request->has('tags') && is_array($request->tags)) {
            $fantasy->syncTags($request->tags);
        }

        return [
            'success' => true,
            'message' => 'Fantasy created successfully and submitted for review',
            'fantasy' => [
                'id' => $fantasy->id,
                'content' => $fantasy->content,
                'is_premium' => $fantasy->is_premium,
                'is_anonymous' => $fantasy->is_anonymous,
                'status' => $fantasy->status,
                'status_label' => $fantasy->status_label,
                'created_at' => $fantasy->created_at,
            ],
        ];
    }
}