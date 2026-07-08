<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $recipientRole
    ) {
        //
    }

    public function envelope(): Envelope
    {
        $subject = $this->recipientRole === 'agent'
            ? "You've been assigned to ticket #{$this->ticket->id}"
            : "An agent has been assigned to your ticket #{$this->ticket->id}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tickets.assigned',
            with: [
                'ticket' => $this->ticket,
                'recipientRole' => $this->recipientRole,
            ]
        );
    }
}
