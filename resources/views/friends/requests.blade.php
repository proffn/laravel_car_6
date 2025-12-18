@extends('layouts.app')

@section('title', 'Запросы в друзья')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-user-clock me-2"></i>Запросы в друзья
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('friends.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Мои друзья
            </a>
            <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-car me-1"></i>Все автомобили
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

    @if($pendingRequests->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-3">
                <i class="fas fa-inbox"></i>
            </div>
            <h3 class="mb-3">Нет новых запросов</h3>
            <p class="text-muted mb-4">
                Здесь будут отображаться запросы от других пользователей, которые хотят добавить вас в друзья
            </p>
            <a href="{{ route('users.index') }}" class="btn btn-primary">
                <i class="fas fa-users me-2"></i>Найти пользователей
            </a>
        </div>
    @else
        <div class="row">
            @foreach($pendingRequests as $request)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">{{ $request->user->name }}</h5>
                                <p class="card-text text-muted mb-0">{{ $request->user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="card-text">
                            <p class="mb-2">
                                <i class="fas fa-car me-2 text-primary"></i>
                                <strong>Автомобилей:</strong> {{ $request->user->cars_count ?? $request->user->cars->count() }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                <strong>Зарегистрирован:</strong> {{ $request->user->created_at->format('d.m.Y') }}
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <strong>Запрос отправлен:</strong> {{ $request->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="row g-2">
                            <div class="col-6">
                                <form action="{{ route('friends.accept', $request) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-1"></i>Принять
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form action="{{ route('friends.reject', $request) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Отклонить запрос в друзья?')">
                                        <i class="fas fa-times me-1"></i>Отклонить
                                    </button>
                                </form>
                            </div>
                            <div class="col-12 mt-2">
                                <a href="{{ route('users.cars', $request->user->name) }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-eye me-1"></i>Посмотреть профиль
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Статистика -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Статистика</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="display-6 text-primary">{{ $pendingRequests->count() }}</div>
                        <p class="text-muted mb-0">Новых запросов</p>
                    </div>
                    <div class="col-md-4">
                        <div class="display-6 text-success">{{ auth()->user()->allFriends()->count() }}</div>
                        <p class="text-muted mb-0">Всего друзей</p>
                    </div>
                    <div class="col-md-4">
                        <div class="display-6 text-warning">{{ auth()->user()->sentFriendRequests()->where('status', 'pending')->count() }}</div>
                        <p class="text-muted mb-0">Отправлено запросов</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection