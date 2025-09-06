# Task Activity Tracking System

## Overview
The Task Activity Tracking System provides comprehensive logging and display of user interactions with tasks, rewards, and punishments. This system powers the "Recent Activity" widget on the Tasks Dashboard and provides detailed history for analytics and user engagement.

## Database Structure

### TaskActivity Model
- **Primary Table**: `task_activities`
- **Key Fields**:
  - `user_id` - The user who performed the activity
  - `task_id` - The task involved in the activity
  - `user_assigned_task_id` - Optional link to specific task assignment
  - `activity_type` - Type of activity (enum)
  - `title` - Human-readable activity title
  - `description` - Detailed description
  - `metadata` - JSON data for additional context
  - `activity_at` - When the activity occurred

### Activity Types
```php
enum TaskActivityType: string
{
    case Assigned = 'assigned';           // 📋 Task assigned to user
    case Completed = 'completed';         // ✅ Task completed successfully
    case Failed = 'failed';              // ❌ Task failed or abandoned
    case RewardReceived = 'reward_received';     // 🎁 User received a reward
    case PunishmentReceived = 'punishment_received'; // ⚡ User received punishment
    case TaskCreated = 'task_created';    // ➕ User created a new task
    case TaskViewed = 'task_viewed';      // 👁️ User viewed task details
}
```

## Usage Examples

### Logging Activities

#### Basic Activity Logging
```php
use App\Models\TaskActivity;
use App\TaskActivityType;

// Log when a task is assigned
TaskActivity::log(
    type: TaskActivityType::Assigned,
    user: $user,
    task: $task,
    assignedTask: $assignedTask
);

// Log when a task is completed
TaskActivity::log(
    type: TaskActivityType::Completed,
    user: $user,
    task: $task,
    assignedTask: $assignedTask
);
```

#### Custom Activity Logging
```php
// Log with custom title and description
TaskActivity::log(
    type: TaskActivityType::RewardReceived,
    user: $user,
    task: $task,
    assignedTask: $assignedTask,
    title: "Received amazing reward!",
    description: "You earned a 30-minute massage for completing the task.",
    metadata: [
        'reward_type' => 'physical',
        'value' => 8,
        'source' => 'task_completion'
    ]
);
```

### Retrieving Activities

#### User's Recent Activities
```php
// Get user's recent activities (for dashboard)
$recentActivities = $user->recentTaskActivities(10)->get();

// Get all user activities
$allActivities = $user->taskActivities()->get();

// Get activities for a specific task
$taskActivities = $user->taskActivities()
    ->where('task_id', $task->id)
    ->orderBy('activity_at', 'desc')
    ->get();
```

#### Filtering Activities
```php
// Get only completed tasks
$completedActivities = $user->taskActivities()
    ->where('activity_type', TaskActivityType::Completed)
    ->get();

// Get activities from last week
$recentWeekActivities = $user->taskActivities()
    ->where('activity_at', '>=', now()->subWeek())
    ->get();

// Get activities with specific metadata
$rewardActivities = $user->taskActivities()
    ->where('activity_type', TaskActivityType::RewardReceived)
    ->whereJsonContains('metadata->reward_type', 'physical')
    ->get();
```

### Dashboard Integration

#### Livewire Component
```php
// In Tasks/Dashboard.php
public function render()
{
    $recentActivities = auth()->user()->recentTaskActivities(5)->get();
    
    return view('livewire.tasks.dashboard', [
        'recentActivities' => $recentActivities,
    ]);
}
```

#### Blade Template
```blade
@foreach($recentActivities as $activity)
    <div class="activity-item">
        <div class="activity-icon {{ $activity->activity_type->color() }}">
            {{ $activity->activity_type->icon() }}
        </div>
        <div class="activity-content">
            <h4>{{ $activity->title }}</h4>
            <p>{{ $activity->description }}</p>
            <time>{{ $activity->activity_at->diffForHumans() }}</time>
        </div>
    </div>
@endforeach
```

## Integration Points

### Task Assignment Flow
```php
// When assigning a task to a user
$assignedTask = UserAssignedTask::create([
    'user_id' => $user->id,
    'task_id' => $task->id,
    'status' => TaskStatus::Assigned,
    'assigned_at' => now(),
]);

// Log the assignment
TaskActivity::log(
    type: TaskActivityType::Assigned,
    user: $user,
    task: $task,
    assignedTask: $assignedTask
);
```

### Task Completion Flow
```php
// When user completes a task
$assignedTask->update([
    'status' => TaskStatus::Completed,
    'completed_at' => now(),
    'outcome_type' => 'reward',
    'outcome_id' => $reward->id,
]);

// Log the completion
TaskActivity::log(
    type: TaskActivityType::Completed,
    user: $user,
    task: $task,
    assignedTask: $assignedTask
);

// Log the reward received
TaskActivity::log(
    type: TaskActivityType::RewardReceived,
    user: $user,
    task: $task,
    assignedTask: $assignedTask,
    metadata: [
        'reward_id' => $reward->id,
        'reward_title' => $reward->title,
        'missed_punishment_id' => $assignedTask->potential_punishment_id,
    ]
);
```

### Task Creation Flow
```php
// When user creates a new task
$task = Task::create([
    'title' => $title,
    'description' => $description,
    'user_id' => $user->id,
    // ... other fields
]);

// Log the creation
TaskActivity::log(
    type: TaskActivityType::TaskCreated,
    user: $user,
    task: $task,
    metadata: [
        'difficulty_level' => $task->difficulty_level,
        'target_user_type' => $task->target_user_type->value,
    ]
);
```

## Factory Usage

### Creating Test Data
```php
// Create random activities
TaskActivity::factory()->count(50)->create();

// Create activities for specific user
TaskActivity::factory()
    ->count(10)
    ->forUserAndTask($user, $task)
    ->create();

// Create specific activity types
TaskActivity::factory()
    ->ofType(TaskActivityType::Completed)
    ->count(5)
    ->create();

// Create activities for assigned tasks
TaskActivity::factory()
    ->forAssignedTask($assignedTask)
    ->ofType(TaskActivityType::Completed)
    ->create();
```

## Analytics and Insights

### User Engagement Metrics
```php
// Get user's activity summary
$userStats = [
    'total_activities' => $user->taskActivities()->count(),
    'tasks_completed' => $user->taskActivities()
        ->where('activity_type', TaskActivityType::Completed)
        ->count(),
    'rewards_received' => $user->taskActivities()
        ->where('activity_type', TaskActivityType::RewardReceived)
        ->count(),
    'tasks_created' => $user->taskActivities()
        ->where('activity_type', TaskActivityType::TaskCreated)
        ->count(),
];

// Get activity trends
$weeklyActivity = $user->taskActivities()
    ->where('activity_at', '>=', now()->subWeek())
    ->groupBy('activity_type')
    ->selectRaw('activity_type, count(*) as count')
    ->get();
```

### Platform Analytics
```php
// Most active users
$mostActiveUsers = User::withCount('taskActivities')
    ->orderBy('task_activities_count', 'desc')
    ->limit(10)
    ->get();

// Most popular activity types
$popularActivities = TaskActivity::selectRaw('activity_type, count(*) as count')
    ->groupBy('activity_type')
    ->orderBy('count', 'desc')
    ->get();

// Activity trends over time
$activityTrends = TaskActivity::selectRaw('DATE(activity_at) as date, count(*) as count')
    ->where('activity_at', '>=', now()->subMonth())
    ->groupBy('date')
    ->orderBy('date')
    ->get();
```

## Performance Considerations

### Indexing
The system includes optimized indexes for:
- `user_id + activity_at` - Fast user activity queries
- `task_id + activity_at` - Fast task activity queries
- `activity_type + activity_at` - Fast activity type filtering
- `activity_at` - Fast time-based queries

### Query Optimization
```php
// Use eager loading to prevent N+1 queries
$activities = TaskActivity::with(['user', 'task', 'userAssignedTask'])
    ->where('user_id', $user->id)
    ->orderBy('activity_at', 'desc')
    ->paginate(20);

// Use select to limit fields when possible
$activitySummaries = TaskActivity::select(['id', 'activity_type', 'title', 'activity_at'])
    ->where('user_id', $user->id)
    ->orderBy('activity_at', 'desc')
    ->get();
```

## Future Enhancements

1. **Real-time Updates**: WebSocket integration for live activity feeds
2. **Activity Notifications**: Push notifications for important activities
3. **Activity Filtering**: Advanced filtering by date, type, task, etc.
4. **Activity Export**: Export user activity history
5. **Activity Analytics**: Detailed charts and insights
6. **Activity Reactions**: Allow users to react to activities
7. **Activity Sharing**: Share interesting activities with others
8. **Activity Badges**: Achievement system based on activity patterns

## Best Practices

1. **Always Log Important Events**: Log task assignments, completions, rewards, and punishments
2. **Use Descriptive Titles**: Make activity titles clear and user-friendly
3. **Include Relevant Metadata**: Store additional context in the metadata field
4. **Optimize Queries**: Use eager loading and proper indexing
5. **Clean Up Old Data**: Consider archiving very old activities
6. **Privacy Considerations**: Ensure sensitive data isn't exposed in activity logs
7. **Performance Monitoring**: Monitor query performance for large activity datasets
