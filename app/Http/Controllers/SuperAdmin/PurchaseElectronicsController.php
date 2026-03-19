<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseElectronicsController extends Controller
{
    public function panel(Request $request)
    {
        return view('SuperAdmin.purchases._electronic_serials_panel');
    }
}
