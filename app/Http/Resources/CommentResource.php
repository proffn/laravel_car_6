<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();
        
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'time_ago' => $this->created_at->diffForHumans(),
            
            // Связи
            'user' => new UserResource($this->whenLoaded('user')),
            'car' => new CarResource($this->whenLoaded('car')),
            'car_id' => $this->car_id,
            
            // Признак "друга" 
            'is_from_friend' => $user ? $this->isFromFriend() : false,
            'is_owner' => $user ? $user->id === $this->user_id : false,
            
            // Мета-данные
            'links' => [
                'self' => route('api.comments.show', $this->id),
                'car' => route('api.cars.show', $this->car_id),
                'user' => route('api.users.show', $this->user_id),
            ]
        ];
    }
}