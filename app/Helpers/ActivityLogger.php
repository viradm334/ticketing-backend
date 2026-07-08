<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $type,
        string $description,
        array $metadata = [],
        ?int $ticketId = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'ticket_id' => $ticketId,
            'user_id' => $userId ?? Auth::id(),
        ]);
    }
}
