<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function handle(Request $request)
    {
        $request->session()->forget('sub');
        return redirect('/');
    }
}
