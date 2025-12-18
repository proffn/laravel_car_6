@extends('layouts.auth')

@section('title', 'Регистрация')
@section('auth-title', 'Присоединяйтесь к нам!')
@section('auth-subtitle', 'Создайте свой аккаунт')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <!-- Name -->
    <div class="mb-4">
        <label for="name" class="form-label">
            <i class="fas fa-user me-2"></i>Имя пользователя
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-id-card"></i>
            </span>
            <input id="name" type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name') }}" 
                   required autocomplete="name" autofocus
                   placeholder="Введите ваше имя">
        </div>
        @error('name')
            <span class="text-danger small mt-1 d-block">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </span>
        @enderror
    </div>
    
    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="fas fa-envelope me-2"></i>Электронная почта
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-at"></i>
            </span>
            <input id="email" type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" 
                   required autocomplete="email"
                   placeholder="Введите ваш email">
        </div>
        @error('email')
            <span class="text-danger small mt-1 d-block">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </span>
        @enderror
    </div>
    
    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="form-label">
            <i class="fas fa-lock me-2"></i>Пароль
        </label>
        <div class="input-group">
            <input id="password" type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="new-password"
                   placeholder="Придумайте пароль">
            <span class="input-group-text password-toggle" data-target="password">
                <i class="fas fa-eye"></i>
            </span>
        </div>
        @error('password')
            <span class="text-danger small mt-1 d-block">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </span>
        @enderror
        <small class="text-muted mt-2 d-block">
            <i class="fas fa-info-circle me-1"></i>
            Пароль должен содержать минимум 8 символов
        </small>
    </div>
    
    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password-confirm" class="form-label">
            <i class="fas fa-lock me-2"></i>Подтвердите пароль
        </label>
        <div class="input-group">
            <input id="password-confirm" type="password" 
                   class="form-control" 
                   name="password_confirmation" required 
                   autocomplete="new-password"
                   placeholder="Повторите пароль">
            <span class="input-group-text password-toggle" data-target="password-confirm">
                <i class="fas fa-eye"></i>
            </span>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="d-grid gap-2 mb-4">
        <button type="submit" class="btn btn-auth">
            <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
        </button>
    </div>
    
    <!-- Links -->
    <div class="auth-links">
        <div>
            <span class="text-muted">Уже есть аккаунт?</span>
            <a href="{{ route('login') }}" class="ms-2 fw-bold">
                <i class="fas fa-sign-in-alt me-1"></i>Войти в систему
            </a>
        </div>
    </div>
</form>
@endsection