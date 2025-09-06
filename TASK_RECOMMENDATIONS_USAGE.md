# Task Recommendations System

## Overview
Tasks can now have recommended rewards and punishments that users can quickly select from when creating tasks. This makes the system more user-friendly and provides curated suggestions.

## Database Structure

### Pivot Tables
- `task_recommended_rewards` - Links tasks to recommended rewards
- `task_recommended_punishments` - Links tasks to recommended punishments

Both tables include:
- `sort_order` - For ordering recommendations (most relevant first)
- Unique constraints to prevent duplicates
- Cascade deletes for data integrity

## Model Relationships

### Task Model
```php
// Get recommended rewards for a task
$task->recommendedRewards // Returns ordered collection

// Get recommended punishments for a task  
$task->recommendedPunishments // Returns ordered collection

// Attach recommendations
$task->recommendedRewards()->attach([
    $reward1->id => ['sort_order' => 1],
    $reward2->id => ['sort_order' => 2],
]);
```

### TaskReward & TaskPunishment Models
```php
// Get tasks that recommend this reward
$reward->recommendedForTasks // Returns collection

// Get tasks that recommend this punishment
$punishment->recommendedForTasks // Returns collection
```

## Usage Examples

### Creating a Task with Recommendations
```php
// Create a task
$task = Task::create([
    'title' => 'Wear your favorite outfit for 24 hours',
    'description' => 'Put on your most comfortable and favorite outfit...',
    'difficulty_level' => 3,
    'target_user_type' => TargetUserType::Any,
    'status' => ContentStatus::Approved,
    'user_id' => $user->id,
]);

// Add recommended rewards (ordered by relevance)
$task->recommendedRewards()->attach([
    $massageReward->id => ['sort_order' => 1],
    $dinnerReward->id => ['sort_order' => 2],
    $movieNightReward->id => ['sort_order' => 3],
]);

// Add recommended punishments (ordered by severity)
$task->recommendedPunishments()->attach([
    $choresPunishment->id => ['sort_order' => 1],
    $noPhonePunishment->id => ['sort_order' => 2],
    $earlyBedPunishment->id => ['sort_order' => 3],
]);
```

### Displaying Recommendations in UI
```php
// In Livewire component or controller
$task = Task::with(['recommendedRewards', 'recommendedPunishments'])->find($id);

// In Blade template
@foreach($task->recommendedRewards as $reward)
    <div class="recommended-reward">
        <h4>{{ $reward->title }}</h4>
        <p>{{ $reward->description }}</p>
        <span class="difficulty">Level {{ $reward->difficulty_level }}</span>
    </div>
@endforeach
```

### Quick Selection Interface
```php
// For task creation form
$availableRewards = TaskReward::where('status', ContentStatus::Approved)
    ->where('target_user_type', $user->user_type)
    ->orWhere('target_user_type', TargetUserType::Any)
    ->get();

$availablePunishments = TaskPunishment::where('status', ContentStatus::Approved)
    ->where('target_user_type', $user->user_type)
    ->orWhere('target_user_type', TargetUserType::Any)
    ->get();
```

### Smart Recommendations
```php
// Get recommendations based on task difficulty
$task = Task::find($id);

$recommendedRewards = $task->recommendedRewards()
    ->where('difficulty_level', '<=', $task->difficulty_level + 2)
    ->where('difficulty_level', '>=', $task->difficulty_level - 1)
    ->get();

$recommendedPunishments = $task->recommendedPunishments()
    ->where('difficulty_level', '<=', $task->difficulty_level + 2)
    ->where('difficulty_level', '>=', $task->difficulty_level - 1)
    ->get();
```

## Benefits

### For Users
- **Quick Setup**: Pre-selected recommendations for fast task creation
- **Curated Content**: Quality rewards/punishments suggested by the community
- **Difficulty Matching**: Recommendations match task difficulty levels
- **User Type Targeting**: Recommendations match user preferences

### For Content Creators
- **Popular Content**: Rewards/punishments that are frequently recommended get more visibility
- **Community Curation**: Users help curate the best content
- **Usage Analytics**: Track which recommendations are most popular

### For the Platform
- **Better UX**: Faster task creation process
- **Content Quality**: Community-driven content curation
- **Engagement**: Users more likely to complete tasks with good recommendations
- **Data Insights**: Understand what content works best together

## Future Enhancements

1. **AI Recommendations**: Use machine learning to suggest better matches
2. **User Preferences**: Learn from user selections to improve recommendations
3. **Seasonal Content**: Time-based recommendations (holidays, seasons)
4. **Difficulty Scaling**: Automatic difficulty adjustment based on user history
5. **Community Voting**: Let users vote on the best recommendations
6. **Bulk Operations**: Tools for admins to manage recommendations at scale

## Admin Interface

Admins can:
- View all task recommendations
- Reorder recommendations by drag-and-drop
- Add/remove recommendations from tasks
- See usage statistics for recommendations
- Bulk manage recommendations across multiple tasks
