<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebTokenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Получаем токены пользователя напрямую из БД
        $tokens = $user->tokens()->get()->map(function($token) {
            // Безопасное форматирование даты
            $createdAt = $token->created_at;
            $formattedDate = null;
            
            if ($createdAt) {
                // Если это строка, преобразуем в Carbon
                if (is_string($createdAt)) {
                    try {
                        $createdAt = \Carbon\Carbon::parse($createdAt);
                    } catch (\Exception $e) {
                        // Если не удалось распарсить, оставляем как есть
                    }
                }
                
                // Если это объект Carbon/DateTime, форматируем
                if ($createdAt instanceof \DateTime || $createdAt instanceof \Carbon\Carbon) {
                    $formattedDate = $createdAt->format('d.m.Y H:i');
                } elseif (is_string($createdAt)) {
                    $formattedDate = $createdAt; // Оставляем строку как есть
                }
            }
            
            return [
                'id' => $token->id,
                'name' => $token->name,
                'created_at' => $formattedDate ?: 'Н/Д',
                'revoked' => (bool) $token->revoked,
            ];
        });
        
        return view('profile.tokens', [
            'tokens' => $tokens,
        ]);
    }
    
    public function createToken(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        try {
            $tokenResult = $request->user()->createToken($request->name);
            $token = $tokenResult->accessToken;
            
            // Сохраняем в сессии для показа
            session()->flash('new_token', $token);
            session()->flash('token_name', $request->name);
            
            return redirect()->route('profile.tokens')
                ->with('success', 'Токен успешно создан! Сохраните его, он покажется только один раз.');
                
        } catch (\Exception $e) {
            return redirect()->route('profile.tokens')
                ->with('error', 'Ошибка создания токена: ' . $e->getMessage());
        }
    }
    
    public function deleteToken(Request $request, $id)
    {
        try {
            $token = $request->user()->tokens()->find($id);
            
            if (!$token) {
                return redirect()->route('profile.tokens')
                    ->with('error', 'Токен не найден');
            }
            
            $token->delete();
            
            return redirect()->route('profile.tokens')
                ->with('success', 'Токен удален');
                
        } catch (\Exception $e) {
            return redirect()->route('profile.tokens')
                ->with('error', 'Ошибка удаления токена: ' . $e->getMessage());
        }
    }
}