<?php

namespace App\Http\Controllers;

use App\Models\OIDCUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserInfoController extends Controller
{
    public function handle(Request $request)
    {
        $sub = $request->session()->get('sub');

        $user = OIDCUser::where('sub', $sub)->first();

        return Inertia::render('UserInfo', [
            'sub' => $sub,
            'name' => $user?->name ?? 'Unknown User',
            'email' => $user?->email ?? 'No Email Provided',
        ]);
    }
}
