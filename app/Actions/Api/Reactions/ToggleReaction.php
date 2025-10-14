<?php

declare(strict_types=1);

namespace App\Actions\Api\Reactions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToggleReaction
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'reactable_type' => ['required', 'string', 'in:story,fantasy,status,task'],
            'reactable_id' => ['required', 'integer'],
            'reaction_type' => ['required', 'string', 'in:like,love,laugh,wow,sad,angry'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        // Get the reactable model
        $modelClass = match ($request->reactable_type) {
            'story' => \App\Models\Story::class,
            'fantasy' => \App\Models\Fantasy::class,
            'status' => \App\Models\Status::class,
            'task' => \App\Models\Tasks\Task::class,
        };

        $reactable = $modelClass::find($request->reactable_id);

        if (!$reactable) {
            return [
                'success' => false,
                'message' => 'Content not found',
            ];
        }

        // Toggle reaction
        $reaction = $user->toggleReaction($reactable, $request->reaction_type);

        return [
            'success' => true,
            'message' => $reaction ? 'Reaction added' : 'Reaction removed',
            'reaction' => [
                'type' => $request->reaction_type,
                'active' => $reaction,
            ],
            'reactions' => [
                'count' => $reactable->reactions()->count(),
                'user_reacted' => $user->hasReactedTo($reactable),
            ],
        ];
    }
}