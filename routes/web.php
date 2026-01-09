<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuperAdmin\ProductController as SuperAdminProductController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
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

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Login successful');
    }

    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');

Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Product routes
    Route::prefix('superadmin')->group(function () {
        Route::get('/products', [SuperAdminProductController::class, 'index'])->name('superadmin.products.index');
        Route::get('/products/create', [SuperAdminProductController::class, 'create'])->name('superadmin.products.create');
        Route::post('/products', [SuperAdminProductController::class, 'store'])->name('superadmin.products.store');
        Route::get('/products/{product}/edit', [SuperAdminProductController::class, 'edit'])->name('superadmin.products.edit');
        Route::put('/products/{product}', [SuperAdminProductController::class, 'update'])->name('superadmin.products.update');
        Route::delete('/products/{product}', [SuperAdminProductController::class, 'destroy'])->name('superadmin.products.destroy');
    });

    // Password reset routes (remote)
    Route::get('/password/reset', function () {
        return view('auth.passwords.email');
    })->name('password.request');

    Route::post('/password/email', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $user = \App\Models\User::where('email', $request->input('email'))->first();
        if ($user) {
            // dispatch reset email here if implemented
        }
        return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
    })->name('password.email');
});
