<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Car;
use Illuminate\Auth\Access\HandlesAuthorization;

class CarPolicy
{
    use HandlesAuthorization;

    // Все могут просматривать список
    public function viewAny(User $user)
    {
        return true; // Разрешаем всем
    }

    // Все могут просматривать детали
    public function view(User $user, Car $car)
    {
        return true; // Разрешаем всем
    }

    // Только авторизованные могут создавать
    public function create(User $user)
    {
        return auth()->check(); // Разрешаем авторизованным
    }

    // Редактировать может владелец или администратор
    public function update(User $user, Car $car)
    {
        // Если автомобиль без владельца (старые записи)
        if (!$car->user_id) {
            return true; // Разрешаем всем
        }
        
        return $user->id === $car->user_id || ($user->is_admin ?? false);
    }

    // Удалять может владелец или администратор
    public function delete(User $user, Car $car)
    {
        // Если автомобиль без владельца (старые записи)
        if (!$car->user_id) {
            return true; // Разрешаем всем
        }
        
        return $user->id === $car->user_id || ($user->is_admin ?? false);
    }

    // Восстанавливать может только администратор
    public function restore(User $user, Car $car)
    {
        return $user->is_admin ?? false;
    }

    // Полностью удалять может только администратор
    public function forceDelete(User $user, Car $car)
    {
        return $user->is_admin ?? false;
    }
}