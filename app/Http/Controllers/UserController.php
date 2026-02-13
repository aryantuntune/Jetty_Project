<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $counts = [
            'admins' => User::where('role_id', 2)->count(),
            'managers' => User::where('role_id', 3)->count(),
            'operators' => User::where('role_id', 4)->count(),
            'checkers' => User::where('role_id', 5)->count(),
        ];

        return Inertia::render('Users/Index', [
            'counts' => $counts,
        ]);
    }
}
