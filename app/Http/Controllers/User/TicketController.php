<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Helpers\ApiResponse;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('user')->where('user_id', auth()->id())->paginate(10);

        return ApiResponse::resource(TicketResource::collection($tickets), "Successfully get tickets");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        $ticket = Ticket::create($data);

        return ApiResponse::resource(new TicketResource($ticket), "Successfully submitted a new ticket");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);

        return ApiResponse::resource(new TicketResource($ticket));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, string $id)
    {
        $data = $request->validated();

        $ticket = Ticket::findOrFail($id);

        $ticket->update($data);

        return ApiResponse::resource(new TicketResource($ticket), "Successfully updated ticket");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->delete();

        return ApiResponse::success("Successfully deleted ticket");
    }
}
