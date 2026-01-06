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
        if ($request->input('email') !== 'admin@example.com' || $request->input('password') !== 'password') {
        return redirect()->back()->withInput()->with('error', 'Incorrect email or password');
    }
    return redirect()->route('dashboard')->with('success', 'Login successful');
})->name('login.post');


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


