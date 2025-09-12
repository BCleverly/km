<?php

use App\Livewire\ManageWidget;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ManageWidget::class)
        ->assertStatus(200);
});
