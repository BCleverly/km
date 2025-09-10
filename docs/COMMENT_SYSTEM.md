# Comment System Documentation

A comprehensive comment system for Laravel applications built with Livewire that supports nested replies, markdown formatting, reactions, and real-time updates.

## Features

- **Nested Comments**: Support for up to 3 levels of nested replies
- **Markdown Support**: Full markdown formatting with live preview
- **Reactions**: Emoji-based reactions using the existing reaction system
- **Real-time Updates**: Livewire-powered real-time comment updates
- **Permissions**: Edit/delete permissions based on ownership or user roles
- **Polymorphic**: Can be attached to any model
- **Performance**: Individual comment components for optimal performance
- **Moderation**: Built-in approval system for comment moderation

## Installation

The comment system is already set up in this project. To use it:

1. **Run the migration** (if not already done):
   ```bash
   php artisan migrate
   ```

2. **Add the Commentable trait** to any model you want to support comments:
   ```php
   use App\Traits\Commentable;

   class Story extends Model
   {
       use Commentable;
   }
   ```

## Usage

### Basic Usage

To add comments to any page, simply include the comments list component:

```blade
<livewire:comments.comments-list :commentable="$model" />
```

### Advanced Usage

```blade
<livewire:comments.comments-list 
    :commentable="$model" 
    :per-page="20" 
    :show-form="true" />
```

### Parameters

- `commentable`: The model instance to attach comments to (required)
- `perPage`: Number of comments per page (default: 10)
- `showForm`: Whether to show the comment form (default: true)

## Components

### CommentsList
The main component that displays all comments and handles pagination.

### CommentItem
Individual comment component that handles:
- Displaying comment content with markdown rendering
- Edit/delete functionality
- Reply handling
- Reaction display
- Nested reply rendering

### CommentForm
Form component for creating new comments and replies with:
- Markdown formatting toolbar
- Live preview
- Validation
- Reply mode

## Database Structure

### Comments Table
```sql
- id (primary key)
- content (text)
- commentable_id (morph)
- commentable_type (morph)
- user_id (foreign key)
- parent_id (foreign key to comments)
- is_approved (boolean)
- approved_at (timestamp)
- approved_by (foreign key to users)
- created_at, updated_at, deleted_at
```

## API Methods

### Comment Model

```php
// Create a comment
$model->addComment('Comment content', $parentId, $userId);

// Get comments
$model->comments() // All comments
$model->approvedComments() // Only approved
$model->topLevelComments() // Only top-level (no replies)

// Check if has comments
$model->hasComments()

// Get comment count
$model->comments_count

// Get nested structure
$model->getNestedComments()
```

### Comment Instance

```php
// Check if reply
$comment->isReply()

// Get depth level
$comment->depth

// Get replies
$comment->replies()

// Approve comment
$comment->approve($approver)
```

## Permissions

The system supports permission-based editing and deletion:

- **Edit**: Users can edit their own comments or if they have the `edit comments` permission
- **Delete**: Users can delete their own comments or if they have the `delete comments` permission
- **Reply**: Authenticated users can reply (limited to 3 levels deep)

## Markdown Support

The comment form includes a formatting toolbar with buttons for:
- **Bold**: `**text**`
- **Italic**: `*text*`
- **Quote**: `> text`
- **Lists**: `- item` or `1. item`
- **Links**: `[text](url)`
- **Code**: `` `code` `` or ``` ```code``` ```

## Reactions

Comments support the same reaction system as other content:
- 👍 Like
- 👎 Dislike
- 😊 Blush
- 🍆 Eggplant
- ❤️ Heart
- 🤤 Drool

## Testing

Run the comment system tests:

```bash
php artisan test tests/Feature/Comments/
```

## Demo

Visit `/app/comments-demo` to see the comment system in action with a sample story.

## Customization

### Styling
The components use Tailwind CSS classes and can be customized by modifying the view files in `resources/views/livewire/comments/`.

### Validation Rules
Modify validation rules in the `CommentForm` component's `rules()` method.

### Permissions
Update permission checks in the `CommentItem` component's `canEdit()` and `canDelete()` methods.

### Markdown Rendering
The system uses Laravel's built-in `Str::markdown()` method. You can customize this by modifying the view files.

## Performance Considerations

- Each comment is its own Livewire component for optimal performance
- Comments are paginated to handle large numbers of comments
- Reactions are cached for better performance
- Nested replies are limited to 3 levels to prevent deep nesting

## Security

- All comment content is validated and sanitized
- CSRF protection is handled by Livewire
- Permission checks prevent unauthorized editing/deletion
- Soft deletes are used for comment removal