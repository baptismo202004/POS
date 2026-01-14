<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UiStateController extends Controller
{
    public function setSidebarUserMgmt(Request $request)
    {
        $request->validate([
            'open' => 'required|boolean',
        ]);
        session(['ui.sidebar.user_mgmt_open' => (bool) $request->boolean('open')]);
        return response()->json(['ok' => true]);
    }
}
