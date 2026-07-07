<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignAgentToTicketRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->paginate(10);

        return ApiResponse::collection(TicketResource::collection($tickets));
    }

    public function assign(AssignAgentToTicketRequest $request)
    {
        $data = $request->validated();

        $ticket = Ticket::findOrFail($data['ticket_id']);

        $ticket->update([
            'agent_id' => $data['agent_id']
        ]);

        $ticket->load(['agent', 'user']);

        return ApiResponse::collection(new TicketResource($ticket), "Successfully assigned agent to ticket");
    }
}
