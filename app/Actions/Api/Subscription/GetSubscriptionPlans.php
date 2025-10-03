<?php

declare(strict_types=1);

namespace App\Actions\Api\Subscription;

use App\Enums\SubscriptionPlan;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetSubscriptionPlans
{
    use AsAction;

    public function handle(Request $request): array
    {
        $plans = collect(SubscriptionPlan::cases())->map(function ($plan) {
            return [
                'value' => $plan->value,
                'label' => $plan->label(),
                'description' => $plan->description(),
                'price' => $plan->price(),
                'price_formatted' => $plan->priceFormatted(),
                'is_recurring' => $plan->isRecurring(),
                'interval' => $plan->interval(),
                'features' => $plan->features(),
                'max_tasks_per_day' => $plan->maxTasksPerDay(),
                'can_create_stories' => $plan->canCreateStories(),
                'can_upload_images' => $plan->canUploadImages(),
                'can_access_premium_content' => $plan->canAccessPremiumContent(),
                'can_create_custom_tasks' => $plan->canCreateCustomTasks(),
                'is_couple_plan' => $plan->isCouplePlan(),
                'is_lifetime' => $plan->isLifetime(),
                'is_paid' => $plan->isPaid(),
            ];
        });

        return [
            'success' => true,
            'plans' => $plans,
        ];
    }
}