<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    // GET /api/cars - список всех автомобилей
    public function index()
    {
        $cars = Car::with(['user', 'comments.user'])->latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => CarResource::collection($cars),
            'meta' => [
                'current_page' => $cars->currentPage(),
                'last_page' => $cars->lastPage(),
                'per_page' => $cars->perPage(),
                'total' => $cars->total(),
            ]
        ]);
    }

    // POST /api/cars - создать новый автомобиль
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'color' => 'required|max:50',
            'body_type' => 'required|in:Седан,Универсал,Хэтчбек,Внедорожник,Купе,Минивэн,Пикап',
            'detailed_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $car = Car::create([
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'mileage' => $request->mileage,
            'color' => $request->color,
            'body_type' => $request->body_type,
            'detailed_description' => $request->detailed_description,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Car created successfully',
            'data' => new CarResource($car->load('user'))
        ], 201);
    }

    // GET /api/cars/{id} - показать автомобиль
    public function show(Car $car)
    {
        $car->load(['user', 'comments.user']);
        
        return response()->json([
            'success' => true,
            'data' => new CarResource($car)
        ]);
    }

    // PUT /api/cars/{id} - обновить автомобиль
    public function update(Request $request, Car $car)
    {
        // Проверка прав: редактировать может только владелец
        if ($car->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this car'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'color' => 'required|max:50',
            'body_type' => 'required|in:Седан,Универсал,Хэтчбек,Внедорожник,Купе,Минивэн,Пикап',
            'detailed_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $car->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'data' => new CarResource($car->load('user'))
        ]);
    }

    // DELETE /api/cars/{id} - удалить автомобиль
    public function destroy(Request $request, Car $car)
    {
        // Проверка прав: удалять может только владелец или админ
        if ($car->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this car'
            ], 403);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully'
        ]);
    }
}