<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // GET /api/cars/{car}/comments - список комментариев для автомобиля
    public function indexByCar(Request $request, $carId)
    {
        $car = Car::findOrFail($carId);
        $user = $request->user();
        
        $comments = Comment::with(['user', 'car'])
            ->where('car_id', $carId)
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'car' => [
                    'id' => $car->id,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'year' => $car->year,
                ],
                'comments' => $comments->map(function($comment) use ($user) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => $comment->user ? [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'email' => $comment->user->email,
                            // Признак "друга" для комментариев
                            'is_friend' => $user ? $user->isFriendWith($comment->user) : false,
                        ] : null,
                        'car' => $comment->car ? [
                            'id' => $comment->car->id,
                            'brand' => $comment->car->brand,
                            'model' => $comment->car->model,
                        ] : null,
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'meta' => [
                    'total_comments' => $comments->count(),
                    'car_owner' => $car->user ? [
                        'id' => $car->user->id,
                        'name' => $car->user->name,
                        'is_friend' => $user ? $user->isFriendWith($car->user) : false,
                    ] : null,
                ]
            ]
        ]);
    }

    // POST /api/cars/{car}/comments - создать комментарий для автомобиля
    public function storeForCar(Request $request, $carId)
    {
        $car = Car::findOrFail($carId);
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'car_id' => $carId,
        ]);

        // Загружаем связи для ответа
        $comment->load(['user', 'car']);
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'email' => $comment->user->email,
                ],
                'car' => [
                    'id' => $comment->car->id,
                    'brand' => $comment->car->brand,
                    'model' => $comment->car->model,
                    'year' => $comment->car->year,
                ],
                // Признак "друга" - расширенный уровень
                'is_owner_friend' => $user ? $user->isFriendWith($car->user) : false,
                'is_comment_owner' => true, // Текущий пользователь - автор комментария
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    // GET /api/comments/{id} - показать комментарий
    public function show($id)
    {
        $comment = Comment::with(['user', 'car'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ] : null,
                'car' => $comment->car ? [
                    'id' => $comment->car->id,
                    'brand' => $comment->car->brand,
                    'model' => $comment->car->model,
                ] : null,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
            ]
        ]);
    }

    // POST /api/comments - создать комментарий (общий)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:1000',
            'car_id' => 'required|exists:cars,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'car_id' => $request->car_id,
        ]);

        $comment->load(['user', 'car']);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ] : null,
                'car' => $comment->car ? [
                    'id' => $comment->car->id,
                    'brand' => $comment->car->brand,
                    'model' => $comment->car->model,
                ] : null,
                'created_at' => $comment->created_at,
            ]
        ], 201);
    }

    // PUT /api/comments/{id} - обновить комментарий
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Проверка прав: редактировать может только автор
        if ($comment->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this comment'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment->update($request->only('content'));

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'updated_at' => $comment->updated_at,
            ]
        ]);
    }

    // DELETE /api/comments/{id} - удалить комментарий
    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Проверка прав: удалять может только автор или админ
        if ($comment->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this comment'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}