<?php

declare(strict_types=1);

namespace App\Actions\Api\Subscription;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetUserSubscription
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        return [
            'success' => true,
            'subscription' => [
                'current_plan' => [
                    'value' => $user->subscription_plan->value,
                    'label' => $user->subscription_plan->label(),
                    'description' => $user->subscription_plan->description(),
                ],
                'status' => $user->subscription_status,
                'has_active_subscription' => $user->hasActiveSubscription(),
                'has_paid_subscription' => $user->hasPaidSubscription(),
                'is_on_trial' => $user->isOnTrial(),
                'trial_ends_at' => $user->trial_ends_at,
                'needs_subscription_choice' => $user->needsSubscriptionChoice(),
                'stripe_subscription' => $user->subscription() ? [
                    'id' => $user->subscription()->id,
                    'status' => $user->subscription()->status,
                    'current_period_start' => $user->subscription()->current_period_start,
                    'current_period_end' => $user->subscription()->current_period_end,
                    'cancel_at_period_end' => $user->subscription()->cancel_at_period_end,
                ] : null,
                'permissions' => [
                    'can_upload_completion_images' => $user->canUploadCompletionImages(),
                    'can_create_stories' => $user->canCreateStories(),
                    'can_access_premium_content' => $user->canAccessPremiumContent(),
                    'can_create_custom_tasks' => $user->canCreateCustomTasks(),
                    'can_assign_couple_tasks' => $user->canAssignCoupleTasks(),
                    'can_receive_couple_tasks' => $user->canReceiveCoupleTasks(),
                ],
                'limits' => [
                    'max_tasks_per_day' => $user->getMaxTasksPerDay(),
                    'max_active_outcomes' => $user->getMaxActiveOutcomes(),
                    'remaining_outcome_slots' => $user->getRemainingOutcomeSlots(),
                ],
            ],
        ];
    }
}