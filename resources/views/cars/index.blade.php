@extends('layouts.app')

@section('title', 'Отдам даром')

@section('content')
<div class="my-5">
    <!-- Сообщения об успехе -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Информация о пользователе и навигация -->
    @auth
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-user me-2"></i>
                    Вы вошли как: <strong>{{ auth()->user()->name }}</strong>
                    @if(auth()->user()->is_admin)
                        <span class="badge bg-danger ms-2">Администратор</span>
                    @endif
                </div>
                
                <div class="d-flex gap-2">
                    <!-- Ссылка на список пользователей -->
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-users me-1"></i>Все пользователи
                    </a>
                    
                    <!-- Ссылка на корзину (только для админа) -->
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('cars.trash') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-trash-restore me-1"></i>Корзина
                        </a>
                    @endif
                    
                    <!-- Ссылка на свои машины -->
                    <a href="{{ route('users.cars', auth()->user()->name) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-car me-1"></i>Мои автомобили
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Вы не авторизованы. Для добавления автомобилей нужно войти в систему.
        </div>
    @endauth

    @if($cars->isEmpty())
        <!-- Если нет автомобилей -->
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">
                <i class="fas fa-car"></i>
            </div>
            <h3 class="mb-3">Автомобилей пока нет</h3>
            <p class="text-muted mb-4">Добавьте первый автомобиль через кнопку "Добавить автомобиль" в меню</p>
            
            @auth
                <a href="{{ route('cars.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Добавить первый автомобиль
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Войти для добавления
                </a>
            @endauth
        </div>
    @else
        <!-- Информация для пользователей -->
        <div class="alert alert-light border mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-primary me-2 fs-5"></i>
                <div>
                    <strong>Все автомобили в системе</strong>
                    <p class="mb-0 text-muted">
                        @auth
                            @if(auth()->user()->is_admin)
                                Вы видите все автомобили всех пользователей и можете редактировать любые.
                            @else
                                Вы видите все автомобили. Редактировать и удалять можете только свои.
                            @endif
                        @else
                            Вы видите все автомобили. Для добавления, редактирования или удаления войдите в систему.
                        @endauth
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Автомобили из базы данных -->
        <div class="row g-4">
            @foreach($cars as $car)
            <div class="col-xxxl-3 col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="card shadow-sm border-0 rounded-4 h-100 position-relative overflow-hidden car-card">
                    <!-- Бейдж владельца -->
                    <div class="position-absolute top-0 end-0 m-2">
                        @if($car->user)
                            <span class="badge bg-dark bg-opacity-75 px-2 py-1">
                                <i class="fas fa-user me-1"></i>{{ $car->user->name }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Тип кузова -->
                    <div class="position-absolute top-0 start-0 m-2 px-2 py-1 bg-primary text-white rounded fw-semibold small shadow-sm">
                        {{ $car->body_type }}
                    </div>
                    
                    <!-- Изображение -->
                    <img src="{{ $car->image_url }}" 
                         class="card-img-top img-fluid" 
                         alt="{{ $car->brand }} {{ $car->model }}"
                         style="height: 250px; object-fit: cover;">
                    
                    <div class="card-body bg-light-subtle">
                        <!-- Название без года -->
                        <h5 class="card-title fw-bold mb-2">{{ $car->brand }} {{ $car->model }}</h5>
                        
                        <!-- Основная информация -->
                        <p class="card-text text-muted">
                            <strong>Год:</strong> {{ $car->year }}<br>
                            <strong>Пробег:</strong> {{ number_format($car->mileage, 0, ',', ' ') }} км<br>
                            <strong>Цвет:</strong> {{ $car->color }}
                        </p>
                        
                        <!-- Статус владельца -->
                        @auth
                            @if(auth()->id() == $car->user_id)
                                <p class="card-text small text-success">
                                    <i class="fas fa-check-circle me-1"></i>Ваш автомобиль
                                </p>
                            @endif
                        @endauth
                        
                        <!-- Кнопки -->
                        <div class="mt-3 d-grid gap-2">
                            <a href="{{ route('cars.show', $car) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Подробнее
                            </a>
                            
                            @auth
                                <!-- Кнопки редактирования и удаления показываем только владельцу или админу -->
                                @if(auth()->user()->is_admin || auth()->id() == $car->user_id)
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('cars.edit', $car) }}" 
                                           class="btn btn-outline-warning">
                                            <i class="fas fa-edit me-1"></i>Редактировать
                                        </a>
                                        <form action="{{ route('cars.destroy', $car) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
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