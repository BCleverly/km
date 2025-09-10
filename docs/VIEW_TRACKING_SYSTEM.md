# View Tracking System

## Overview

The view tracking system provides robust, abuse-resistant view counting for content (fantasies, stories, tasks, outcomes) using Redis for real-time tracking and daily synchronization to the database.

## Key Features

### Abuse Prevention
- **Session Limits**: Maximum 3 views per content per session
- **Daily Limits**: Maximum 100 views per user per day
- **Cooldown Period**: 5-minute cooldown between views of the same content
- **Session Duration**: 24-hour session tracking

### Performance
- **Redis-based**: Real-time view tracking with Redis
- **Daily Sync**: View counts synced to database daily at midnight
- **Batch Operations**: Efficient bulk view count retrieval
- **Automatic Cleanup**: Redis data cleared after sync

## Architecture

### Components

1. **ViewTrackingService** (`app/Services/ViewTrackingService.php`)
   - Core service handling view tracking logic
   - Abuse prevention algorithms
   - Redis operations

2. **SyncViewCountsJob** (`app/Jobs/SyncViewCountsJob.php`)
   - Queued job for syncing view counts
   - Error handling and retry logic

3. **SyncViewCountsCommand** (`app/Console/Commands/SyncViewCountsCommand.php`)
   - Artisan command for manual sync
   - Statistics and monitoring

### Redis Keys Structure

```
views:{model_type}:{model_id}           # View count for specific content
viewed:{user_key}:{model_type}:{model_id}  # Session view tracking
daily_views:{user_key}:{date}           # Daily view limits
cooldown:{user_key}:{model_type}:{model_id}  # Cooldown tracking
```

## Usage

### Tracking Views

```php
use App\Services\ViewTrackingService;

$service = app(ViewTrackingService::class);
$userId = auth()->id();

// Track a view
$success = $service->trackView('story', $storyId, $userId);

if ($success) {
    // View was tracked successfully
} else {
    // View was blocked due to abuse prevention
}
```

### Getting View Counts

```php
// Get single view count
$viewCount = $service->getViewCount('story', $storyId);

// Get multiple view counts
$viewCounts = $service->getViewCounts('story', [$storyId1, $storyId2, $storyId3]);
```

### In Models

```php
// Fantasy model
public function getViewCount(): int
{
    return app(ViewTrackingService::class)->getViewCount('fantasy', $this->id);
}

// Story model
public function getViewCount(): int
{
    return app(ViewTrackingService::class)->getViewCount('story', $this->id);
}
```

## Configuration

### Abuse Prevention Settings

```php
// In ViewTrackingService
private const MAX_VIEWS_PER_SESSION = 3;        // Max views per content per session
private const MAX_DAILY_VIEWS_PER_USER = 100;   // Max daily views per user
private const VIEW_COOLDOWN_MINUTES = 5;        // Cooldown between views
private const SESSION_DURATION_HOURS = 24;      // Session duration
```

### Scheduled Tasks

```php
// In bootstrap/app.php
$schedule->command('views:sync')->dailyAt('00:00');
```

## Commands

### Manual Sync

```bash
# Sync view counts manually
php artisan views:sync

# Force sync even if no views pending
php artisan views:sync --force

# Show abuse prevention statistics
php artisan views:sync --stats
```

### Queue Processing

```bash
# Process the sync job
php artisan queue:work
```

## Monitoring

### Statistics

```php
$service = app(ViewTrackingService::class);
$stats = $service->getAbuseStats();

// Returns:
// - max_views_per_session
// - max_daily_views_per_user
// - view_cooldown_minutes
// - session_duration_hours
// - total_view_keys
// - total_session_keys
// - total_daily_limit_keys
```

### Logging

The system logs all view tracking activities:

- Successful view tracking
- Blocked views (with reasons)
- Sync operations
- Errors and failures

## Database Schema

### View Count Columns

```sql
-- fantasies table
ALTER TABLE fantasies ADD COLUMN view_count INT DEFAULT 0;

-- stories table  
ALTER TABLE stories ADD COLUMN view_count INT DEFAULT 0;
```

## Testing

### Unit Tests

```php
// Test view tracking
it('can track views for fantasies', function () {
    $service = app(ViewTrackingService::class);
    $result = $service->trackView('fantasy', $fantasyId, $userId);
    expect($result)->toBeTrue();
});

// Test abuse prevention
it('prevents abuse by limiting views per session', function () {
    // Test implementation
});
```

### Test Setup

```php
beforeEach(function () {
    // Clear Redis before each test
    Redis::flushdb();
});
```

## Error Handling

### Job Failures

- Automatic retry (3 attempts)
- Comprehensive logging
- Failure notifications

### Redis Failures

- Graceful degradation
- Fallback to database-only tracking
- Error logging

## Performance Considerations

### Redis Memory Usage

- Keys expire automatically
- Daily cleanup after sync
- Efficient key structure

### Database Impact

- Batch updates during sync
- Minimal impact on read operations
- Indexed view_count columns

## Security

### Abuse Prevention

- Multiple layers of protection
- Configurable limits
- Session-based tracking
- Cooldown periods

### Data Privacy

- No personal data stored in Redis
- Session-based anonymous tracking
- Automatic data cleanup

## Maintenance

### Daily Operations

- Automatic sync at midnight
- Redis cleanup
- Error monitoring

### Manual Operations

- Force sync when needed
- Statistics monitoring
- Redis memory monitoring

## Troubleshooting

### Common Issues

1. **Views not tracking**
   - Check Redis connection
   - Verify abuse prevention limits
   - Check logs for errors

2. **Sync failures**
   - Check queue worker status
   - Verify database connection
   - Check job logs

3. **High Redis memory usage**
   - Check for stuck keys
   - Verify cleanup process
   - Monitor key expiration

### Debug Commands

```bash
# Check Redis keys
redis-cli keys "views:*"

# Check abuse stats
php artisan views:sync --stats

# Force sync
php artisan views:sync --force
```