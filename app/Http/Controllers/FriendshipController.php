<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    // Список друзей
    public function index()
    {
        $user = auth()->user();
        $friends = $user->allFriends();
        $pendingRequests = $user->pendingFriendRequests();
        
        return view('friends.index', [
            'friends' => $friends,
            'pendingRequests' => $pendingRequests,
            'header' => 'Мои друзья'
        ]);
    }

    // Список запросов в друзья
    public function requests()
    {
        $user = auth()->user();
        $pendingRequests = $user->pendingFriendRequests();
        
        return view('friends.requests', [
            'pendingRequests' => $pendingRequests,
            'header' => 'Запросы в друзья'
        ]);
    }

    // Отправить запрос в друзья
    public function sendRequest(User $user)
    {
        $currentUser = auth()->user();
        
        // Проверяем, не пытаемся ли добавить самого себя
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'Нельзя добавить самого себя в друзья');
        }
        
        // Проверяем, не отправлен ли уже запрос
        if ($currentUser->hasSentFriendRequestTo($user)) {
            return redirect()->back()->with('info', 'Запрос в друзья уже отправлен');
        }
        
        // Проверяем, не являются ли уже друзьями
        if ($currentUser->isFriendWith($user)) {
            return redirect()->back()->with('info', 'Вы уже друзья с этим пользователем');
        }
        
        // Создаем запрос в друзья
        Friendship::create([
            'user_id' => $currentUser->id,
            'friend_id' => $user->id,
            'status' => Friendship::STATUS_PENDING
        ]);
        
        return redirect()->back()->with('success', 'Запрос в друзья отправлен!');
    }

    // Принять запрос в друзья
    public function acceptRequest(Friendship $friendship)
    {
        // Проверяем, что текущий пользователь получатель запроса
        if ($friendship->friend_id !== auth()->id()) {
            abort(403, 'Вы не можете принять этот запрос');
        }
        
        // Принимаем запрос (автоматически создаст обоюдную дружбу)
        $friendship->accept();
        
        return redirect()->back()->with('success', 'Запрос в друзья принят!');
    }

    // Отклонить запрос в друзья
    public function rejectRequest(Friendship $friendship)
    {
        // Проверяем, что текущий пользователь получатель запроса
        if ($friendship->friend_id !== auth()->id()) {
            abort(403, 'Вы не можете отклонить этот запрос');
        }
        
        $friendship->reject();
        
        return redirect()->back()->with('success', 'Запрос в друзья отклонен');
    }

    // Удалить из друзей
    public function removeFriend(Friendship $friendship)
    {
        $currentUser = auth()->user();
        
        // Проверяем, что текущий пользователь участник дружбы
        if ($friendship->user_id !== $currentUser->id && $friendship->friend_id !== $currentUser->id) {
            abort(403, 'Вы не можете удалить эту дружбу');
        }
        
        $friendship->remove();
        
        return redirect()->back()->with('success', 'Пользователь удален из друзей');
    }
}