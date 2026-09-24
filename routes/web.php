<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EntryController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::get('/', fn () => redirect()->route('entries.index'));
    Route::get('/dashboard', fn () => redirect()->route('entries.index'))->name('dashboard');
    Route::resource('entries', EntryController::class)->except(['show']);
    Route::get('/entries-export', [EntryController::class, 'export'])->name('entries.export');
    Route::post('/options/quick-add', [EntryController::class, 'quickAddOption'])->name('options.quick-add');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.options.index'));
    Route::resource('options', Admin\OptionController::class)->except(['show', 'create', 'edit']);
    Route::resource('users', Admin\UserController::class)->except(['show', 'create', 'edit']);
    Route::post('users/{id}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
});
