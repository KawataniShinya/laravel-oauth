<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InitController extends Controller
{
    public function handle(Request $request)
    {
        $request->session()->forget('username');
        return Inertia::render('UsernameInput');
    }
}
