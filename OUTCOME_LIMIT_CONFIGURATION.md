# Outcome Limit Configuration

## Overview

The system now limits users to a maximum number of active outcomes (rewards and punishments combined) at any time. This prevents users from accumulating too many active outcomes and encourages them to complete or let outcomes expire.

## Default Configuration

- **Default Limit**: 2 active outcomes per user
- **Configurable**: Yes, via environment variable or config file

## Configuration Methods

### Method 1: Environment Variable (Recommended)

Add this line to your `.env` file:

```env
MAX_ACTIVE_OUTCOMES=2
```

### Method 2: Artisan Command

Use the provided command to easily update the limit:

```bash
php artisan outcomes:limit 3
```

This command will:
- Update your `.env` file
- Validate the new limit (must be between 1-10)
- Ask for confirmation before making changes

### Method 3: Direct Config File

Edit `config/app.php` and modify the `tasks.max_active_outcomes` value:

```php
'tasks' => [
    'max_active_outcomes' => 3, // Change this value
],
```

## How It Works

### Automatic Management

1. **Expired Cleanup**: The system automatically marks expired outcomes as expired
2. **Limit Enforcement**: When a user reaches their limit, the oldest active outcome is automatically replaced
3. **Real-time Updates**: The dashboard shows current usage and remaining slots

### User Experience

- **Visual Indicators**: Dashboard shows "2/2 active" with progress dots
- **Warning Messages**: Users see warnings when approaching or at their limit
- **Automatic Replacement**: Oldest outcomes are replaced when limit is reached

### Dashboard Features

- **Outcome Counter**: Shows current active outcomes vs. maximum allowed
- **Progress Dots**: Visual indicator of how many slots are used
- **Warning Banner**: Appears when user is at or near their limit
- **Completion Buttons**: Users can manually complete outcomes to free up slots

## Testing the System

### Current Test Results

- ✅ **Limit Enforcement**: Users cannot exceed their configured limit
- ✅ **Automatic Replacement**: Oldest outcomes are replaced when limit is reached
- ✅ **Expired Cleanup**: Expired outcomes are automatically cleaned up
- ✅ **Dashboard Display**: All limit information is properly displayed
- ✅ **User Methods**: All helper methods work correctly

### Test Commands

```bash
# Test the current limit
php artisan tinker
>>> $user = App\Models\User::first();
>>> $user->getMaxActiveOutcomes();
>>> $user->getActiveOutcomeCount();
>>> $user->hasReachedOutcomeLimit();
```

## Future Enhancements

The system is designed to be easily extensible:

1. **Per-User Limits**: Could be extended to have different limits per user role
2. **Temporary Boosts**: Could add temporary limit increases as rewards
3. **Premium Features**: Higher limits could be a premium feature
4. **Time-Based Limits**: Could have different limits based on time periods

## Configuration Examples

### Conservative (Default)
```env
MAX_ACTIVE_OUTCOMES=2
```

### Moderate
```env
MAX_ACTIVE_OUTCOMES=3
```

### Generous
```env
MAX_ACTIVE_OUTCOMES=5
```

### Maximum (Performance Limit)
```env
MAX_ACTIVE_OUTCOMES=10
```

## Performance Considerations

- The system is optimized for limits up to 10 active outcomes per user
- Higher limits may impact dashboard loading times
- Database queries are optimized with proper indexing
- Automatic cleanup prevents database bloat

## Troubleshooting

### Common Issues

1. **Limit Not Updating**: Restart your application after changing the environment variable
2. **Outcomes Not Replacing**: Check that the `cleanupExpiredOutcomes()` method is being called
3. **Dashboard Not Showing Limits**: Ensure the Livewire component is passing the limit variables

### Debug Commands

```bash
# Check current configuration
php artisan tinker
>>> config('app.tasks.max_active_outcomes');

# Check user's current state
>>> $user = App\Models\User::first();
>>> $user->getActiveOutcomeCount();
>>> $user->getRemainingOutcomeSlots();
```
