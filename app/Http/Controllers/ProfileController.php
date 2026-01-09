<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();

        $activities = [];
        if (Schema::hasTable('activities')) {
            $activities = DB::table('activities')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        // Resolve a usable avatar URL regardless of how the path is stored.
        $avatarUrl = null;
        $avatar = $user->profile_picture ?? null;
        if ($avatar) {
            // Absolute URL or protocol-relative
            if (Str::startsWith($avatar, ['http://', 'https://', '//'])) {
                $avatarUrl = $avatar;
            } elseif (Str::startsWith($avatar, 'storage/')) {
                $avatarUrl = asset($avatar);
            } else {
                // If the path is already a public path (starts with a slash) or a direct file under public/
                $candidate = ltrim($avatar, '/');
                // If stored under storage/app/public (public disk), check and use storage/ prefix
                if (Storage::disk('public')->exists($candidate)) {
                    $avatarUrl = asset('storage/' . $candidate);
                } elseif (file_exists(public_path($candidate))) {
                    $avatarUrl = asset($candidate);
                } else {
                    // fallback: try asset of the raw value
                    $avatarUrl = asset($candidate);
                }
            }
        }

        return view('profile', compact('activities', 'avatarUrl'));
    }

    public function avatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $text = implode('<br>', $messages);
            return back()->withErrors($validator)->with('swal', [
                'title' => 'Upload failed',
                'html' => $text,
                'icon' => 'error',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3500,
            ]);
        }

        $user = Auth::user();

        try {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile_picture = 'storage/' . $path;
            $user->save();
            Auth::setUser($user->fresh());

            return back()->with('swal', [
                'title' => 'Profile picture updated',
                'text' => 'Your profile picture was updated successfully.',
                'icon' => 'success',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2200,
            ]);
        } catch (\Exception $e) {
            return back()->with('swal', [
                'title' => 'Upload error',
                'text' => 'There was a problem saving your image.',
                'icon' => 'error',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3500,
            ]);
        }
    }

    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $text = implode('<br>', $messages);
            return back()->withErrors($validator)->with('swal', [
                'title' => 'Validation error',
                'html' => $text,
                'icon' => 'error',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3500,
            ]);
        }

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('swal', [
                'title' => 'Incorrect password',
                'text' => 'Current password is incorrect.',
                'icon' => 'error',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 3500,
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return back()->with('swal', [
            'title' => 'Password changed',
            'text' => 'Your password was updated successfully.',
            'icon' => 'success',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }
}
