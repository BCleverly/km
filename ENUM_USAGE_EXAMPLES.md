# PHP Enum Usage Examples

## Overview
The application now uses PHP enum classes with integer backing values instead of database enums. This provides better flexibility, type safety, and maintainability.

## Enum Classes Created

### 1. TargetUserType
```php
enum TargetUserType: int
{
    case Male = 1;
    case Female = 2;
    case Couple = 3;
    case Any = 4;
}
```

### 2. ContentStatus
```php
enum ContentStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case InReview = 3;
    case Rejected = 4;
}
```

### 3. TaskStatus
```php
enum TaskStatus: int
{
    case Assigned = 1;
    case Completed = 2;
    case Failed = 3;
}
```

## Usage Examples

### Model Usage
```php
// Creating a task with enum values
$task = Task::create([
    'title' => 'Sample Task',
    'description' => 'A sample task description',
    'difficulty_level' => 5,
    'target_user_type' => TargetUserType::Male, // Enum instance
    'status' => ContentStatus::Pending, // Enum instance
    'user_id' => 1,
]);

// Accessing enum properties
echo $task->target_user_type->label(); // "Male"
echo $task->target_user_type->description(); // "For individual male users"
echo $task->status->color(); // "yellow"
echo $task->status->isVisible(); // false
```

### Query Usage
```php
// Query by enum value
$maleTasks = Task::where('target_user_type', TargetUserType::Male)->get();

// Query by enum integer value
$approvedTasks = Task::where('status', ContentStatus::Approved->value)->get();

// Query with enum methods
$visibleTasks = Task::where('status', ContentStatus::Approved)->get();
```

### Form Usage
```php
// In Livewire components or forms
public function rules(): array
{
    return [
        'target_user_type' => ['required', 'integer', 'in:' . implode(',', array_column(TargetUserType::cases(), 'value'))],
        'status' => ['required', 'integer', 'in:' . implode(',', array_column(ContentStatus::cases(), 'value'))],
    ];
}

// In Blade templates
<select name="target_user_type">
    @foreach(TargetUserType::cases() as $type)
        <option value="{{ $type->value }}">{{ $type->label() }}</option>
    @endforeach
</select>
```

### API Usage
```php
// API responses automatically serialize to integer values
return response()->json([
    'task' => $task, // target_user_type: 1, status: 2
]);

// API input validation
$request->validate([
    'target_user_type' => ['required', 'integer', 'in:1,2,3,4'],
    'status' => ['required', 'integer', 'in:1,2,3,4'],
]);
```

## Benefits

1. **Type Safety**: Compile-time checking of enum values
2. **Flexibility**: Easy to add new values without database migrations
3. **Rich Methods**: Each enum can have custom methods (label, color, etc.)
4. **IDE Support**: Full autocomplete and type hints
5. **Maintainability**: Centralized enum logic in PHP classes
6. **Performance**: Integer storage is more efficient than string enums
7. **Backward Compatibility**: Easy to migrate from database enums

## Migration Strategy

The migration automatically converts existing database enum values to integers:
- 'male' → 1
- 'female' → 2  
- 'couple' → 3
- 'any' → 4
- 'pending' → 1
- 'approved' → 2
- 'in_review' → 3
- 'rejected' → 4
- 'assigned' → 1
- 'completed' → 2
- 'failed' → 3

This ensures no data loss during the transition.
