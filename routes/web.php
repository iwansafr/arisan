<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('member','member.member-list')->name('member');
    Volt::route('member/create','member.member-create')->name('member.create');
    Volt::route('member/{memberId}/edit','member.member-edit')->name('member.edit');

    Volt::route('transaction','transaction.transaction-list')->name('transaction');
});

require __DIR__.'/auth.php';
