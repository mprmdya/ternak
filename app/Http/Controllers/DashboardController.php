<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $adminCount = User::where('role', 'admin')->count();
        return view('home', compact('userCount', 'adminCount'));
    }
}
