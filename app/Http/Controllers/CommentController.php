<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    // Хранение нового комментария
    public function store(Request $request)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для добавления комментария нужно войти в систему');
        }

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'content' => 'required|string|min:3|max:1000',
        ]);

        // Добавляем user_id текущего пользователя
        $validated['user_id'] = auth()->id();
        
        $comment = Comment::create($validated);
        
        return redirect()->route('cars.show', $comment->car_id)
            ->with('success', 'Комментарий успешно добавлен!');
    }

    // Обновление комментария
    public function update(Request $request, Comment $comment)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для редактирования комментария нужно войти в систему');
        }

        // Проверяем права: редактировать может только автор комментария
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'У вас нет прав для редактирования этого комментария');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        $comment->update($validated);
        
        return redirect()->route('cars.show', $comment->car_id)
            ->with('success', 'Комментарий успешно обновлен!');
    }

    // Удаление комментария
    public function destroy(Comment $comment)
    {
        // Проверяем авторизацию
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для удаления комментария нужно войти в систему');
        }

        // Проверяем права: удалять может только автор комментария или администратор
        if ($comment->user_id !== auth()->id() && !(auth()->user()->is_admin ?? false)) {
            abort(403, 'У вас нет прав для удаления этого комментария');
        }

        $carId = $comment->car_id;
        $comment->delete();
        
        return redirect()->route('cars.show', $carId)
            ->with('success', 'Комментарий удален!');
    }
}