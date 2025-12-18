@extends('layouts.app')

@section('title', 'Лента друзей')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-newspaper me-2"></i>Лента друзей
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Все автомобили
            </a>
            <a href="{{ route('friends.index') }}" class="btn btn-outline-warning position-relative">
                <i class="fas fa-user-friends me-1"></i>Друзья
                @if(auth()->user()->pendingFriendRequestsCount() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ auth()->user()->pendingFriendRequestsCount() }}
                        <span class="visually-hidden">новых запросов</span>
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Сообщения -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Информация о ленте -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle text-primary me-3 fs-4"></i>
            <div>
                <strong>Лента друзей</strong>
                <p class="mb-0">
                    Здесь отображаются новые автомобили ваших друзей. 
                    Вы можете комментировать их и добавлять в избранное.
                </p>
            </div>
        </div>
    </div>

    @if($cars->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="mb-3">Лента пуста</h3>
            <p class="text-muted mb-4">
                У вас пока нет друзей или ваши друзья еще не добавили автомобили.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('users.index') }}" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Найти друзей
                </a>
                <a href="{{ route('friends.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-user-friends me-2"></i>Мои друзья
                </a>
            </div>
        </div>
    @else
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <h2 class="display-6 mb-1">{{ $cars->count() }}</h2>
                        <p class="mb-0 small">Автомобилей в ленте</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-3">
                        <h2 class="display-6 mb-1">{{ auth()->user()->allFriends()->count() }}</h2>
                        <p class="mb-0 small">Всего друзей</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center py-3">
                        @php
                            $activeFriends = collect();
                            foreach(auth()->user()->allFriends() as $friend) {
                                if($friend->cars->count() > 0) {
                                    $activeFriends->push($friend);
                                }
                            }
                        @endphp
                        <h2 class="display-6 mb-1">{{ $activeFriends->count() }}</h2>
                        <p class="mb-0 small">Активных друзей</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Автомобили в ленте -->
        <div class="row g-4">
            @foreach($cars as $car)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <!-- Бейдж друга -->
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-success">
                            <i class="fas fa-user-friends me-1"></i>Друг
                        </span>
                    </div>
                    
                    <!-- Бейдж типа кузова -->
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-dark">{{ $car->body_type }}</span>
                    </div>
                    
                    <!-- Изображение -->
                    <img src="{{ $car->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $car->brand }} {{ $car->model }}"
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $car->brand }} {{ $car->model }}</h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                {{ $car->user->name }}
                                @if($car->user->is_admin)
                                    <span class="badge bg-danger ms-1">Админ</span>
                                @endif
                            </small>
                        </p>
                        
                        <p class="card-text">
                            <strong>Год:</strong> {{ $car->year }}<br>
                            <strong>Пробег:</strong> {{ number_format($car->mileage, 0, ',', ' ') }} км<br>
                            <strong>Цвет:</strong> {{ $car->color }}
                        </p>
                        
                        <!-- Комментарии -->
                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="fas fa-comments me-2 text-primary"></i>
                                <strong>Комментарии:</strong> {{ $car->comments->count() }}
                            </p>
                            @if($car->comments->count() > 0)
                                <div class="bg-light rounded p-2">
                                    @foreach($car->comments->take(2) as $comment)
                                        <div class="mb-1">
                                            <small>
                                                <strong>{{ $comment->user->name }}:</strong>
                                                {{ Str::limit($comment->content, 50) }}
                                                @if($comment->isFromFriend())
                                                    <span class="badge bg-success bg-opacity-25 text-success ms-1">
                                                        <i class="fas fa-user-friends"></i>
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                    @endforeach
                                    @if($car->comments->count() > 2)
                                        <small class="text-muted">
                                            и еще {{ $car->comments->count() - 2 }} комментариев...
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('cars.show', $car) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>Подробнее
                            </a>
                            @auth
                                @if(auth()->user()->isFriendWith($car->user))
                                    <a href="{{ route('cars.show', $car) }}#comments" class="btn btn-outline-success">
                                        <i class="fas fa-comment me-1"></i>Комментировать
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Информация о друзьях -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-friends me-2"></i>
                    Ваши друзья в ленте
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $friendsInFeed = $cars->pluck('user')->unique();
                    @endphp
                    
                    @if($friendsInFeed->count() > 0)
                        @foreach($friendsInFeed as $friend)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">{{ $friend->name }}</h6>
                                    <small class="text-muted">
                                        {{ $cars->where('user_id', $friend->id)->count() }} авто в ленте
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <p class="text-muted text-center mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Нет друзей с автомобилями в ленте
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Стили для ленты -->
<style>
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .badge.bg-success {
        box-shadow: 0 2px 4px rgba(25, 135, 84, 0.2);
    }
</style>
@endsection