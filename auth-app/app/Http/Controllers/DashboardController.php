<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        $scopes = $role?->scopes()->pluck('name');
        $permissions = $role?->permissions()->pluck('name');

        return Inertia::render('Dashboard', [
            'role' => $role?->name,
            'scopes' => $scopes->toArray(),
            'permissions' => $permissions->toArray(),
        ]);
    }
}
