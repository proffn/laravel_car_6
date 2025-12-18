@extends('layouts.app')

@section('title', 'Мои друзья')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>Мои друзья
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Все автомобили
            </a>
            <a href="{{ route('friends.requests') }}" class="btn btn-outline-warning position-relative">
                <i class="fas fa-user-clock me-1"></i>Запросы
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Запросы в друзья (если есть) -->
    @if($pendingRequests->count() > 0)
        <div class="card border-warning mb-4">
            <div class="card-header bg-warning bg-opacity-25">
                <h5 class="mb-0">
                    <i class="fas fa-user-clock me-2"></i>Ожидают подтверждения ({{ $pendingRequests->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($pendingRequests as $request)
                    <div class="col-md-6 col-lg-4">
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
                                        <h6 class="card-title mb-0">{{ $request->user->name }}</h6>
                                        <p class="card-text text-muted mb-0 small">
                                            Отправил запрос {{ $request->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100" role="group">
                                    <form action="{{ route('friends.accept', $request) }}" method="POST" class="w-50">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-check me-1"></i>Принять
                                        </button>
                                    </form>
                                    <form action="{{ route('friends.reject', $request) }}" method="POST" class="w-50">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="fas fa-times me-1"></i>Отклонить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Список друзей -->
    @if($friends->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">
                <i class="fas fa-user-friends"></i>
            </div>
            <h3 class="mb-3">У вас пока нет друзей</h3>
            <p class="text-muted mb-4">
                Добавляйте друзей, чтобы видеть их автомобили в ленте и выделять их комментарии
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('users.index') }}" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Найти пользователей
                </a>
                <a href="{{ route('feed') }}" class="btn btn-outline-primary">
                    <i class="fas fa-newspaper me-2"></i>Перейти в ленту
                </a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-friends me-2"></i>
                            Друзья ({{ $friends->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($friends as $friend)
                            <div class="col-md-6 col-lg-4">
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
                                                <h6 class="card-title mb-0">{{ $friend->name }}</h6>
                                                <p class="card-text text-muted mb-0 small">{{ $friend->email }}</p>
                                            </div>
                                            @if($friend->is_admin)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-crown me-1"></i>Админ
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="card-text">
                                            <p class="mb-2 small">
                                                <i class="fas fa-car me-2 text-primary"></i>
                                                <strong>Автомобилей:</strong> {{ $friend->cars_count ?? $friend->cars->count() }}
                                            </p>
                                            <p class="mb-0 small">
                                                <i class="fas fa-clock me-2 text-primary"></i>
                                                <strong>В друзьях:</strong> 
                                                @php
                                                    // Находим запись о дружбе
                                                    $friendship = auth()->user()->sentFriendRequests()
                                                        ->where('friend_id', $friend->id)
                                                        ->where('status', 'accepted')
                                                        ->first();
                                                    
                                                    if (!$friendship) {
                                                        $friendship = auth()->user()->receivedFriendRequests()
                                                            ->where('user_id', $friend->id)
                                                            ->where('status', 'accepted')
                                                            ->first();
                                                    }
                                                @endphp
                                                @if($friendship)
                                                    {{ $friendship->created_at->diffForHumans() }}
                                                @else
                                                    Недавно
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('users.cars', $friend->name) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-car me-1"></i>Автомобили
                                            </a>
                                            @php
                                                // Находим запись о дружбе для удаления
                                                $friendshipToDelete = auth()->user()->sentFriendRequests()
                                                    ->where('friend_id', $friend->id)
                                                    ->where('status', 'accepted')
                                                    ->first();
                                                
                                                if (!$friendshipToDelete) {
                                                    $friendshipToDelete = auth()->user()->receivedFriendRequests()
                                                        ->where('user_id', $friend->id)
                                                        ->where('status', 'accepted')
                                                        ->first();
                                                }
                                            @endphp
                                            @if($friendshipToDelete)
                                                <form action="{{ route('friends.remove', $friendshipToDelete) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                            onclick="return confirm('Удалить из друзей?')">
                                                        <i class="fas fa-user-minus me-1"></i>Удалить из друзей
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Боковая панель -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Информация</h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text small">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Друзья</strong> - это пользователи, которых вы добавили или которые добавили вас.
                        </p>
                        <p class="card-text small">
                            <i class="fas fa-car text-primary me-2"></i>
                            Вы видите автомобили друзей в <a href="{{ route('feed') }}">ленте</a>.
                        </p>
                        <p class="card-text small">
                            <i class="fas fa-comment text-warning me-2"></i>
                            Комментарии друзей выделяются цветом на страницах автомобилей.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Быстрые действия</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Найти пользователей
                            </a>
                            <a href="{{ route('feed') }}" class="btn btn-primary">
                                <i class="fas fa-newspaper me-2"></i>Перейти в ленту
                            </a>
                            @if(auth()->user()->pendingFriendRequestsCount() > 0)
                                <a href="{{ route('friends.requests') }}" class="btn btn-warning">
                                    <i class="fas fa-user-clock me-2"></i>
                                    Запросы ({{ auth()->user()->pendingFriendRequestsCount() }})
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection