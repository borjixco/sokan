<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $rows = Event::with('user');
        if($search){
            $rows = $rows
                ->where('title','LIKE',"%$search%")
                ->orWhere('location','LIKE',"%$search%")
                ->orWhere('short_description','LIKE',"%$search%")
                ->orWhere('description','LIKE',"%$search%");
        }
        $rows = $rows->orderByDesc('created_at')->paginate(20);
        $rows = EventResource::collection($rows);
        return inertia('Client/Events/Index', compact('rows'));
    }
}
