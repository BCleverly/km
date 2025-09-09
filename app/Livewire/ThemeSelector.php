<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeSelector extends Component
{
    public string $theme = 'system';

    public function mount(): void
    {
        // Get theme from user profile, session, or default to system
        if (auth()->check()) {
            $user = auth()->user();
            $profile = $user->profile;
            
            if ($profile && $profile->theme_preference) {
                $this->theme = $profile->theme_preference;
            } else {
                $this->theme = session('theme', 'system');
            }
        } else {
            $this->theme = session('theme', 'system');
        }
        
        // Ensure session is in sync with the loaded theme
        session(['theme' => $this->theme]);
    }

    protected $listeners = ['theme-changed' => 'updateTheme'];

    public function updateTheme($theme): void
    {
        $this->theme = $theme;
        session(['theme' => $theme]);

        // Update user profile if authenticated and the theme changed
        if (auth()->check() && auth()->user()->profile) {
            $currentProfileTheme = auth()->user()->profile->theme_preference ?? 'system';
            if ($currentProfileTheme !== $theme) {
                auth()->user()->profile->update(['theme_preference' => $theme]);
            }
        }

        // Force a re-render to update the icon
        $this->render();
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
        session(['theme' => $theme]);

        // Update user profile if authenticated
        if (auth()->check() && auth()->user()->profile) {
            auth()->user()->profile->update(['theme_preference' => $theme]);
        }

        // Dispatch event to update the HTML class
        $this->dispatch('theme-changed', theme: $theme);

        // Also dispatch to document for JavaScript handling
        $this->js("
            document.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { theme: '{$theme}' } 
            }));
        ");
    }

    public function render()
    {
        return view('livewire.theme-selector');
    }
}
