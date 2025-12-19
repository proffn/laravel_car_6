<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();
        $carUser = $this->whenLoaded('user') ? $this->user : null;
        
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'formatted_mileage' => $this->mileage ? number_format($this->mileage, 0, '', ' ') . ' км' : '0 км',
            'color' => $this->color,
            'body_type' => $this->body_type,
            'detailed_description' => $this->detailed_description,
            'image_url' => $this->image_url ?? null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            
            // Связи с проверкой на существование
            'user' => $carUser ? [
                'id' => $carUser->id,
                'name' => $carUser->name,
                'email' => $carUser->email,
            ] : null,
            
            'comments' => $this->whenLoaded('comments', function() {
                return $this->comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => $comment->user ? [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ] : null,
                        'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : null,
                    ];
                });
            }, []),
            
            'comments_count' => $this->whenLoaded('comments', function() {
                return $this->comments->count();
            }, 0),
            
            // Признак "друга" (расширенный уровень - КРИТИЧЕСКИ ВАЖНО для лабы)
            'is_friend' => $user && $carUser ? $user->isFriendWith($carUser) : false,
            'is_owner' => $user && $carUser ? $user->id === $carUser->id : false,
            
            // Мета-данные БЕЗ route() чтобы избежать ошибок
            'links' => [
                'self' => url("/api/cars/{$this->id}"),
                'comments' => url("/api/cars/{$this->id}/comments"),
            ]
        ];
    }
}