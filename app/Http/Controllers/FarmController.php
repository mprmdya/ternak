<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index()
    {
        return view('farm-monitor');
    }
}