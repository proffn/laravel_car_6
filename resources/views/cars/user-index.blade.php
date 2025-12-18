@extends('layouts.app')

@section('title', 'Автомобили пользователя ' . $user->name)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user me-2"></i>
                Автомобили пользователя: {{ $user->name }}
            </h1>
            @if($user->is_admin)
                <span class="badge bg-danger mt-2">
                    <i class="fas fa-crown me-1"></i>Администратор
                </span>
            @endif
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-users me-1"></i>Все пользователи
            </a>
            <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Все автомобили
            </a>
        </div>
    </div>

    @if($cars->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            У пользователя {{ $user->name }} пока нет автомобилей.
        </div>
    @else
        <!-- Информация для текущего пользователя -->
        @auth
            @if(auth()->user()->is_admin)
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Режим администратора:</strong> Вы можете редактировать и удалять любые автомобили этого пользователя.
                </div>
            @elseif(auth()->id() == $user->id)
                <div class="alert alert-info mb-4">
                    <i class="fas fa-user-check me-2"></i>
                    <strong>Это ваш профиль!</strong> Вы можете управлять своими автомобилями.
                </div>
            @else
                <div class="alert alert-light border mb-4">
                    <i class="fas fa-eye me-2"></i>
                    Вы просматриваете автомобили пользователя {{ $user->name }}.
                </div>
            @endif
        @endauth
        
        <!-- Автомобили пользователя -->
        <div class="row g-4">
            @foreach($cars as $car)
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="{{ $car->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $car->brand }} {{ $car->model }}"
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $car->brand }} {{ $car->model }}</h5>
                        <p class="card-text">
                            Год: {{ $car->year }}<br>
                            Пробег: {{ $car->formatted_mileage }}<br>
                            Цвет: {{ $car->color }}<br>
                            <small class="text-muted">
                                Добавлен: {{ $car->created_at->format('d.m.Y') }}
                            </small>
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('cars.show', $car) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>Подробнее
                            </a>
                            
                            @auth
                                <!-- Кнопки редактирования и удаления показываем только: -->
                                <!-- 1. Администратору -->
                                <!-- 2. Владельцу автомобиля (если это его профиль) -->
                                @if(auth()->user()->is_admin || (auth()->id() == $car->user_id && auth()->id() == $user->id))
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('cars.edit', $car) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-1"></i>Редактировать
                                        </a>
                                        <form action="{{ route('cars.destroy', $car) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Удалить этот автомобиль?')">
                                                <i class="fas fa-trash me-1"></i>Удалить
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection