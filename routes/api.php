<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Тестовый маршрут
Route::get('/test', function() {
    return response()->json(['status' => 'API работает!']);
});

// Публичные маршруты (не требуют аутентификации)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Защищенные маршруты (требуют аутентификации через Passport)
Route::middleware('auth:api')->group(function () {
    // Текущий пользователь
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'friends_count' => $user->allFriends()->count(),
                'cars_count' => $user->cars()->count(),
                'created_at' => $user->created_at,
            ],
            // OAuth информация для выполнения задания
            'oauth_info' => [
                'personal_access_client_id' => 1,
                'password_grant_client_id' => 2,
                'note' => 'Используйте токен в заголовке Authorization: Bearer {ваш_токен}'
            ]
        ]);
    })->name('api.user.profile');
    
    // Выход
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Управление токенами (для выполнения задания - пользователь видит свои токены)
    Route::prefix('user')->group(function () {
        Route::get('/tokens', [AuthController::class, 'getTokens'])->name('api.user.tokens');
        Route::post('/tokens/create', [AuthController::class, 'createToken'])->name('api.user.tokens.create');
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'deleteToken'])->name('api.user.tokens.delete');
    });
    
    // Маршрут для просмотра других пользователей (нужен для api.users.show)
    Route::get('/users/{user}', function ($userId) {
        $user = \App\Models\User::with(['cars', 'comments'])->findOrFail($userId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'cars_count' => $user->cars()->count(),
                'friends_count' => $user->allFriends()->count(),
                'comments_count' => $user->comments()->count(),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    })->name('api.users.show');
    
    // Автомобили (Cars) - основная сущность
    Route::apiResource('cars', CarController::class)->names([
        'index' => 'api.cars.index',
        'store' => 'api.cars.store',
        'show' => 'api.cars.show',
        'update' => 'api.cars.update', 
        'destroy' => 'api.cars.destroy'
    ]);
    
    // Комментарии (Comments) - вспомогательная сущность
    Route::apiResource('comments', CommentController::class)->except(['index'])->names([
        'store' => 'api.comments.store',
        'show' => 'api.comments.show',
        'update' => 'api.comments.update',
        'destroy' => 'api.comments.destroy'
    ]);
    
    // Комментарии для конкретного автомобиля (GET и POST)
    Route::prefix('cars/{car}')->group(function () {
        Route::get('/comments', [CommentController::class, 'indexByCar'])->name('api.cars.comments.index');
        Route::post('/comments', [CommentController::class, 'storeForCar'])->name('api.cars.comments.store');
    });
    
    // Друзья (для проверки признака "друга")
    Route::prefix('friends')->group(function () {
        Route::get('/', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => [
                    'friends' => $request->user()->allFriends()->map(function ($friend) {
                        return [
                            'id' => $friend->id,
                            'name' => $friend->name,
                            'email' => $friend->email,
                            'cars_count' => $friend->cars()->count(),
                        ];
                    }),
                    'friends_count' => $request->user()->allFriends()->count(),
                    'pending_requests_count' => $request->user()->pendingFriendRequestsCount(),
                    'has_friends' => $request->user()->allFriends()->count() > 0,
                ]
            ]);
        })->name('api.friends.index');
        
        // Маршруты для управления друзьями (опционально)
        Route::post('/add/{friendId}', function (Request $request, $friendId) {
            $friend = \App\Models\User::findOrFail($friendId);
            
            // Проверка, не является ли уже другом
            if ($request->user()->isFriendWith($friend)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь уже в друзьях'
                ], 400);
            }
            
            // Создание запроса дружбы
            \App\Models\Friendship::create([
                'user_id' => $request->user()->id,
                'friend_id' => $friend->id,
                'status' => \App\Models\Friendship::STATUS_PENDING,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Запрос дружбы отправлен',
                'friend' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                ]
            ]);
        })->name('api.friends.add');
        
        Route::post('/accept/{friendshipId}', function (Request $request, $friendshipId) {
            $friendship = \App\Models\Friendship::findOrFail($friendshipId);
            
            // Проверка прав
            if ($friendship->friend_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не можете принять этот запрос'
                ], 403);
            }
            
            $friendship->update([
                'status' => \App\Models\Friendship::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Запрос дружбы принят',
                'friend' => [
                    'id' => $friendship->user->id,
                    'name' => $friendship->user->name,
                ]
            ]);
        })->name('api.friends.accept');
    });
    
    // Статистика (опционально)
    Route::get('/stats', function (Request $request) {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user_stats' => [
                    'total_cars' => $user->cars()->count(),
                    'total_comments' => $user->comments()->count(),
                    'total_friends' => $user->allFriends()->count(),
                    'pending_friend_requests' => $user->pendingFriendRequestsCount(),
                ],
                'system_stats' => [
                    'total_users' => \App\Models\User::count(),
                    'total_cars' => \App\Models\Car::count(),
                    'total_comments' => \App\Models\Comment::count(),
                ]
            ]
        ]);
    })->name('api.stats');
});