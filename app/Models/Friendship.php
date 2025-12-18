<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    // Статусы дружбы
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_BLOCKED = 'blocked';

    // Пользователь, который отправил запрос
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Пользователь, который получил запрос
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    // Проверка, является ли дружба обоюдной
    public function isMutual()
    {
        return Friendship::where('user_id', $this->friend_id)
            ->where('friend_id', $this->user_id)
            ->where('status', self::STATUS_ACCEPTED)
            ->exists();
    }
    
    // Принять запрос дружбы
    public function accept()
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->save();
        
        // Создаем обоюдную дружбу автоматически (если еще нет)
        Friendship::firstOrCreate([
            'user_id' => $this->friend_id,
            'friend_id' => $this->user_id,
        ], [
            'status' => self::STATUS_ACCEPTED
        ]);
        
        return $this;
    }
    
    // Отклонить запрос дружбы
    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
        
        return $this;
    }
    
    // Удалить из друзей
    public function remove()
    {
        // Удаляем обе связи (двустороннюю)
        Friendship::where(function($query) {
            $query->where('user_id', $this->user_id)
                  ->where('friend_id', $this->friend_id);
        })->orWhere(function($query) {
            $query->where('user_id', $this->friend_id)
                  ->where('friend_id', $this->user_id);
        })->delete();
        
        return true;
    }
    
    // Проверка, является ли запрос ожидающим
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    // Проверка, принята ли дружба
    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }
    
    // Scope для получения ожидающих запросов
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    
    // Scope для получения принятых запросов
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }
}