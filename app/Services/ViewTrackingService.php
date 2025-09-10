<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ViewTrackingService
{
    private const SESSION_PREFIX = 'viewed:';
    private const DAILY_LIMIT_PREFIX = 'daily_views:';
    private const DAILY_VIEWED_PREFIX = 'daily_viewed:';
    
    // Abuse prevention settings
    private const MAX_VIEWS_PER_SESSION = 3; // Max views per session per content
    private const MAX_DAILY_VIEWS_PER_USER = 100; // Max daily views per user
    private const VIEW_COOLDOWN_MINUTES = 5; // Cooldown between views of same content
    private const SESSION_DURATION_HOURS = 24; // Session duration for tracking

    /**
     * Track a view for a specific model
     */
    public function trackView(string $modelType, int $modelId, ?int $userId = null): bool
    {
        $sessionId = $this->getSessionId();
        $userKey = $userId ? "user:{$userId}" : "session:{$sessionId}";
        
        // Check if user/session has exceeded daily limit
        if (!$this->canViewToday($userKey)) {
            Log::info('View tracking blocked: Daily limit exceeded', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'user_key' => $userKey,
            ]);
            return false;
        }

        // Check if user/session has already viewed this content too many times
        if (!$this->canViewContent($userKey, $modelType, $modelId)) {
            Log::info('View tracking blocked: Session limit exceeded', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'user_key' => $userKey,
            ]);
            return false;
        }

        // Check if user has already viewed this content today
        if ($this->hasViewedToday($userKey, $modelType, $modelId)) {
            Log::info('View tracking blocked: Already viewed today', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'user_key' => $userKey,
            ]);
            return false;
        }

        // Check cooldown period
        if (!$this->isCooldownExpired($userKey, $modelType, $modelId)) {
            Log::info('View tracking blocked: Cooldown period active', [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'user_key' => $userKey,
            ]);
            return false;
        }

        // Record the view
        $this->recordView($userKey, $modelType, $modelId);
        
        // Increment view count in Redis
        $this->incrementViewCount($modelType, $modelId);
        
        // Update daily view count
        $this->incrementDailyViewCount($userKey);

        Log::info('View tracked successfully', [
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_key' => $userKey,
        ]);

        return true;
    }

    /**
     * Get view count for a specific model (database + current Redis)
     */
    public function getViewCount(string $modelType, int $modelId): int
    {
        // Get current Redis count
        $key = $this->getViewKey($modelType, $modelId);
        $redisCount = (int) Redis::get($key) ?? 0;
        
        // Get database count
        $databaseCount = $this->getDatabaseViewCount($modelType, $modelId);
        
        return $databaseCount + $redisCount;
    }

    /**
     * Get view counts for multiple models (database + current Redis)
     */
    public function getViewCounts(string $modelType, array $modelIds): array
    {
        if (empty($modelIds)) {
            return [];
        }

        $result = [];
        
        foreach ($modelIds as $modelId) {
            $result[$modelId] = $this->getViewCount($modelType, $modelId);
        }
        
        return $result;
    }

    /**
     * Get view count from database only
     */
    private function getDatabaseViewCount(string $modelType, int $modelId): int
    {
        $modelClass = $this->getModelClass($modelType);
        
        if ($modelClass) {
            $model = $modelClass::find($modelId);
            if ($model) {
                return (int) $model->view_count ?? 0;
            }
        }
        
        return 0;
    }

    /**
     * Get current Redis view count only (for debugging)
     */
    public function getRedisViewCount(string $modelType, int $modelId): int
    {
        $key = $this->getViewKey($modelType, $modelId);
        return (int) Redis::get($key) ?? 0;
    }

    /**
     * Sync view counts from Redis to database
     */
    public function syncViewCountsToDatabase(): int
    {
        $syncedCount = 0;
        $failedCount = 0;
        
        // Get all view keys from Redis (scoped by model type)
        $viewKeys = Redis::keys('*:views:*');
        
        foreach ($viewKeys as $key) {
            $viewCount = (int) Redis::get($key);
            
            if ($viewCount > 0) {
                // Parse the key to get model type and ID
                $parsed = $this->parseViewKey($key);
                
                if ($parsed) {
                    try {
                        $this->updateDatabaseViewCount($parsed['model_type'], $parsed['model_id'], $viewCount);
                        $syncedCount++;
                    } catch (\Exception $e) {
                        Log::error('Failed to sync view count to database', [
                            'key' => $key,
                            'model_type' => $parsed['model_type'],
                            'model_id' => $parsed['model_id'],
                            'view_count' => $viewCount,
                            'error' => $e->getMessage(),
                        ]);
                        $failedCount++;
                    }
                }
            }
        }

        // Only clear tracking data if sync was successful
        if ($failedCount === 0) {
            $this->clearViewTrackingData();
            
            Log::info('View counts synced to database successfully', [
                'synced_count' => $syncedCount,
                'total_keys' => count($viewKeys),
            ]);
        } else {
            Log::warning('View count sync completed with failures', [
                'synced_count' => $syncedCount,
                'failed_count' => $failedCount,
                'total_keys' => count($viewKeys),
            ]);
        }

        return $syncedCount;
    }

    /**
     * Check if user/session can view content today
     */
    private function canViewToday(string $userKey): bool
    {
        $dailyKey = self::DAILY_LIMIT_PREFIX . $userKey . ':' . Carbon::today()->format('Y-m-d');
        $dailyCount = (int) Redis::get($dailyKey) ?? 0;
        
        return $dailyCount < self::MAX_DAILY_VIEWS_PER_USER;
    }

    /**
     * Check if user/session can view specific content
     */
    private function canViewContent(string $userKey, string $modelType, int $modelId): bool
    {
        $sessionKey = self::SESSION_PREFIX . $userKey . ':' . $modelType . ':' . $modelId;
        $viewCount = (int) Redis::get($sessionKey) ?? 0;
        
        return $viewCount < self::MAX_VIEWS_PER_SESSION;
    }

    /**
     * Check if cooldown period has expired
     */
    private function isCooldownExpired(string $userKey, string $modelType, int $modelId): bool
    {
        $cooldownKey = 'cooldown:' . $userKey . ':' . $modelType . ':' . $modelId;
        $lastView = Redis::get($cooldownKey);
        
        if (!$lastView) {
            return true;
        }

        $lastViewTime = Carbon::createFromTimestamp($lastView);
        return $lastViewTime->addMinutes(self::VIEW_COOLDOWN_MINUTES)->isPast();
    }

    /**
     * Check if user has already viewed this content today
     */
    private function hasViewedToday(string $userKey, string $modelType, int $modelId): bool
    {
        $dailyViewedKey = self::DAILY_VIEWED_PREFIX . $userKey . ':' . $modelType . ':' . $modelId . ':' . Carbon::today()->format('Y-m-d');
        return (bool) Redis::exists($dailyViewedKey);
    }

    /**
     * Record a view for tracking
     */
    private function recordView(string $userKey, string $modelType, int $modelId): void
    {
        $sessionKey = self::SESSION_PREFIX . $userKey . ':' . $modelType . ':' . $modelId;
        $cooldownKey = 'cooldown:' . $userKey . ':' . $modelType . ':' . $modelId;
        $dailyViewedKey = self::DAILY_VIEWED_PREFIX . $userKey . ':' . $modelType . ':' . $modelId . ':' . Carbon::today()->format('Y-m-d');
        
        // Increment session view count
        Redis::incr($sessionKey);
        Redis::expire($sessionKey, self::SESSION_DURATION_HOURS * 3600);
        
        // Set cooldown
        Redis::setex($cooldownKey, self::VIEW_COOLDOWN_MINUTES * 60, time());
        
        // Mark as viewed today (expires at end of day + 1 day for safety)
        Redis::setex($dailyViewedKey, 2 * 24 * 3600, time());
    }

    /**
     * Increment view count in Redis
     */
    private function incrementViewCount(string $modelType, int $modelId): void
    {
        $key = $this->getViewKey($modelType, $modelId);
        Redis::incr($key);
        
        // Set expiration to 7 days (in case sync fails)
        Redis::expire($key, 7 * 24 * 3600);
    }

    /**
     * Increment daily view count
     */
    private function incrementDailyViewCount(string $userKey): void
    {
        $dailyKey = self::DAILY_LIMIT_PREFIX . $userKey . ':' . Carbon::today()->format('Y-m-d');
        Redis::incr($dailyKey);
        Redis::expire($dailyKey, 2 * 24 * 3600); // Expire after 2 days
    }

    /**
     * Get view key for Redis
     */
    private function getViewKey(string $modelType, int $modelId): string
    {
        return $modelType . ':views:' . $modelId;
    }

    /**
     * Parse view key to extract model type and ID
     */
    private function parseViewKey(string $key): ?array
    {
        // Pattern: {model_type}:views:{model_id}
        $pattern = '/^(.+):views:(\d+)$/';
        
        if (preg_match($pattern, $key, $matches)) {
            return [
                'model_type' => $matches[1],
                'model_id' => (int) $matches[2],
            ];
        }
        
        return null;
    }

    /**
     * Update view count in database
     */
    private function updateDatabaseViewCount(string $modelType, int $modelId, int $viewCount): void
    {
        $modelClass = $this->getModelClass($modelType);
        
        if ($modelClass) {
            $model = $modelClass::find($modelId);
            if ($model) {
                $model->increment('view_count', $viewCount);
            }
        }
    }

    /**
     * Get model class from type
     */
    private function getModelClass(string $modelType): ?string
    {
        return match ($modelType) {
            'fantasy', 'fantasies' => \App\Models\Fantasy::class,
            'story', 'stories' => \App\Models\Story::class,
            'task', 'tasks' => \App\Models\Tasks\Task::class,
            'outcome', 'outcomes' => \App\Models\Tasks\Outcome::class,
            default => null,
        };
    }

    /**
     * Clear all view tracking data (only after successful sync)
     */
    private function clearViewTrackingData(): void
    {
        // Clear view counts (scoped by model type) - these have been synced to database
        $viewKeys = Redis::keys('*:views:*');
        if (!empty($viewKeys)) {
            Redis::del($viewKeys);
        }

        // Clear session data - reset for new day
        $sessionKeys = Redis::keys(self::SESSION_PREFIX . '*');
        if (!empty($sessionKeys)) {
            Redis::del($sessionKeys);
        }

        // Clear cooldown data - reset for new day
        $cooldownKeys = Redis::keys('cooldown:*');
        if (!empty($cooldownKeys)) {
            Redis::del($cooldownKeys);
        }

        // Clear daily viewed data - reset for new day (only after successful sync)
        $dailyViewedKeys = Redis::keys(self::DAILY_VIEWED_PREFIX . '*');
        if (!empty($dailyViewedKeys)) {
            Redis::del($dailyViewedKeys);
        }

        // Clear daily view limit counters - reset for new day
        $dailyLimitKeys = Redis::keys(self::DAILY_LIMIT_PREFIX . '*');
        if (!empty($dailyLimitKeys)) {
            Redis::del($dailyLimitKeys);
        }

        Log::info('View tracking data cleared after successful sync', [
            'view_keys_cleared' => count($viewKeys),
            'session_keys_cleared' => count($sessionKeys),
            'cooldown_keys_cleared' => count($cooldownKeys),
            'daily_viewed_keys_cleared' => count($dailyViewedKeys),
            'daily_limit_keys_cleared' => count($dailyLimitKeys),
        ]);
    }

    /**
     * Get session ID for anonymous users
     */
    private function getSessionId(): string
    {
        return session()->getId();
    }

    /**
     * Get abuse prevention statistics
     */
    public function getAbuseStats(): array
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return [
            'max_views_per_session' => self::MAX_VIEWS_PER_SESSION,
            'max_daily_views_per_user' => self::MAX_DAILY_VIEWS_PER_USER,
            'view_cooldown_minutes' => self::VIEW_COOLDOWN_MINUTES,
            'session_duration_hours' => self::SESSION_DURATION_HOURS,
            'total_view_keys' => count(Redis::keys('*:views:*')),
            'total_session_keys' => count(Redis::keys(self::SESSION_PREFIX . '*')),
            'total_daily_limit_keys' => count(Redis::keys(self::DAILY_LIMIT_PREFIX . '*')),
            'total_daily_viewed_keys' => count(Redis::keys(self::DAILY_VIEWED_PREFIX . '*')),
        ];
    }
}