<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PartnerInvitation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PartnerInvitationController extends Controller
{
    public function accept(Request $request, string $token)
    {
        $invitation = PartnerInvitation::where('token', $token)->first();

        if (! $invitation) {
            return redirect()->route('register')
                ->with('error', 'Invalid invitation link.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('register')
                ->with('error', 'This invitation has expired.');
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('login')
                ->with('error', 'This invitation has already been accepted.');
        }

        // If user is already logged in, link them as partner
        if (Auth::check()) {
            return $this->linkExistingUser($invitation, Auth::user());
        }

        // Show registration form with pre-filled email
        return view('auth.register', [
            'invitation' => $invitation,
            'email' => $invitation->email,
        ]);
    }

    public function processAcceptance(Request $request, string $token)
    {
        $invitation = PartnerInvitation::where('token', $token)->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'invitation' => 'Invalid invitation link.',
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation has expired.',
            ]);
        }

        if ($invitation->isAccepted()) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation has already been accepted.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Ensure email matches invitation
        if ($request->email !== $invitation->email) {
            $validator->errors()->add('email', 'Email must match the invitation.');
        }

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Create the new user
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => \App\TargetUserType::Couple,
            'partner_id' => $invitation->invited_by,
        ]);

        // Link the inviter as partner
        $inviter = $invitation->inviter;
        $inviter->update(['partner_id' => $user->id]);

        // Mark invitation as accepted
        $invitation->update([
            'accepted_at' => now(),
            'accepted_by' => $user->id,
        ]);

        // Log the user in
        Auth::login($user);

        return redirect()->route('app.dashboard')
            ->with('success', 'Welcome! You have successfully joined as a partner.');
    }

    private function linkExistingUser(PartnerInvitation $invitation, \App\Models\User $user): Response
    {
        // Check if user email matches invitation
        if ($user->email !== $invitation->email) {
            return redirect()->route('app.dashboard')
                ->with('error', 'This invitation is for a different email address.');
        }

        // Check if user already has a partner
        if ($user->partner_id) {
            return redirect()->route('app.dashboard')
                ->with('error', 'You already have a partner linked to your account.');
        }

        // Link the users as partners
        $user->update(['partner_id' => $invitation->invited_by]);
        $inviter = $invitation->inviter;
        $inviter->update(['partner_id' => $user->id]);

        // Mark invitation as accepted
        $invitation->update([
            'accepted_at' => now(),
            'accepted_by' => $user->id,
        ]);

        return redirect()->route('app.dashboard')
            ->with('success', 'You have successfully linked with your partner!');
    }
}
