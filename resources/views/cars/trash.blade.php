@extends('layouts.app')

@section('title', 'Корзина удаленных автомобилей')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-trash-restore me-2 text-warning"></i>
            {{ $header ?? 'Корзина удаленных автомобилей' }}
        </h1>
        <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>Назад к списку
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($cars->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Корзина пуста. Здесь будут отображаться удаленные автомобили.
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            В корзине {{ $cars->count() }} удаленных автомобилей. Вы можете восстановить их или удалить навсегда.
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Изображение</th>
                        <th>Марка и модель</th>
                        <th>Год</th>
                        <th>Пробег</th>
                        <th>Владелец</th>
                        <th>Удален</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cars as $car)
                    <tr>
                        <td>{{ $car->id }}</td>
                        <td>
                            <img src="{{ $car->image_url }}" 
                                 alt="{{ $car->brand }} {{ $car->model }}"
                                 class="img-thumbnail" 
                                 style="width: 80px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <strong>{{ $car->brand }} {{ $car->model }}</strong><br>
                            <small class="text-muted">{{ $car->body_type }}, {{ $car->color }}</small>
                        </td>
                        <td>{{ $car->year }}</td>
                        <td>{{ number_format($car->mileage, 0, ',', ' ') }} км</td>
                        <td>
                            @if($car->user)
                                {{ $car->user->name }}
                            @else
                                <span class="text-muted">Нет владельца</span>
                            @endif
                        </td>
                        <td>
                            {{ $car->deleted_at->format('d.m.Y H:i') }}
                            <br>
                            <small class="text-muted">
                                {{ $car->deleted_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Кнопка восстановления -->
                                <form action="{{ route('cars.restore', $car->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-outline-success"
                                            onclick="return confirm('Восстановить этот автомобиль?')">
                                        <i class="fas fa-trash-restore me-1"></i>Восстановить
                                    </button>
                                </form>
                                
                                <!-- Кнопка полного удаления -->
                                <form action="{{ route('cars.force-delete', $car->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger"
                                            onclick="return confirm('УДАЛИТЬ НАВСЕГДА? Это действие нельзя отменить!')">
                                        <i class="fas fa-fire me-1"></i>Удалить навсегда
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection