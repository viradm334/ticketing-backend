<x-mail::message>
@if ($recipientRole === 'agent')
# You've been assigned a ticket

Hi {{ $ticket->agent->name }},

You've been assigned to a ticket submitted by **{{ $ticket->user->name }}**.
@else
# An agent has been assigned to your ticket

Hi {{ $ticket->user->name }},

**{{ $ticket->agent->name }}** has been assigned to your ticket and will be in touch soon.
@endif

<x-mail::panel>
**#{{ $ticket->id }} &mdash; {{ $ticket->title }}**

{{ $ticket->description }}
</x-mail::panel>

| | |
|:--|:--|
| **Priority** | {{ ucfirst($ticket->priority) }} |
| **Status** | {{ ucfirst(str_replace('_', ' ', $ticket->status)) }} |

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
