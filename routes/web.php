<?php

use App\Http\Controllers\BioStarController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

    Route::view('/item', 'items')
    ->middleware(['auth'])
    ->name('items');
    Route::view('/shiftcreate', 'shiftcreate')
    ->middleware(['auth'])
    ->name('shiftcreate');

    Route::view('/event', 'events')
    ->middleware(['auth'])
    ->name('eventtype');
    Route::view('/users', 'userlistshow')
    ->middleware(['auth'])
    ->name('userlistshow');
    
    Route::view('/user_register', 'userregistration')
    ->middleware(['auth'])
    ->name('userregistration');

    Route::view('/userEdit/{user_id}', 'userprofileedit')
    ->middleware(['auth'])
    ->name('userprofileedit');

    
    Route::view('/shiftedit/{shiftId}', 'shiftedit')
    ->middleware(['auth'])
    ->name('shiftedit');
    
    Route::view('/useraddshift/{shiftId}', 'addUsersShift')
    ->middleware(['auth'])
    ->name('useraddshift');

    Route::view('/AttendanceReport', 'taReports')
    ->middleware(['auth'])
    ->name('taReports');

require __DIR__.'/auth.php';
