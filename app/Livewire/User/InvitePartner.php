<?php

namespace App\Livewire\User;

use App\Actions\SendPartnerInvitation;
use App\Models\PartnerInvitation;
use Livewire\Component;

class InvitePartner extends Component
{
    public $email = '';

    public $message = '';

    public $showForm = true;

    public $lastInvitation = null;

    public $currentInvitation = null;

    public function mount()
    {
        // Check if user has a pending invitation
        $this->currentInvitation = PartnerInvitation::getPendingInvitation(auth()->user());
        $this->showForm = ! $this->currentInvitation;
    }

    public function sendInvitation()
    {
        // Check authorization first
        if (! auth()->user()->canSendPartnerInvitations()) {
            $this->addError('email', 'You do not have permission to send partner invitations.');

            return;
        }

        $this->validate([
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $action = new SendPartnerInvitation;
            $this->lastInvitation = $action->execute(
                auth()->user(),
                $this->email,
                $this->message ?: null
            );

            // Reset form and update current invitation
            $this->reset(['email', 'message']);
            $this->showForm = false;
            $this->currentInvitation = $this->lastInvitation;

            session()->flash('invitation_message', 'Partner invitation sent successfully!');
            $this->dispatch('invitation-sent');
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            // Check if it's a validation error about existing user
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->addError('email', 'A user with this email address already exists. Please use a different email address.');
            } else {
                $this->addError('email', 'An error occurred while sending the invitation. Please try again.');
                \Log::error('Partner invitation error: '.$e->getMessage());
            }
        }
    }

    public function sendAnother()
    {
        // Check if there's still a pending invitation
        $this->currentInvitation = PartnerInvitation::getPendingInvitation(auth()->user());

        if (! $this->currentInvitation) {
            $this->showForm = true;
            $this->lastInvitation = null;
        }
    }

    public function revokeInvitation()
    {
        if (! $this->currentInvitation) {
            return;
        }

        // Check authorization
        if (! auth()->user()->canSendPartnerInvitations()) {
            $this->addError('email', 'You do not have permission to revoke invitations.');

            return;
        }

        // Only allow revoking pending invitations
        if ($this->currentInvitation->accepted_at || $this->currentInvitation->isExpired()) {
            $this->addError('email', 'This invitation cannot be revoked.');

            return;
        }

        // Delete the invitation
        $this->currentInvitation->delete();

        // Reset state
        $this->currentInvitation = null;
        $this->showForm = true;
        $this->lastInvitation = null;

        session()->flash('invitation_message', 'Invitation revoked successfully.');
        $this->dispatch('invitation-revoked');
    }

    public function revokeInvitationById($invitationId)
    {
        $invitation = PartnerInvitation::where('id', $invitationId)
            ->where('invited_by', auth()->id())
            ->first();

        if (! $invitation) {
            $this->addError('email', 'Invitation not found.');

            return;
        }

        // Check authorization
        if (! auth()->user()->canSendPartnerInvitations()) {
            $this->addError('email', 'You do not have permission to revoke invitations.');

            return;
        }

        // Only allow revoking pending invitations
        if ($invitation->accepted_at || $invitation->isExpired()) {
            $this->addError('email', 'This invitation cannot be revoked.');

            return;
        }

        // Delete the invitation
        $invitation->delete();

        // If this was the current invitation, reset state
        if ($this->currentInvitation && $this->currentInvitation->id === $invitationId) {
            $this->currentInvitation = null;
            $this->showForm = true;
            $this->lastInvitation = null;
        }

        session()->flash('invitation_message', 'Invitation revoked successfully.');
        $this->dispatch('invitation-revoked');
    }

    public function refreshInvitationStatus()
    {
        $this->currentInvitation = PartnerInvitation::getPendingInvitation(auth()->user());
        $this->showForm = ! $this->currentInvitation;
    }

    public function getSentInvitations()
    {
        return PartnerInvitation::where('invited_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.user.invite-partner');
    }
}
