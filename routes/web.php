<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

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
        
        return redirect()->route('login')->with('success', 'Login successful');
    }

    // Invalid credentials - send a single flash message (handled by SweetAlert in the view)
    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');


Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->middleware('auth')->name('dashboard');

// Logout route used by the dashboard user dropdown (POST)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});


// Password reset (simple request flow)
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    // If user exists, we would normally send a reset link. For now, just show a generic message.
    $user = \App\Models\User::where('email', $request->input('email'))->first();
    if ($user) {
        // dispatch reset email here if implemented
    }
    return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
})->name('password.email');


