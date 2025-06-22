<?php

namespace App\Http\Controllers\OIDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserInfoApiController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user(); // アクセストークンに紐づくユーザー

        if (!$request->user()->tokenCan('openid')) {
            abort(403, 'The token does not have openid scope.');
        }

        return response()->json([
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ]);
    }
}
