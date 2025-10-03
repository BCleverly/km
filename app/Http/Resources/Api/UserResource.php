<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $this->user_type->value,
            'user_type_label' => $this->user_type->label(),
            'subscription_plan' => $this->subscription_plan->value,
            'subscription_plan_label' => $this->subscription_plan->label(),
            'subscription_status' => $this->subscription_status,
            'has_active_subscription' => $this->hasActiveSubscription(),
            'has_paid_subscription' => $this->hasPaidSubscription(),
            'is_on_trial' => $this->isOnTrial(),
            'trial_ends_at' => $this->trial_ends_at,
            'profile' => $this->when($this->relationLoaded('profile'), function () {
                return $this->profile ? [
                    'username' => $this->profile->username,
                    'about' => $this->profile->about,
                    'bdsm_role' => $this->profile->bdsm_role?->value,
                    'bdsm_role_label' => $this->profile->bdsm_role_label,
                    'bdsm_role_description' => $this->profile->bdsm_role_description,
                    'profile_picture_url' => $this->profile_picture_url,
                    'cover_photo_url' => $this->cover_photo_url,
                ] : null;
            }),
            'permissions' => [
                'can_upload_completion_images' => $this->canUploadCompletionImages(),
                'can_create_stories' => $this->canCreateStories(),
                'can_access_premium_content' => $this->canAccessPremiumContent(),
                'can_create_custom_tasks' => $this->canCreateCustomTasks(),
                'can_assign_couple_tasks' => $this->canAssignCoupleTasks(),
                'can_receive_couple_tasks' => $this->canReceiveCoupleTasks(),
                'can_send_partner_invitations' => $this->canSendPartnerInvitations(),
            ],
            'limits' => [
                'max_tasks_per_day' => $this->getMaxTasksPerDay(),
                'max_active_outcomes' => $this->getMaxActiveOutcomes(),
                'remaining_outcome_slots' => $this->getRemainingOutcomeSlots(),
            ],
        ];
    }
}