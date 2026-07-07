<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'agent' => $this->whenLoaded('agent', fn() => new UserResource($this->agent)),
            'comments' => $this->whenLoaded('comments', fn() => CommentResource::collection($this->comments))
        ];
    }
}
