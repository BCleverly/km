<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Homepage;
use Illuminate\Support\Facades\Route;

Route::passkeys();

Route::get('/', Homepage::class);

// Authentication Routes
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// Password Reset Routes
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
