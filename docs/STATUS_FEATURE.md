# Status Feature Documentation

## Overview

The Status feature allows users to publish short text updates (280 characters by default) to their profile, similar to Facebook status updates. Other users can react to these statuses, and the comment system is prepared but currently disabled.

## Features

### Core Functionality
- **Status Creation**: Users can create status updates up to 280 characters (configurable)
- **Public/Private**: Statuses can be marked as public or private
- **Daily Limits**: Users are limited to 10 statuses per day (configurable)
- **Reactions**: Users can react to statuses using the existing reaction system
- **Comments**: Comment system is prepared but disabled (as requested)

### User Interface
- **Status List**: Displays recent statuses on user profiles
- **Create Form**: Toggle-able form for creating new statuses
- **Character Counter**: Shows current character count and remaining characters
- **Time Display**: Shows relative time (e.g., "2 hours ago")
- **Delete Functionality**: Users can delete their own statuses

## Configuration

The status system can be configured in `config/app.php`:

```php
'statuses' => [
    'max_length' => env('STATUS_MAX_LENGTH', 280),
    'max_per_user_per_day' => env('STATUS_MAX_PER_USER_PER_DAY', 10),
],
```

Environment variables:
- `STATUS_MAX_LENGTH`: Maximum characters per status (default: 280)
- `STATUS_MAX_PER_USER_PER_DAY`: Maximum statuses per user per day (default: 10)

## Database Schema

### Statuses Table
```sql
CREATE TABLE statuses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_public_created (is_public, created_at)
);
```

## Models

### Status Model
- **Relationships**: Belongs to User, has many Comments
- **Scopes**: `public()`, `forUser()`, `recent()`
- **Methods**: `isWithinLimit()`, `getMaxLength()`
- **Traits**: `Reactable`, `LogsActivity`, `SoftDeletes`

### User Model Extensions
- **Relationships**: `statuses()` - has many Status
- **Methods**: `hasReachedDailyStatusLimit()`, `getTodayStatusCount()`

## Livewire Components

### CreateStatus
- Handles status creation form
- Validates content length and daily limits
- Provides character counting
- Shows success/error notifications

### StatusList
- Displays list of statuses for a user
- Shows create form toggle for authenticated users
- Handles daily limit display
- Manages status creation and display

### StatusItem
- Displays individual status
- Shows user info, content, and timestamp
- Handles status deletion
- Integrates with reaction system

## Testing

The feature includes comprehensive tests:

### Feature Tests
- Status creation and validation
- Daily limit enforcement
- User authorization
- Character counting
- Public/private visibility

### Unit Tests
- Model relationships and scopes
- Character limit validation
- Soft delete functionality
- Configuration handling

### Integration Tests
- End-to-end status creation and display
- User profile integration
- Daily limit enforcement
- Status deletion

## Usage Examples

### Creating a Status
```php
$user = User::find(1);
$status = Status::create([
    'user_id' => $user->id,
    'content' => 'Hello, world!',
    'is_public' => true,
]);
```

### Getting User's Statuses
```php
$user = User::find(1);
$publicStatuses = $user->statuses()->public()->recent(10)->get();
```

### Checking Daily Limit
```php
$user = User::find(1);
if ($user->hasReachedDailyStatusLimit()) {
    // Handle limit reached
}
```

## Future Enhancements

The comment system is already prepared and can be easily enabled by:
1. Removing the `disabled` attribute from the comments button
2. Adding the comment form and list components
3. Updating the StatusItem component to handle comment display

Other potential enhancements:
- Status editing
- Status sharing
- Status search
- Status categories/tags
- Media attachments
- Status scheduling