# Kink Master API Documentation

## Overview

The Kink Master API provides comprehensive access to all platform features for mobile applications. The API follows RESTful conventions and uses JSON for data exchange.

**Base URL:** `https://your-domain.com/api/v1`

## Authentication

The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Response Format

All API responses follow this structure:

```json
{
  "success": true,
  "message": "Optional message",
  "data": { ... }
}
```

Error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

## Endpoints

### Authentication

#### Register User
**POST** `/auth/register`

Register a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "user_type": 1,
  "username": "johndoe",
  "bdsm_role": 1,
  "about": "About me..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "user": { ... },
  "token": "1|abc123..."
}
```

#### Login User
**POST** `/auth/login`

Authenticate a user and return an access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user": { ... },
  "token": "1|abc123..."
}
```

#### Logout User
**POST** `/auth/logout`

Revoke the current access token.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### Get User Profile
**GET** `/auth/user`

Get the authenticated user's profile information.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_type": 1,
    "user_type_label": "Male",
    "subscription_plan": 2,
    "subscription_plan_label": "Premium",
    "subscription_status": "Premium",
    "has_active_subscription": true,
    "has_paid_subscription": true,
    "is_on_trial": false,
    "profile": {
      "username": "johndoe",
      "about": "About me...",
      "bdsm_role": 1,
      "bdsm_role_label": "Dominant",
      "profile_picture_url": "https://...",
      "cover_photo_url": "https://..."
    },
    "permissions": {
      "can_upload_completion_images": true,
      "can_create_stories": true,
      "can_access_premium_content": true,
      "can_create_custom_tasks": true,
      "can_assign_couple_tasks": false,
      "can_receive_couple_tasks": false,
      "can_send_partner_invitations": false
    },
    "limits": {
      "max_tasks_per_day": null,
      "max_active_outcomes": 2,
      "remaining_outcome_slots": 1
    }
  }
}
```

### User Profile

#### Update User Profile
**PUT** `/user/profile`

Update the authenticated user's profile.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "name": "John Doe",
  "username": "johndoe",
  "about": "Updated about me...",
  "bdsm_role": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "user": { ... }
}
```

### Tasks

#### Get User Tasks
**GET** `/tasks`

Get the authenticated user's assigned tasks.

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional): Filter by status (`assigned`, `completed`, `failed`, `all`)
- `limit` (optional): Number of results (default: 20)
- `offset` (optional): Offset for pagination (default: 0)

**Response:**
```json
{
  "success": true,
  "tasks": [
    {
      "id": 1,
      "status": 2,
      "status_label": "Completed",
      "assigned_at": "2024-01-01T10:00:00Z",
      "completed_at": "2024-01-01T12:00:00Z",
      "deadline": "2024-01-01T18:00:00Z",
      "has_completion_image": true,
      "completion_note": "Task completed successfully",
      "task": {
        "id": 1,
        "title": "Morning Exercise",
        "description": "Complete 30 minutes of exercise",
        "difficulty_level": 3,
        "duration_display": "2 hours",
        "target_user_type": 1,
        "target_user_type_label": "Male",
        "is_premium": false,
        "view_count": 150,
        "author": {
          "id": 2,
          "name": "Jane Smith",
          "username": "janesmith"
        }
      },
      "potential_reward": {
        "id": 1,
        "title": "Relaxation Time",
        "description": "30 minutes of relaxation",
        "difficulty_level": 3
      }
    }
  ],
  "pagination": {
    "limit": 20,
    "offset": 0,
    "has_more": true
  }
}
```

#### Get Active Task
**GET** `/tasks/active`

Get the user's currently active task.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "has_active_task": true,
  "task": {
    "id": 2,
    "status": 1,
    "status_label": "Assigned",
    "assigned_at": "2024-01-02T10:00:00Z",
    "deadline": "2024-01-02T18:00:00Z",
    "task": {
      "id": 2,
      "title": "Evening Meditation",
      "description": "Practice 15 minutes of meditation",
      "difficulty_level": 2,
      "duration_display": "1 hour",
      "target_user_type": 1,
      "target_user_type_label": "Male",
      "is_premium": false,
      "view_count": 75,
      "author": {
        "id": 3,
        "name": "Bob Wilson",
        "username": "bobwilson"
      }
    },
    "potential_reward": {
      "id": 2,
      "title": "Extra Screen Time",
      "description": "1 hour of extra screen time",
      "difficulty_level": 2
    },
    "potential_punishment": {
      "id": 1,
      "title": "No Dessert",
      "description": "No dessert for 1 day",
      "difficulty_level": 2
    }
  }
}
```

#### Complete Task
**POST** `/tasks/complete`

Complete the user's active task.

**Headers:** `Authorization: Bearer {token}`

**Request Body (multipart/form-data):**
```
completion_note: "Task completed successfully"
completion_image: [file] (optional, premium feature)
```

**Response:**
```json
{
  "success": true,
  "message": "Task completed! You earned a reward: Extra Screen Time",
  "task": {
    "id": 2,
    "status": 2,
    "status_label": "Completed",
    "completed_at": "2024-01-02T14:00:00Z",
    "has_completion_image": true,
    "completion_note": "Task completed successfully",
    "task": {
      "id": 2,
      "title": "Evening Meditation",
      "description": "Practice 15 minutes of meditation"
    },
    "outcome": {
      "id": 2,
      "title": "Extra Screen Time",
      "description": "1 hour of extra screen time",
      "type": "reward"
    }
  }
}
```

#### Get Task Statistics
**GET** `/tasks/stats`

Get the user's task statistics and streaks.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "stats": {
    "summary": {
      "total_assigned": 25,
      "completed": 20,
      "failed": 3,
      "active": 2,
      "completion_rate": 80.0,
      "current_streak": 5,
      "longest_streak": 12
    },
    "streaks": {
      "current_streak": 5,
      "longest_streak": 12,
      "total_completed_tasks": 20,
      "completion_rate": 80.0
    },
    "active_outcomes": {
      "count": 1,
      "max_allowed": 2,
      "remaining_slots": 1
    },
    "daily_limits": {
      "max_tasks_per_day": null,
      "tasks_today": 1,
      "has_reached_daily_limit": false
    }
  }
}
```

### Content

#### Get Stories
**GET** `/content/stories`

Get published stories.

**Query Parameters:**
- `limit` (optional): Number of results (default: 20)
- `offset` (optional): Offset for pagination (default: 0)
- `search` (optional): Search query

**Response:**
```json
{
  "success": true,
  "stories": [
    {
      "id": 1,
      "title": "My First Experience",
      "slug": "my-first-experience",
      "summary": "A story about my first BDSM experience...",
      "word_count": 1500,
      "reading_time_minutes": 8,
      "view_count": 250,
      "created_at": "2024-01-01T10:00:00Z",
      "author": {
        "id": 2,
        "name": "Jane Smith",
        "username": "janesmith"
      },
      "tags": [
        {
          "id": 1,
          "name": "BDSM",
          "slug": "bdsm"
        }
      ],
      "reactions": {
        "count": 15,
        "user_reacted": false
      }
    }
  ],
  "pagination": {
    "limit": 20,
    "offset": 0,
    "has_more": true
  }
}
```

#### Get Story
**GET** `/content/stories/{slug}`

Get a specific story by slug.

**Response:**
```json
{
  "success": true,
  "story": {
    "id": 1,
    "title": "My First Experience",
    "slug": "my-first-experience",
    "summary": "A story about my first BDSM experience...",
    "content": "Full story content here...",
    "word_count": 1500,
    "reading_time_minutes": 8,
    "view_count": 251,
    "created_at": "2024-01-01T10:00:00Z",
    "author": {
      "id": 2,
      "name": "Jane Smith",
      "username": "janesmith"
    },
    "tags": [...],
    "reactions": {
      "count": 15,
      "user_reacted": false
    },
    "comments": [
      {
        "id": 1,
        "content": "Great story!",
        "created_at": "2024-01-01T12:00:00Z",
        "user": {
          "id": 3,
          "name": "Bob Wilson",
          "username": "bobwilson"
        }
      }
    ]
  }
}
```

#### Create Story
**POST** `/content/stories`

Create a new story (requires subscription).

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "title": "My New Story",
  "summary": "A brief summary of the story...",
  "content": "Full story content here...",
  "tags": ["BDSM", "First Time"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Story created successfully and submitted for review",
  "story": {
    "id": 3,
    "title": "My New Story",
    "slug": "my-new-story",
    "summary": "A brief summary of the story...",
    "status": 1,
    "status_label": "Pending Review",
    "created_at": "2024-01-02T10:00:00Z"
  }
}
```

#### Get Statuses
**GET** `/content/statuses`

Get public status updates.

**Query Parameters:**
- `limit` (optional): Number of results (default: 20)
- `offset` (optional): Offset for pagination (default: 0)
- `user_id` (optional): Filter by specific user

**Response:**
```json
{
  "success": true,
  "statuses": [
    {
      "id": 1,
      "content": "Just completed my morning task! Feeling accomplished!",
      "is_public": true,
      "has_image": true,
      "status_image_url": "https://...",
      "created_at": "2024-01-01T10:00:00Z",
      "user": {
        "id": 2,
        "name": "Jane Smith",
        "username": "janesmith",
        "profile_picture_url": "https://..."
      },
      "reactions": {
        "count": 8,
        "user_reacted": false
      },
      "comments": {
        "count": 3
      }
    }
  ],
  "pagination": {
    "limit": 20,
    "offset": 0,
    "has_more": true
  }
}
```

#### Create Status
**POST** `/content/statuses`

Create a new status update.

**Headers:** `Authorization: Bearer {token}`

**Request Body (multipart/form-data):**
```
content: "Just completed my task!"
is_public: true
status_image: [file] (optional)
```

**Response:**
```json
{
  "success": true,
  "message": "Status created successfully",
  "status": {
    "id": 2,
    "content": "Just completed my task!",
    "is_public": true,
    "has_image": false,
    "status_image_url": null,
    "created_at": "2024-01-02T10:00:00Z"
  }
}
```

#### Get Fantasies
**GET** `/content/fantasies`

Get published fantasies.

**Query Parameters:**
- `limit` (optional): Number of results (default: 20)
- `offset` (optional): Offset for pagination (default: 0)
- `search` (optional): Search query

**Response:**
```json
{
  "success": true,
  "fantasies": [
    {
      "id": 1,
      "content": "I've always fantasized about...",
      "word_count": 500,
      "is_premium": false,
      "is_anonymous": false,
      "view_count": 100,
      "created_at": "2024-01-01T10:00:00Z",
      "author": {
        "id": 2,
        "name": "Jane Smith",
        "username": "janesmith"
      },
      "tags": [...],
      "reactions": {
        "count": 5,
        "user_reacted": false
      }
    }
  ],
  "pagination": {
    "limit": 20,
    "offset": 0,
    "has_more": true
  }
}
```

#### Create Fantasy
**POST** `/content/fantasies`

Create a new fantasy.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "content": "I've always fantasized about...",
  "is_premium": false,
  "is_anonymous": false,
  "tags": ["BDSM", "Fantasy"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Fantasy created successfully and submitted for review",
  "fantasy": {
    "id": 2,
    "content": "I've always fantasized about...",
    "is_premium": false,
    "is_anonymous": false,
    "status": 1,
    "status_label": "Pending",
    "created_at": "2024-01-02T10:00:00Z"
  }
}
```

### Subscription

#### Get Subscription Plans
**GET** `/subscription/plans`

Get all available subscription plans.

**Response:**
```json
{
  "success": true,
  "plans": [
    {
      "value": 0,
      "label": "Free",
      "description": "Limited access with 1 task per day",
      "price": 0,
      "price_formatted": "£0.00",
      "is_recurring": false,
      "interval": null,
      "features": [
        "1 task per day",
        "Basic rewards and punishments",
        "Community content viewing"
      ],
      "max_tasks_per_day": 1,
      "can_create_stories": false,
      "can_upload_images": false,
      "can_access_premium_content": false,
      "can_create_custom_tasks": false,
      "is_couple_plan": false,
      "is_lifetime": false,
      "is_paid": false
    },
    {
      "value": 2,
      "label": "Premium",
      "description": "Full access with premium features",
      "price": 299,
      "price_formatted": "£2.99",
      "is_recurring": true,
      "interval": "month",
      "features": [
        "Everything in Solo",
        "Premium content access",
        "Advanced analytics",
        "Custom task creation",
        "Image uploads"
      ],
      "max_tasks_per_day": null,
      "can_create_stories": true,
      "can_upload_images": true,
      "can_access_premium_content": true,
      "can_create_custom_tasks": true,
      "is_couple_plan": false,
      "is_lifetime": false,
      "is_paid": true
    }
  ]
}
```

#### Get User Subscription
**GET** `/subscription/current`

Get the authenticated user's current subscription.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "subscription": {
    "current_plan": {
      "value": 2,
      "label": "Premium",
      "description": "Full access with premium features"
    },
    "status": "Premium",
    "has_active_subscription": true,
    "has_paid_subscription": true,
    "is_on_trial": false,
    "trial_ends_at": null,
    "needs_subscription_choice": false,
    "stripe_subscription": {
      "id": "sub_1234567890",
      "status": "active",
      "current_period_start": 1704067200,
      "current_period_end": 1706745600,
      "cancel_at_period_end": false
    },
    "permissions": {
      "can_upload_completion_images": true,
      "can_create_stories": true,
      "can_access_premium_content": true,
      "can_create_custom_tasks": true,
      "can_assign_couple_tasks": false,
      "can_receive_couple_tasks": false
    },
    "limits": {
      "max_tasks_per_day": null,
      "max_active_outcomes": 2,
      "remaining_outcome_slots": 1
    }
  }
}
```

#### Create Checkout Session
**POST** `/subscription/checkout`

Create a Stripe checkout session for subscription.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "plan": 2
}
```

**Response:**
```json
{
  "success": true,
  "checkout_url": "https://checkout.stripe.com/...",
  "plan": {
    "value": 2,
    "label": "Premium",
    "price_formatted": "£2.99"
  }
}
```

#### Cancel Subscription
**POST** `/subscription/cancel`

Cancel the user's current subscription.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "message": "Subscription cancelled successfully"
}
```

#### Get Billing Portal
**GET** `/subscription/billing-portal`

Get the Stripe billing portal URL for subscription management.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "billing_portal_url": "https://billing.stripe.com/..."
}
```

### Reactions

#### Toggle Reaction
**POST** `/reactions/toggle`

Toggle a reaction on content.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "reactable_type": "story",
  "reactable_id": 1,
  "reaction_type": "like"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Reaction added",
  "reaction": {
    "type": "like",
    "active": true
  },
  "reactions": {
    "count": 16,
    "user_reacted": true
  }
}
```

### Search

#### Search Content
**GET** `/search`

Search across all content types.

**Query Parameters:**
- `q` (required): Search query
- `type` (optional): Content type (`all`, `stories`, `fantasies`, `statuses`, `tasks`)
- `limit` (optional): Number of results (default: 20)
- `offset` (optional): Offset for pagination (default: 0)

**Response:**
```json
{
  "success": true,
  "query": "BDSM",
  "type": "all",
  "results": {
    "stories": [
      {
        "id": 1,
        "title": "My First BDSM Experience",
        "slug": "my-first-bdsm-experience",
        "summary": "A story about...",
        "word_count": 1500,
        "created_at": "2024-01-01T10:00:00Z",
        "author": {
          "id": 2,
          "name": "Jane Smith",
          "username": "janesmith"
        }
      }
    ],
    "fantasies": [...],
    "statuses": [...],
    "tasks": [...]
  },
  "pagination": {
    "limit": 20,
    "offset": 0
  }
}
```

## Error Codes

- `400` - Bad Request (validation errors)
- `401` - Unauthorized (invalid or missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error

## Rate Limiting

API requests are rate limited to prevent abuse. The limits are:

- **Authentication endpoints:** 5 requests per minute
- **Content creation:** 10 requests per minute
- **General API:** 60 requests per minute

Rate limit headers are included in responses:
- `X-RateLimit-Limit`
- `X-RateLimit-Remaining`
- `X-RateLimit-Reset`

## Webhooks

The API supports Stripe webhooks for subscription events. The webhook endpoint is:

**POST** `/stripe/webhook`

This endpoint handles:
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

## SDK Examples

### JavaScript/TypeScript

```typescript
class KinkMasterAPI {
  private baseURL = 'https://your-domain.com/api/v1';
  private token: string | null = null;

  async login(email: string, password: string) {
    const response = await fetch(`${this.baseURL}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    if (data.success) {
      this.token = data.token;
    }
    return data;
  }

  async getActiveTask() {
    const response = await fetch(`${this.baseURL}/tasks/active`, {
      headers: { 'Authorization': `Bearer ${this.token}` }
    });
    return response.json();
  }

  async completeTask(completionNote: string, image?: File) {
    const formData = new FormData();
    formData.append('completion_note', completionNote);
    if (image) formData.append('completion_image', image);

    const response = await fetch(`${this.baseURL}/tasks/complete`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${this.token}` },
      body: formData
    });
    return response.json();
  }
}
```

### Swift (iOS)

```swift
class KinkMasterAPI {
    private let baseURL = "https://your-domain.com/api/v1"
    private var token: String?
    
    func login(email: String, password: String) async throws -> LoginResponse {
        let url = URL(string: "\(baseURL)/auth/login")!
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        
        let body = ["email": email, "password": password]
        request.httpBody = try JSONSerialization.data(withJSONObject: body)
        
        let (data, _) = try await URLSession.shared.data(for: request)
        let response = try JSONDecoder().decode(LoginResponse.self, from: data)
        
        if response.success {
            token = response.token
        }
        
        return response
    }
    
    func getActiveTask() async throws -> TaskResponse {
        let url = URL(string: "\(baseURL)/tasks/active")!
        var request = URLRequest(url: url)
        request.setValue("Bearer \(token ?? "")", forHTTPHeaderField: "Authorization")
        
        let (data, _) = try await URLSession.shared.data(for: request)
        return try JSONDecoder().decode(TaskResponse.self, from: data)
    }
}
```

## Support

For API support and questions, please contact the development team or refer to the main application documentation.