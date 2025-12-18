<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\FeedController;

// Главная страница - перенаправляем на автомобили
Route::get('/', function () {
    return redirect()->route('cars.index');
});

// Дашборд Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Маршруты аутентификации Breeze
require __DIR__.'/auth.php';

// Маршруты автомобилей
Route::resource('cars', CarController::class);

//  Список всех пользователей
Route::get('/users', [CarController::class, 'users'])->name('users.index');

//  Автомобили конкретного пользователя по username
Route::get('users/{username}/cars', [CarController::class, 'userCars'])
    ->name('users.cars');

// Маршруты для администратора с использованием Gates
Route::middleware(['auth', 'can:admin-access'])->group(function () {
    Route::get('admin/trash', [CarController::class, 'trash'])->name('cars.trash');
    Route::post('admin/cars/{id}/restore', [CarController::class, 'restore'])->name('cars.restore');
    Route::delete('admin/cars/{id}/force-delete', [CarController::class, 'forceDelete'])->name('cars.force-delete'); 
});



//  ЛАБОРАТОРНАЯ РАБОТА №5 

// Маршруты для комментариев
Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);

// Маршруты для дружбы
Route::prefix('friends')->name('friends.')->middleware('auth')->group(function () {
    Route::get('/', [FriendshipController::class, 'index'])->name('index');
    Route::post('/{user}/add', [FriendshipController::class, 'sendRequest'])->name('send');
    Route::post('/{friendship}/accept', [FriendshipController::class, 'acceptRequest'])->name('accept');
    Route::post('/{friendship}/reject', [FriendshipController::class, 'rejectRequest'])->name('reject');
    Route::delete('/{friendship}/remove', [FriendshipController::class, 'removeFriend'])->name('remove');
    Route::get('/requests', [FriendshipController::class, 'requests'])->name('requests');
});

// Лента друзей
Route::get('/feed', [FeedController::class, 'index'])->middleware('auth')->name('feed');