@extends('layouts.app')

@section('title', 'Мои API токены')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">
        <i class="fas fa-key me-2"></i>Мои OAuth2 токены
    </h1>

    <!-- Сообщения -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('new_token'))
        <div class="alert alert-warning alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Новый токен создан!</strong> Сохраните его, он покажется только один раз.
            <div class="mt-2">
                <strong>Токен:</strong>
                <div class="input-group mt-1">
                    <input type="text" class="form-control" value="{{ session('new_token') }}" 
                           id="newToken" readonly>
                    <button class="btn btn-outline-secondary" type="button" 
                            onclick="copyToClipboard('newToken')">
                        <i class="fas fa-copy"></i> Копировать
                    </button>
                </div>
                <small class="text-muted">Используйте: <code>Authorization: Bearer {{ session('new_token') }}</code></small>
            </div>
        </div>
    @endif

    <!-- Форма создания токена -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Создать новый токен</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('profile.tokens.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Название токена</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Например: 'Мобильное приложение'" required>
                    <div class="form-text">Название поможет идентифицировать токен</div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Создать токен
                </button>
            </form>
        </div>
    </div>

    <!-- Список токенов -->
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Мои активные токены</h5>
        </div>
        <div class="card-body">
            @if($tokens->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    У вас еще нет токенов. Создайте первый токен выше.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Создан</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tokens as $token)
                            <tr>
                                <td>{{ $token['name'] }}</td>
                                <td>{{ $token['created_at'] }}</td>
                                <td>
                                    @if($token['revoked'])
                                        <span class="badge bg-danger">Отозван</span>
                                    @else
                                        <span class="badge bg-success">Активен</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$token['revoked'])
                                    <form action="{{ route('profile.tokens.delete', $token['id']) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Удалить токен?')">
                                            <i class="fas fa-trash"></i> Удалить
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-muted">Недоступно</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Инструкция по использованию -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-book me-2"></i>Использование API</h5>
        </div>
        <div class="card-body">
            <h6>1. Получение токена через API:</h6>
            <pre class="bg-light p-3">POST /api/login
Content-Type: application/json
{
    "email": "ваш@email.com",
    "password": "пароль"
}</pre>

            <h6 class="mt-3">2. Использование токена:</h6>
            <pre class="bg-light p-3">GET /api/cars
Authorization: Bearer [ваш_токен]
Accept: application/json</pre>

            <h6 class="mt-3">3. Проверка поля "is_friend" (расширенный уровень):</h6>
            <p>В ответе API будет поле <code>"is_friend": true/false</code></p>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const input = document.getElementById(elementId);
    input.select();
    document.execCommand('copy');
    alert('Токен скопирован в буфер обмена');
}
</script>
@endsection