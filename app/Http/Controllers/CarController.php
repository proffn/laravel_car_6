<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // Главная страница - все автомобили для всех пользователей
    public function index()
    {
        // По заданию: все пользователи (гости, обычные, админы) видят все машины
        $cars = Car::with('user')->latest()->get();
        
        return view('cars.index', [
            'cars' => $cars,
            'header' => 'Автомобили'
        ]);
    }

    // Список всех пользователей
    public function users()
    {
        $users = User::withCount('cars')->get();
        
        return view('users.index', [
            'users' => $users,
            'header' => 'Список пользователей'
        ]);
    }

    // Автомобили конкретного пользователя по username
    public function userCars($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        $cars = $user->cars()->latest()->get();
        
        return view('cars.user-index', [
            'cars' => $cars,
            'user' => $user,
            'header' => 'Автомобили пользователя: ' . $user->name
        ]);
    }

    // Форма создания
    public function create()
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для добавления автомобиля нужно войти в систему');
        }
        
        return view('cars.create', [
            'header' => 'Добавить автомобиль'
        ]);
    }

    // Сохранение нового автомобиля
    public function store(Request $request)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для добавления автомобиля нужно войти в систему');
        }
        
        $validated = $request->validate([
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'color' => 'required|max:50',
            'body_type' => 'required|in:Седан,Универсал,Хэтчбек,Внедорожник,Купе,Минивэн,Пикап',
            'image' => 'nullable|image|max:2048',
            'detailed_description' => 'required',
        ]);

        // Обработка изображения
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $validated['image'] = $path;
        }

        // Добавляем user_id текущего пользователя
        $validated['user_id'] = auth()->id();
        
        Car::create($validated);
        
        return redirect()->route('cars.index')->with('success', 'Автомобиль успешно добавлен!');
    }

    // Просмотр деталей
    public function show(Car $car)
    {
        return view('cars.show', [
            'car' => $car,
            'header' => $car->brand . ' ' . $car->model
        ]);
    }

    // Форма редактирования
    public function edit(Car $car)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для редактирования нужно войти в систему');
        }
        
        // Проверяем права: редактировать может только владелец или администратор
        if ($car->user_id !== auth()->id() && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'У вас нет прав для редактирования этого автомобиля');
        }
        
        return view('cars.edit', [
            'car' => $car,
            'header' => 'Редактировать: ' . $car->brand . ' ' . $car->model
        ]);
    }

    // Обновление
    public function update(Request $request, Car $car)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для редактирования нужно войти в систему');
        }
        
        // Проверяем права: редактировать может только владелец или администратор
        if ($car->user_id !== auth()->id() && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'У вас нет прав для редактирования этого автомобиля');
        }
        
        $validated = $request->validate([
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'color' => 'required|max:50',
            'body_type' => 'required|in:Седан,Универсал,Хэтчбек,Внедорожник,Купе,Минивэн,Пикап',
            'image' => 'nullable|image|max:2048',
            'detailed_description' => 'required',
        ]);

        // Обновление изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }
            
            $path = $request->file('image')->store('cars', 'public');
            $validated['image'] = $path;
        }

        $car->update($validated);
        
        return redirect()->route('cars.show', $car)->with('success', 'Автомобиль успешно обновлен!');
    }

    // Удаление (мягкое)
    public function destroy(Car $car)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для удаления нужно войти в систему');
        }
        
        // Проверяем права: удалять может только владелец или администратор
        if ($car->user_id !== auth()->id() && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'У вас нет прав для удаления этого автомобиля');
        }
        
        $car->delete();
        
        return redirect()->route('cars.index')->with('success', 'Автомобиль удален!');
    }

    // Корзина (только для админа)
    public function trash()
    {
        // Проверяем что пользователь админ
        if (!auth()->check() || !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Доступ только для администраторов');
        }
        
        $cars = Car::onlyTrashed()->with('user')->get();
        
        return view('cars.trash', [
            'cars' => $cars,
            'header' => 'Корзина удаленных автомобилей'
        ]);
    }

    // Восстановление
    public function restore($id)
    {
        // Проверяем что пользователь админ
        if (!auth()->check() || !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Доступ только для администраторов');
        }
        
        $car = Car::withTrashed()->findOrFail($id);
        $car->restore();
        
        return redirect()->route('cars.trash')->with('success', 'Автомобиль восстановлен!');
    }

    // Полное удаление
    public function forceDelete($id)
    {
        // Проверяем что пользователь админ
        if (!auth()->check() || !(auth()->user()->is_admin ?? false)) {
            abort(403, 'Доступ только для администраторов');
        }
        
        $car = Car::withTrashed()->findOrFail($id);
        
        // Удаляем изображение
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }
        
        $car->forceDelete();
        
        return redirect()->route('cars.trash')->with('success', 'Автомобиль полностью удален!');
    }
}