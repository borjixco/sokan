<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $rows = Ticket::with(['user']);
        $search = $request->search;
        if($search){
            $rows->whereHas('user', function ($q) use($search){
                $q->where('name','LIKE',"%$search%")
                    ->orWhere('mobile','LIKE',"%$search%");
            })->orWhere('subject','LIKE', "%$search%");
        }
        $rows = $rows->orderBy('updated_at','ASC')->paginate(10);
        $rows = TicketResource::collection($rows);
        return inertia('Admin/Tickets/Index',compact('rows'));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }

    public function edit(Ticket $ticket,Request $request)
    {
        $ticket = new TicketResource($ticket);
        $ticketStatus = enumFormated(TicketStatusEnum::cases());
        return inertia('Admin/Tickets/Edit', compact('ticket','ticketStatus'));
    }

    public function update(Ticket $ticket,Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|min:5'
            ],
            [
                'content' => 'متن تیکت را وارد کنید'
            ]);
            (new TicketService)->response($ticket,$request->user()->id,$request->get('content'));
            (new TicketService)->updateStatus($ticket,'RESPONDED');
            return redirectMessage('success', 'تیکت ارسال شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function updateStatus(Ticket $ticket,Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:'.collect(enumNames(TicketStatusEnum::cases()))->implode(',')
            ],
            [
                'status' => 'وضعیت به درستی انتخاب شود'
            ]);
            (new TicketService)->updateStatus($ticket,$request->status);
            return redirectMessage('success', 'وضعیت تغییر کرد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }
}
