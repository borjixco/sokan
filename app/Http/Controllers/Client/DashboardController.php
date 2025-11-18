<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function redirectToDashboard()
    {
        return redirect()->route('client.charges');
    }

    public function index()
    {
        return inertia('Client/Dashboard/Index');
    }
}
