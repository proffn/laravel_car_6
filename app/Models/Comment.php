<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'car_id',
        'user_id',
        'content'
    ];

    protected $with = ['user']; // Всегда загружаем пользователя с комментарием

    // Связь с автомобилем
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    // Связь с пользователем (кто оставил комментарий)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Проверка, является ли комментарий от друга текущего пользователя
    public function isFromFriend()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $currentUser = auth()->user();
        $commentUser = $this->user;
        
        if (!$commentUser) {
            return false;
        }
        
        // Проверяем, являются ли пользователи друзьями
        return $currentUser->isFriendWith($commentUser);
    }

    // Проверка прав: редактировать может только автор комментария
    public function isOwnedBy(User $user)
    {
        return $this->user_id === $user->id;
    }

    // Accessor для форматированного времени создания
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Scope для получения комментариев к автомобилю
    public function scopeForCar($query, $carId)
    {
        return $query->where('car_id', $carId);
    }

    // Scope для получения комментариев пользователя
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}