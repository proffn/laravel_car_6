<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at->toDateTimeString(),
            
            // Статистика
            'cars_count' => $this->whenLoaded('cars', $this->cars->count()),
            'comments_count' => $this->whenLoaded('comments', $this->comments->count()),
            
            // Признак "друга" (для текущего пользователя)
            'is_friend' => $request->user() ? $request->user()->isFriendWith($this->resource) : false,
            
            // Мета-данные
            'links' => [
                'self' => route('api.users.show', $this->id),
                'cars' => route('api.users.cars.index', $this->id),
            ]
        ];
    }
}