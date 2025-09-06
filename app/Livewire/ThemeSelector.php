<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeSelector extends Component
{
    public string $theme = 'system';

    public function mount(): void
    {
        // Get theme from user profile, session, or default to system
        if (auth()->check() && auth()->user()->profile) {
            $this->theme = auth()->user()->profile->theme_preference ?? 'system';
        } else {
            $this->theme = session('theme', 'system');
        }
    }

    protected $listeners = ['theme-changed' => 'updateTheme'];

    public function updateTheme($theme): void
    {
        $this->theme = $theme;
        session(['theme' => $theme]);

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
