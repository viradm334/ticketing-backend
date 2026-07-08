<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const TYPE_USER_REGISTERED = 'user_registered';
    const TYPE_TICKET_CREATED = 'ticket_created';
    const TYPE_TICKET_ASSIGNED = 'ticket_assigned';
    const TYPE_AGENT_CREATED = 'agent_created';
    const TYPE_TICKET_STATUS_CHANGED = 'ticket_status_changed';
    const TYPE_TICKET_PRIORITY_CHANGED = 'ticket_priority_changed';
    const TYPE_COMMENT_CREATED = 'comment_created';

    protected $fillable = [
        'type',
        'description',
        'user_id',
        'metadata',
        'ticket_id'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
