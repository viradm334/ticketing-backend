<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignAgentToTicketRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user', 'agent')->paginate(10);

        return ApiResponse::resource(TicketResource::collection($tickets));
    }

    public function assign(AssignAgentToTicketRequest $request, string $id)
    {
        $data = $request->validated();

        $ticket = Ticket::findOrFail($id);

        $agent = User::findOrFail($data['agent_id']);

        if (!$agent->isAgent()) {
            return ApiResponse::error("Selected user must be an agent");
        }

        $ticket->update([
            'agent_id' => $agent->id
        ]);

        $ticket->load(['agent', 'user']);

        return ApiResponse::resource(new TicketResource($ticket), "Successfully assigned agent to ticket");
    }
}
