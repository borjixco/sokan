<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create($userId,$categoryId,$subject,$message,$status = 'PENDING')
    {
        try {
            return DB::transaction(function () use($userId,$categoryId,$subject,$message,$status){
                $ticket = Ticket::create([
                    'user_id' => $userId,
                    'subject' => $subject,
                    'status'  => $status,
                ]);
                $ticket->messages()->create([
                    'user_id' => $userId,
                    'message' => $message,
                ]);
                $ticket->categories()->attach([$categoryId]);
                return $ticket;
            });
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function response($ticket,$userId,$message)
    {
        return $ticket->messages()->create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    public function updateStatus($ticket,$status)
    {
        return $ticket->update(['status' => $status]);
    }
}
