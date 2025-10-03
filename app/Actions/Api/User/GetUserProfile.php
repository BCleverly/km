<?php

declare(strict_types=1);

namespace App\Actions\Api\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetUserProfile
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user()->load('profile');

        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type->value,
                'user_type_label' => $user->user_type->label(),
                'subscription_plan' => $user->subscription_plan->value,
                'subscription_plan_label' => $user->subscription_plan->label(),
                'subscription_status' => $user->subscription_status,
                'has_active_subscription' => $user->hasActiveSubscription(),
                'has_paid_subscription' => $user->hasPaidSubscription(),
                'is_on_trial' => $user->isOnTrial(),
                'trial_ends_at' => $user->trial_ends_at,
                'profile' => $user->profile ? [
                    'username' => $user->profile->username,
                    'about' => $user->profile->about,
                    'bdsm_role' => $user->profile->bdsm_role?->value,
                    'bdsm_role_label' => $user->profile->bdsm_role_label,
                    'bdsm_role_description' => $user->profile->bdsm_role_description,
                    'profile_picture_url' => $user->profile_picture_url,
                    'cover_photo_url' => $user->cover_photo_url,
                ] : null,
                'permissions' => [
                    'can_upload_completion_images' => $user->canUploadCompletionImages(),
                    'can_create_stories' => $user->canCreateStories(),
                    'can_access_premium_content' => $user->canAccessPremiumContent(),
                    'can_create_custom_tasks' => $user->canCreateCustomTasks(),
                    'can_assign_couple_tasks' => $user->canAssignCoupleTasks(),
                    'can_receive_couple_tasks' => $user->canReceiveCoupleTasks(),
                    'can_send_partner_invitations' => $user->canSendPartnerInvitations(),
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