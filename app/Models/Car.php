<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand',
        'model', 
        'year',
        'mileage',
        'color',
        'body_type',
        'image',
        'detailed_description',
        'user_id'
    ];

    // Мутаторы
    public function setDetailedDescriptionAttribute($value)
    {
        // Очищаем от HTML тегов
        $this->attributes['detailed_description'] = strip_tags($value);
    }

    public function getFormattedMileageAttribute()
    {
        return number_format($this->mileage, 0, '.', ' ') . ' км';
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Пробуем найти файл в разных местах
            $filename = basename($this->image);
            
            // 1. В public/storage/cars/
            $publicPath = 'storage/cars/' . $filename;
            if (file_exists(public_path($publicPath))) {
                return asset($publicPath);
            }
            
            // 2. В storage/app/public/cars/
            $storagePath = storage_path('app/public/cars/' . $filename);
            if (file_exists($storagePath)) {
                // Копируем в public/storage/cars/
                $destPath = public_path('storage/cars/' . $filename);
                if (!file_exists($destPath)) {
                    copy($storagePath, $destPath);
                }
                return asset('storage/cars/' . $filename);
            }
            
            if (strpos($this->image, 'cars/') === 0) {
                $cleanName = substr($this->image, 5);
                return asset('storage/cars/' . $cleanName);
            }
        }
        
        return asset('images/no-image.jpg');
    }

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== ЛАБОРАТОРНАЯ РАБОТА №5 ==========
    
    // Связь с комментариями
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // Проверка, является ли пользователь владельцем
    public function isOwnedBy(User $user)
    {
        return $this->user_id === $user->id;
    }

    // Получить комментарии от друзей текущего пользователя
    public function commentsFromFriends()
    {
        if (!auth()->check()) {
            return collect(); // Пустая коллекция для гостей
        }
        
        $currentUser = auth()->user();
        $friendIds = $currentUser->allFriends()->pluck('id');
        
        return $this->comments()
            ->whereIn('user_id', $friendIds)
            ->get();
    }

    // Events/Closures для проверки прав на уровне модели
    protected static function booted()
    {
        // Closure для проверки доступа при сохранении
        static::saving(function ($car) {
            // Если автомобиль уже существует
            if ($car->exists) {
                $user = Auth::user();
                
                // Проверяем права через Gate
                if ($user && !$user->can('update', $car)) {
                    abort(403, 'У вас нет прав для изменения этого автомобиля');
                }
            }
        });

        // Closure для проверки доступа при удалении
        static::deleting(function ($car) {
            $user = Auth::user();
            
            // Если мягкое удаление и пользователь не админ
            if (!$car->isForceDeleting()) {
                if ($user && !$user->can('delete', $car)) {
                    abort(403, 'У вас нет прав для удаления этого автомобиля');
                }
            }
            // Если полное удаление
            else {
                if ($user && !$user->can('forceDelete', $car)) {
                    abort(403, 'У вас нет прав для полного удаления этого автомобиля');
                }
            }
        });
    }
}