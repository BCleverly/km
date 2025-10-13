<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Task;
use Illuminate\Http\JsonResponse;

class TaskAffiliateLinksController extends Controller
{
    /**
     * Get affiliate links for a specific task.
     */
    public function index(Task $task): JsonResponse
    {
        $affiliateLinks = $task->affiliateLinks()
            ->where('is_active', true)
            ->orderBy('task_affiliate_links.sort_order')
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'name' => $link->name,
                    'description' => $link->description,
                    'url' => $link->url,
                    'partner_type' => $link->partner_type,
                    'is_premium' => $link->is_premium,
                    'link_text' => $link->pivot->link_text,
                    'pivot_description' => $link->pivot->description,
                    'is_primary' => $link->pivot->is_primary,
                    'sort_order' => $link->pivot->sort_order,
                ];
            });

        return response()->json([
            'data' => $affiliateLinks,
            'meta' => [
                'total' => $affiliateLinks->count(),
                'has_primary' => $affiliateLinks->where('is_primary', true)->count() > 0,
            ],
        ]);
    }

    /**
     * Get the primary affiliate link for a specific task.
     */
    public function primary(Task $task): JsonResponse
    {
        $primaryLink = $task->primaryAffiliateLink()
            ->where('is_active', true)
            ->first();

        if (!$primaryLink) {
            return response()->json([
                'data' => null,
                'message' => 'No primary affiliate link found for this task.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $primaryLink->id,
                'name' => $primaryLink->name,
                'description' => $primaryLink->description,
                'url' => $primaryLink->url,
                'partner_type' => $primaryLink->partner_type,
                'is_premium' => $primaryLink->is_premium,
                'link_text' => $primaryLink->pivot->link_text,
                'pivot_description' => $primaryLink->pivot->description,
                'is_primary' => $primaryLink->pivot->is_primary,
                'sort_order' => $primaryLink->pivot->sort_order,
            ],
        ]);
    }
}