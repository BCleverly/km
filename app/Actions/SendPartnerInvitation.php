<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PartnerInvitation;
use App\Models\User;
use App\Notifications\PartnerInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class SendPartnerInvitation
{
    public function execute(User $inviter, string $email, ?string $message = null): PartnerInvitation
    {
        // Validate that the inviter can send invitations
        if (! $this->canSendInvitation($inviter)) {
            throw ValidationException::withMessages([
                'invitation' => 'You do not have permission to send partner invitations.',
            ]);
        }

        // Check if email already exists as a user
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A user with this email address already exists.',
            ]);
        }

        // Check if user already has a pending invitation (only one at a time)
        if (PartnerInvitation::hasPendingInvitation($inviter)) {
            throw ValidationException::withMessages([
                'invitation' => 'You already have a pending invitation. Please wait for it to expire or be accepted before sending another.',
            ]);
        }

        // Check if there's already a pending invitation for this email
        $existingInvitation = PartnerInvitation::where('email', $email)
            ->where('invited_by', $inviter->id)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->first();

        if ($existingInvitation) {
            throw ValidationException::withMessages([
                'email' => 'You have already sent an invitation to this email address.',
            ]);
        }

        // Create the invitation
        $invitation = PartnerInvitation::createInvitation($inviter, $email, $message);

        // Send the notification
        Notification::route('mail', $email)
            ->notify(new PartnerInvitationNotification($invitation));

        return $invitation;
    }

    private function canSendInvitation(User $user): bool
    {
        // Couple users, lifetime subscribers, and admins can send invitations
        return $user->user_type === \App\TargetUserType::Couple ||
               $user->hasLifetimeSubscription() ||
               $user->hasRole('Admin');
    }
}
