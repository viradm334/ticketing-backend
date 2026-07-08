<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignAgentToTicketRequest;
use App\Http\Requests\Admin\UpdateTicketStatusRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\User;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user', 'agent')->paginate(10);

        return ApiResponse::resource(TicketResource::collection($tickets));
    }

    public function metrics()
    {
        $openCount = Ticket::open()->count();
        $inProgressCount = Ticket::inProgress()->count();
        $closedCount = Ticket::closed()->count();

        $data = [
            'open_tickets_count' => $openCount,
            'in_progress_tickets_count' => $inProgressCount,
            'closed_tickets_count' => $closedCount,
        ];

        return ApiResponse::success("Successfully get ticket metrics", $data);
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

        ActivityLogger::log(
            ActivityLog::TYPE_TICKET_ASSIGNED,
            "Ticket #{$ticket->id} was assigned to {$agent->name}",
            metadata: ['agent_id' => $agent->id, 'agent_name' => $agent->name],
            ticketId: $ticket->id
        );

        $ticket->load(['agent', 'user']);

        return ApiResponse::resource(new TicketResource($ticket), "Successfully assigned agent to ticket");
    }

    public function updateStatus(UpdateTicketStatusRequest $request, string $id)
    {
        $data = $request->validated();

        $ticket = Ticket::findOrFail($id);

        $originalStatus = $ticket->status;
        $originalPriority = $ticket->priority;

        $ticket->update($data);

        if ($ticket->wasChanged('status')) {
            ActivityLogger::log(
                ActivityLog::TYPE_TICKET_STATUS_CHANGED,
                "Ticket #{$ticket->id} status changed to {$ticket->status}",
                metadata: ['from' => $originalStatus, 'to' => $ticket->status],
                ticketId: $ticket->id
            );
        }

        if ($ticket->wasChanged('priority')) {
            ActivityLogger::log(
                ActivityLog::TYPE_TICKET_PRIORITY_CHANGED,
                "Ticket #{$ticket->id} priority changed to {$ticket->priority}",
                metadata: ['from' => $originalPriority, 'to' => $ticket->priority],
                ticketId: $ticket->id
            );
        }

        $ticket->load(['agent', 'user']);

        return ApiResponse::resource(new TicketResource($ticket), "Successfully updated ticket");
    }
}
