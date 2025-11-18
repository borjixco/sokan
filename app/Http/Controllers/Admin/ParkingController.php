<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function index()
    {
        return inertia('Admin/Parking/Index');
    }
}
