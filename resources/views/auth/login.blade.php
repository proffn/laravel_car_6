@extends('layouts.auth')

@section('title', 'Вход в систему')
@section('auth-title', 'С возвращением!')
@section('auth-subtitle', 'Войдите в свой аккаунт')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="fas fa-envelope me-2"></i>Электронная почта
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-user"></i>
            </span>
            <input id="email" type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" 
                   required autocomplete="email" autofocus
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
                   name="password" required autocomplete="current-password"
                   placeholder="Введите ваш пароль">
            <span class="input-group-text password-toggle" data-target="password">
                <i class="fas fa-eye"></i>
            </span>
        </div>
        @error('password')
            <span class="text-danger small mt-1 d-block">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </span>
        @enderror
    </div>

    
    <!-- Submit Button -->
    <div class="d-grid gap-2 mb-4">
        <button type="submit" class="btn btn-auth">
            <i class="fas fa-sign-in-alt me-2"></i>Войти в систему
        </button>
    </div>
    
    <!-- Links -->
    <div class="auth-links">
        <div>
            <span class="text-muted">Ещё нет аккаунта?</span>
            <a href="{{ route('register') }}" class="ms-2 fw-bold">
                <i class="fas fa-user-plus me-1"></i>Создать аккаунт
            </a>
        </div>
    </div>
</form>
@endsection