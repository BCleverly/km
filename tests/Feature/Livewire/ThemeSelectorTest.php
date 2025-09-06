<?php

use App\Livewire\ThemeSelector;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ThemeSelector::class)
        ->assertStatus(200);
});

it('displays theme selector button', function () {
    Livewire::test(ThemeSelector::class)
        ->assertSee('Change theme')
        ->assertSee('Light')
        ->assertSee('Dark')
        ->assertSee('System');
});

it('defaults to system theme', function () {
    Livewire::test(ThemeSelector::class)
        ->assertSet('theme', 'system');
});

it('can set theme to light', function () {
    Livewire::test(ThemeSelector::class)
        ->call('setTheme', 'light')
        ->assertSet('theme', 'light')
        ->assertSessionHas('theme', 'light');
});

it('can set theme to dark', function () {
    Livewire::test(ThemeSelector::class)
        ->call('setTheme', 'dark')
        ->assertSet('theme', 'dark')
        ->assertSessionHas('theme', 'dark');
});

it('can set theme to system', function () {
    Livewire::test(ThemeSelector::class)
        ->call('setTheme', 'system')
        ->assertSet('theme', 'system')
        ->assertSessionHas('theme', 'system');
});

it('dispatches theme-changed event when setting theme', function () {
    Livewire::test(ThemeSelector::class)
        ->call('setTheme', 'dark')
        ->assertDispatched('theme-changed', theme: 'dark');
});

it('loads theme from session on mount', function () {
    session(['theme' => 'dark']);
    
    Livewire::test(ThemeSelector::class)
        ->assertSet('theme', 'dark');
});
