<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    return redirect()->route('dashboard');
})->name('login.post');


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

