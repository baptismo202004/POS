<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('/login');
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
        
        return redirect()->route('login')->with('success', 'Login successful');
    }

    // Invalid credentials - send a single flash message (handled by SweetAlert in the view)
    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');


Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->name('dashboard');

// Logout route used by the dashboard user dropdown (POST)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');


