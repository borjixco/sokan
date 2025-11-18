<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Client/Parking/Index');
    }
}
