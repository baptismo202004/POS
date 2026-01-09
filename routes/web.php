<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Attempt authentication against the users table.
    if (Auth::attempt($request->only('email', 'password')) ) {
        // Regenerate session to prevent fixation
        $request->session()->regenerate();
        // Flash success and return to the login page so the client can show the alert first,
        // then the client will redirect to the dashboard.
        return redirect()->route('login')->with('success', 'Login successful');
    }

    // Invalid credentials - send a single flash message (handled by SweetAlert in the view)
    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');


Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->name('dashboard');


