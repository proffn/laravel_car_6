@extends('layouts.app')

@section('title', 'Список пользователей')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>Список пользователей
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Все автомобили
            </a>
            @auth
                <a href="{{ route('friends.index') }}" class="btn btn-outline-warning position-relative">
                    <i class="fas fa-user-friends me-1"></i>Друзья
                    @if(auth()->user()->pendingFriendRequestsCount() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ auth()->user()->pendingFriendRequestsCount() }}
                            <span class="visually-hidden">новых запросов</span>
                        </span>
                    @endif
                </a>
            @endauth
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($users->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">
                <i class="fas fa-users-slash"></i>
            </div>
            <h3 class="mb-3">В системе пока нет пользователей</h3>
            <p class="text-muted mb-4">
                Зарегистрируйтесь первым или пригласите друзей!
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
            </a>
        </div>
    @else
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2 class="display-6">{{ $users->count() }}</h2>
                        <p class="mb-0">Всего пользователей</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h2 class="display-6">{{ $users->where('is_admin', false)->count() }}</h2>
                        <p class="mb-0">Обычных пользователей</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h2 class="display-6">{{ $users->where('is_admin', true)->count() }}</h2>
                        <p class="mb-0">Администраторов</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h2 class="display-6">{{ $users->sum('cars_count') }}</h2>
                        <p class="mb-0">Всего автомобилей</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список пользователей -->
        <div class="row">
            @foreach($users as $user)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">{{ $user->name }}</h5>
                                <p class="card-text text-muted mb-0 small">{{ $user->email }}</p>
                            </div>
                            @if($user->is_admin)
                                <span class="badge bg-danger">
                                    <i class="fas fa-crown me-1"></i>Админ
                                </span>
                            @endif
                        </div>
                        
                        <div class="card-text">
                            <p class="mb-2 small">
                                <i class="fas fa-car me-2 text-primary"></i>
                                <strong>Автомобилей:</strong> {{ $user->cars_count }}
                            </p>
                            <p class="mb-2 small">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                <strong>Зарегистрирован:</strong> {{ $user->created_at->format('d.m.Y') }}
                            </p>
                            <p class="mb-0 small">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <strong>В системе:</strong> {{ $user->created_at->diffForHumans() }}
                            </p>
                            
                            <!-- Статус дружбы -->
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <div class="mt-3 pt-3 border-top">
                                        <p class="mb-1 small">
                                            <i class="fas fa-user-friends me-2"></i>
                                            <strong>Статус:</strong>
                                            @if(auth()->user()->isFriendWith($user))
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Друг
                                                </span>
                                            @elseif(auth()->user()->hasSentFriendRequestTo($user))
                                                <span class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>Запрос отправлен
                                                </span>
                                            @elseif(auth()->user()->hasPendingFriendRequestFrom($user))
                                                <span class="text-info">
                                                    <i class="fas fa-user-clock me-1"></i>Ждет вашего ответа
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-user me-1"></i>Не в друзьях
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('users.cars', $user->name) }}" class="btn btn-primary">
                                <i class="fas fa-car me-1"></i>Автомобили
                            </a>
                            
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <div class="btn-group" role="group">
                                        @if(auth()->user()->isFriendWith($user))
                                            <!-- Уже друзья -->
                                            <button class="btn btn-success disabled w-100">
                                                <i class="fas fa-user-check me-1"></i>Уже друзья
                                            </button>
                                            <!-- Найти запись о дружбе для удаления -->
                                            @php
                                                $friendship = auth()->user()->sentFriendRequests()
                                                    ->where('friend_id', $user->id)
                                                    ->where('status', 'accepted')
                                                    ->first();
                                                
                                                if (!$friendship) {
                                                    $friendship = auth()->user()->receivedFriendRequests()
                                                        ->where('user_id', $user->id)
                                                        ->where('status', 'accepted')
                                                        ->first();
                                                }
                                            @endphp
                                            @if($friendship)
                                                <form action="{{ route('friends.remove', $friendship) }}" method="POST" class="w-100">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger"
                                                            onclick="return confirm('Удалить из друзей?')">
                                                        <i class="fas fa-user-minus me-1"></i>Удалить
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif(auth()->user()->hasSentFriendRequestTo($user))
                                            <!-- Запрос уже отправлен -->
                                            <button class="btn btn-warning disabled w-100">
                                                <i class="fas fa-clock me-1"></i>Запрос отправлен
                                            </button>
                                        @elseif(auth()->user()->hasPendingFriendRequestFrom($user))
                                            <!-- Есть входящий запрос -->
                                            <a href="{{ route('friends.requests') }}" class="btn btn-info w-100">
                                                <i class="fas fa-user-clock me-1"></i>Принять запрос
                                            </a>
                                        @else
                                            <!-- Отправить запрос в друзья -->
                                            <form action="{{ route('friends.send', $user) }}" method="POST" class="w-100">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-primary w-100">
                                                    <i class="fas fa-user-plus me-1"></i>Добавить в друзья
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <!-- Это текущий пользователь -->
                                    <button class="btn btn-secondary disabled w-100">
                                        <i class="fas fa-user me-1"></i>Это вы
                                    </button>
                                @endif
                            @else
                                <!-- Для неавторизованных -->
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-1"></i>Войти для добавления в друзья
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Подсказка -->
        <div class="alert alert-info mt-4">
            <div class="d-flex">
                <i class="fas fa-info-circle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-2">Как работает система друзей?</h6>
                    <p class="mb-1">1. Добавляйте пользователей в друзья, чтобы видеть их автомобили в ленте</p>
                    <p class="mb-1">2. Комментарии друзей выделяются цветом на страницах автомобилей</p>
                    <p class="mb-1">3. Дружба автоматически становится обоюдной при принятии запроса</p>
                    <p class="mb-0">4. Вы можете в любой момент удалить пользователя из друзей</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection