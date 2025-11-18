<?php

namespace App\Http\Controllers\Client;

use App\Enums\TicketStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Category;
use App\Models\Owner;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rows = $user->tickets()->with(['user']);
        $search = $request->search;
        if($search){
            $rows->where('subject','LIKE', "%$search%");
        }
        $rows = $rows->orderBy('updated_at','DESC')->paginate(10);
        $rows = TicketResource::collection($rows);
        return inertia('Client/Tickets/Index',compact('rows'));
    }

    private function categories()
    {
        $categories = Category::where('model_type', Ticket::class)->get()->map(function ($category) {
            return [
                'label' => $category->name,
                'value' => $category->id,
            ];
        });
        return $categories;

    }

    public function create()
    {
        $categories = $this->categories();
        return inertia('Client/Tickets/Create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $request->validate([
                'subject'  => 'required|string|min:3',
                'category' => 'required|exists:categories,id',
                'message'  =>  'required|min:5',
            ],
            [
                'subject'  => 'موضوع را به درستی وارد نمایید',
                'category' => 'دسته را به درستی انتخاب نمایید',
                'message'  => 'پیام را به درستی وارد نمایید',
            ]);
            $ticket = (new TicketService)->create($user->id,$request->category,$request->subject,$request->message);
            return redirectMessage('success','با موفقیت ثبت شد',null,route('client.tickets.edit',$ticket->id));
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }

    public function edit(Ticket $ticket,Request $request)
    {
        Gate::authorize('view',$ticket);
        $ticket = new TicketResource($ticket);
        return inertia('Client/Tickets/Edit', compact('ticket'));
    }

    public function update(Ticket $ticket,Request $request)
    {
        Gate::authorize('update',$ticket);
        try {
            $request->validate([
                'content' => 'required|min:5'
            ],
            [
                'content' => 'پیام را وارد کنید'
            ]);
            (new TicketService)->response($ticket,$request->user()->id,$request->get('content'));
            (new TicketService)->updateStatus($ticket,'PENDING');
            return redirectMessage('success', 'پیام با موفقیت ارسال شد');
        }
        catch (ValidationException $e){
            return redirectMessage('error',array_values($e->errors())[0]);
        }
        catch (\Exception $e){
            return redirectMessage('error',$e->getMessage());
        }
    }
}
