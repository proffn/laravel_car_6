<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean', 
    ];

    // Связь с автомобилями
    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    // ========== ЛАБОРАТОРНАЯ РАБОТА №5 ==========
    
    // Связь с комментариями
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Запросы дружбы, которые пользователь отправил
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    // Запросы дружбы, которые пользователь получил
    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    // Друзья пользователя (принятые запросы, где пользователь отправитель)
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', Friendship::STATUS_ACCEPTED)
            ->withTimestamps();
    }

    // Пользователи, которые в друзьях у текущего пользователя (где пользователь получатель)
    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', Friendship::STATUS_ACCEPTED)
            ->withTimestamps();
    }

    // Все дружеские отношения (двусторонние, уникальные)
    public function allFriends()
    {
        $friends = $this->friends;
        $friendOf = $this->friendOf;
        
        return $friends->merge($friendOf)->unique('id');
    }

    // Проверка, является ли пользователь другом
    public function isFriendWith(User $user)
    {
        if (!$user) return false;
        
        return $this->friends()->where('friend_id', $user->id)->exists() ||
               $this->friendOf()->where('user_id', $user->id)->exists();
    }

    // Получить количество входящих запросов дружбы
    public function pendingFriendRequestsCount()
    {
        return $this->receivedFriendRequests()
            ->where('status', Friendship::STATUS_PENDING)
            ->count();
    }

    // Проверка, есть ли ожидающий запрос дружбы от пользователя
    public function hasPendingFriendRequestFrom(User $user)
    {
        if (!$user) return false;
        
        return $this->receivedFriendRequests()
            ->where('user_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->exists();
    }

    // Проверка, отправил ли пользователь запрос дружбы
    public function hasSentFriendRequestTo(User $user)
    {
        if (!$user) return false;
        
        return $this->sentFriendRequests()
            ->where('friend_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->exists();
    }

    // Проверка, являются ли пользователи друзьями (взаимно)
    public function areMutualFriends(User $user)
    {
        if (!$user) return false;
        
        return $this->isFriendWith($user) && $user->isFriendWith($this);
    }

    // Получить ленту автомобилей друзей
    public function friendsCarsFeed($limit = 20)
    {
        $friendIds = $this->allFriends()->pluck('id');
        
        return Car::whereIn('user_id', $friendIds)
            ->with(['user', 'comments'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    // Получить запросы дружбы, ожидающие подтверждения
    public function pendingFriendRequests()
    {
        return $this->receivedFriendRequests()
            ->where('status', Friendship::STATUS_PENDING)
            ->with('user')
            ->get();
    }
}