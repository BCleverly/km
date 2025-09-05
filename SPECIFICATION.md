Project Specification: The Task & Reward Community Platform
1. Project Overview

This document outlines the features and technical specifications for a subscription-based SaaS community platform built on Laravel 12. The platform's core is a gamified task system where users are assigned tasks and receive rewards or punishments based on self-reported completion. It is built on a foundation of trust.

The platform will foster a strong community through user-generated content, feedback mechanisms, and distinct user roles. The architecture must be robust, scalable, and maintainable, leveraging modern Laravel ecosystem tools and being API-ready from day one.

Core Principles:

    Gamification: Engage users with a cycle of tasks, rewards, and punishments.

    Community Driven: The content library grows through user submissions.

    Trust-Based: The system operates on the honor system for task completion.

    Inclusivity: Caters to different user types (male, female, couples).

    Moderation: A clear system for maintaining content quality.

    Scalability: The architecture must support future features like advanced couple accounts, fantasy matching, and native mobile applications.

2. Technical Stack & Recommended Packages

   Backend Framework: Laravel 12

   Frontend Framework: Livewire 3 (for dynamic, single-page application feel) & Alpine.js

   CSS Framework: Tailwind CSS v4

   Database: MySQL 8 or PostgreSQL 15

   WebSockets: Laravel Reverb

   Server: Nginx

   Payment Gateway: Stripe, integrated via Laravel Cashier.

Recommended Laravel Packages:

    laravel/cashier: For Stripe subscription management.

    spatie/laravel-permission: For robust role and permission management (Admin, Moderator, Reviewer, User).

    livewire/wire: Core frontend interactivity.

    filament/filament: For a rapid-admin panel build. This will save immense time creating the backend management interfaces for Admins, Moderators, and Reviewers.

    lorisleiva/laravel-actions: For creating reusable, single-purpose classes for business logic. This is key to sharing code between Livewire components and API controllers.

    laravel/sanctum: For first-party mobile and SPA API authentication.

    spatie/laravel-medialibrary: If users are to upload profile pictures or images for stories.

    spatie/laravel-activitylog: To create a comprehensive audit trail of user and admin actions.

3. Database Schema Design

Below is a proposed schema. Primary keys are id (unsigned big integer), and timestamps (created_at, updated_at) are assumed for all tables.

users Table

    name (string)

    email (string, unique)

    password (string)

    user_type (enum: 'male', 'female', 'couple')

    partner_id (unsignedBigInteger, nullable, foreign key to users.id): Links the secondary user in a 'couple' account.

    stripe_id (string, nullable): For Laravel Cashier.

    pm_type (string, nullable): For Laravel Cashier.

    pm_last_four (string, nullable): For Laravel Cashier.

    trial_ends_at (timestamp, nullable): For Laravel Cashier.

roles & permissions Tables

    To be managed by spatie/laravel-permission. This will create roles, permissions, model_has_roles, model_has_permissions, role_has_permissions tables.

tasks, rewards, punishments Tables (Content Tables)

    These three tables will have an identical structure.

    title (string)

    description (text)

    difficulty_level (integer, e.g., 1-10)

    target_user_type (enum: 'male', 'female', 'couple', 'any')

    user_id (unsignedBigInteger, foreign key to users.id): The original author.

    status (enum: 'pending', 'approved', 'in_review', 'rejected'): The moderation status.

    view_count (integer, default 0)

    is_premium (boolean, default false): Is this content for subscribers only?

user_assigned_tasks Table (Junction Table)

    user_id (unsignedBigInteger, foreign key to users.id)

    task_id (unsignedBigInteger, foreign key to tasks.id)

    status (enum: 'assigned', 'completed', 'failed')

    outcome_type (string, nullable, e.g., 'reward', 'punishment')

    outcome_id (unsignedBigInteger, nullable): The ID of the assigned reward or punishment.

    assigned_at (timestamp)

    completed_at (timestamp, nullable)

reactions Table

    user_id (unsignedBigInteger, foreign key to users.id)

    reactable_id (unsignedBigInteger): The ID of the item being reacted to (e.g., task_id).

    reactable_type (string): The model name (e.g., 'App\Models\Task').

    type (enum: 'positive', 'negative')

    Unique composite key on (user_id, reactable_id, reactable_type) to enforce one reaction per user.

stories Table

    user_id (unsignedBigInteger, foreign key to users.id)

    title (string)

    content (longText)

    is_private (boolean, default true): Initially private to members.

    status (enum: 'pending', 'approved', 'rejected')

4. Feature Specification Details
   4.1. User Roles & Permissions (spatie/laravel-permission)

   User (Default):

        Can get tasks assigned.

        Can submit new content (tasks, rewards, punishments, stories).

        Can react to content.

        Can manage their own subscription.

   Reviewer:

        Inherits all User permissions.

        Can access the "In Review" queue in the admin panel.

        Can vote to approve or reject items in the review queue.

   Moderator:

        Inherits all Reviewer permissions.

        Can directly approve, reject, or edit any user-submitted content.

        Can ban/suspend users.

   Admin:

        Superuser with all permissions.

        Can manage user roles.

        Can view subscription data, issue refunds (via Stripe dashboard).

        Full CRUD on all platform data.

4.2. User Onboarding & Profiles

    Registration: User provides name, email, password, and selects user_type ('male', 'female', 'couple').

    Couple Accounts: If 'couple' is selected, upon first login, the user is prompted to send an invitation link to their partner. The partner signs up via this link, creating a separate user account that is linked via the partner_id field.

        Shared View: Both partners see the same user_assigned_tasks. Any action (completing, failing) taken by one partner is reflected for both.

        Separate Logins: Both partners have their own email/password credentials.

    Profile Page: Displays user stats (tasks completed, failed), current subscription status, and links to manage their account.

4.3. The Core Task Loop (Livewire Components & Actions)

    Dashboard: A user clicks a "Get New Task" button in a Livewire component.

    Task Assignment Logic:

        The Livewire component calls a RequestNewTask Action.

        The action fetches an approved task from the tasks table, filtering by the user's user_type and ensuring it hasn't been assigned previously.

        A record is created in user_assigned_tasks with status = 'assigned'.

    Task Display: The user is shown the assigned task. Two buttons are present: "I Completed This" and "I Failed This".

    Outcome:

        Clicking a button calls a ResolveTask Action (passing 'completed' or 'failed').

        The action updates the user_assigned_tasks status and randomly assigns an appropriate reward or punishment.

4.4. UGC & Moderation Workflow

    Submission Forms: Simple Livewire forms for submitting content. On submission, a SubmitContent Action is called, which creates the record with status = 'pending'.

    Initial Review (Moderator/Admin): New pending items are reviewed in the Filament admin panel.

    Community Feedback Loop:

        Once content is approved, users can react to it.

        Algorithm: A scheduled job runs periodically (e.g., every hour).

            It queries all approved content items.

            It calculates the reaction ratio: (negative_reactions / total_reactions) * 100.

            Condition: If total_reactions >= 50 AND negative_ratio >= 70%, the item's status is changed to in_review.

    Review Queue: Items with in_review status appear in a special section of the admin panel.

4.5. Monetization (Laravel Cashier)

    Subscription Plans:

        Free Tier: Limited to 1 task per day. Cannot submit or view stories. Sees ads.

        Premium Tier ($X/month): Unlimited tasks, can submit & view all stories, ad-free experience, access to is_premium content.

    Implementation: Use Cashier's trait on the User model. Create middleware to protect premium routes/features. Use Livewire components for the billing page.

4.6. Fetish Stories

    A dedicated section of the site, accessible only to Premium subscribers.

    A form for story submission. Stories are pending until approved by a Moderator.

    A simple, paginated view to read approved stories.

4.7. API Specification for Mobile Apps

To support future native iOS and Android applications, a versioned API must be developed. All business logic should be contained within Laravel Actions to ensure reusability between the web interface and the API.

    Authentication: The API will be stateless and use Laravel Sanctum for token-based authentication. Mobile clients will obtain a token upon login and include it in the Authorization header for all subsequent requests.

    Versioning: The API will be versioned in the URL, starting with /api/v1/.

    Responses: All responses will be in JSON format, using Laravel API Resources to standardize the output structure.

Key API Endpoints:

    Auth:

        POST /api/v1/register

        POST /api/v1/login

        POST /api/v1/logout (Authenticated)

    User:

        GET /api/v1/user (Authenticated - gets current user profile)

        PUT /api/v1/user (Authenticated - updates user profile)

    Tasks:

        POST /api/v1/tasks/request (Authenticated - executes RequestNewTask Action)

        POST /api/v1/assigned-tasks/{id}/complete (Authenticated - executes ResolveTask Action)

        POST /api/v1/assigned-tasks/{id}/fail (Authenticated - executes ResolveTask Action)

    Content & UGC:

        GET /api/v1/content/{type} (e.g., /api/v1/content/tasks - lists approved tasks)

        POST /api/v1/content/{type} (Authenticated - executes SubmitContent Action for tasks, rewards, etc.)

        POST /api/v1/content/{type}/{id}/react (Authenticated - adds a positive/negative reaction)

    Stories:

        GET /api/v1/stories (Authenticated, Premium)

        POST /api/v1/stories (Authenticated, Premium)

        GET /api/v1/stories/{id} (Authenticated, Premium)

4.8. UI/UX Guidance

The visual design should be clean, modern, and intuitive to foster user trust and engagement.

    Aesthetic: Minimalist. Use a generous amount of whitespace and a simple, professional color palette with one clear accent color for primary actions. A dark mode should be considered from the start.

    Layout Principles:

        Dashboard: The primary user dashboard should be hyper-focused. When a task is active, display it in a large, central card with two prominent, unambiguous buttons below it ("I Completed This," "I Failed This").

        Content Feeds: Use a card-based layout for browsing user-submitted tasks, stories, etc. Each card should be uniform and display key information (title, snippet, author, reactions) in a scannable format.

        Forms: Content submission forms should be simple, single-purpose, and focused. Avoid clutter and guide the user through the process.

    Inspiration: Draw inspiration from the clean interfaces of leading SaaS products. The focus should be on clarity and ease of use.

        Linear (https://linear.app): Gold standard for a focused, efficient task-based UI. Excellent model for the main dashboard.

        Vercel (https://vercel.com/dashboard): Prime example of a clean, information-rich dashboard with a great dark mode. Useful for content feed layouts.

        Craft (https://www.craft.do): Beautiful, focused writing and reading experience. A great reference for the story submission and viewing sections.

        Stripe Dashboard (https://dashboard.stripe.com): Masterclass in making forms and settings pages feel simple and intuitive.

4.9. Real-time Notifications (Laravel Reverb)

To provide a smooth, interactive user experience, the platform will use Laravel Reverb for real-time WebSocket communication. This will push instant notifications to the user's browser without needing a page refresh.

    Implementation: Laravel's built-in event broadcasting system will be used. Events will be broadcast on private channels for authenticated users. The Livewire frontend will listen for these events using Laravel Echo.

    Key Notification Events:

        NewTaskAssigned: Fired when a user receives a new task. The frontend will display a toast notification and update the dashboard UI.

        ContentStatusUpdated: Fired when a user's submitted content (task, reward, story) changes status (e.g., to 'approved' or 'rejected').

        PartnerTaskResolved: Fired for 'couple' accounts. When one partner completes or fails a task, the other partner receives a real-time update.

        NewOutcomeAssigned: Fired when a reward or punishment is assigned after a task is resolved, letting the user know their fate.

4.10. User & System Activity Logging (spatie/laravel-activitylog)

To ensure accountability and provide a clear audit trail for moderators, the application will log key activities.

    Implementation: The laravel-activitylog package will be configured to automatically log events for specified Eloquent models. These logs will be accessible through the Filament admin panel for users with the appropriate permissions (Moderators, Admins).

    Key Logged Events:

        User Actions: User registration, login, failed login attempts, subscription changes.

        Content Lifecycle: Creation of a task, reward, punishment, or story.

        Moderation Actions: Status changes on any content (e.g., "Moderator X approved Task Y submitted by User Z").

        Administrative Actions: A user's role is changed, a user is banned/suspended.

        Core Loop: A task is assigned to a user; a user completes or fails a task.

5. Future Expansion: Fantasy Matching System

   Concept: A "Tinder-like" card-swiping interface.

   Fantasies: Admins will pre-populate a fantasies table with various items.

   User Interaction: A user is shown a fantasy card and can swipe Yes, No, or Maybe. The choice is stored in a user_fantasy_preferences table.

   Goal: This data is for the user's private reflection or could be expanded in the future to a system where couples can see their matching fantasies.
