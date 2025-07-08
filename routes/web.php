<?php

use App\Livewire\Plates\Index;
use App\Livewire\Plates\Create;
use App\Livewire\Plates\Form;
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
    Route::get('/plates', Index::class)->name('plates.index');
    Route::get('/plates/create', Create::class)->name('plates.create');
    Route::get('/plates/{plate}/edit', Form::class)->name('plates.edit');
    Route::get('/violations', \App\Livewire\Violations\Index::class)->name('violations.index');
    Route::get('/violations/create', \App\Livewire\Violations\Form::class)->name('violations.create');
    Route::get('/violations/{violation}/edit', \App\Livewire\Violations\Form::class)->name('violations.edit');
});


require __DIR__.'/auth.php';
