@extends('layouts.app')

@section('title', $car->brand . ' ' . $car->model)

@section('content')
<div class="py-5">
    <div class="row">
        <!-- Левая колонка: Изображение и основная информация -->
        <div class="col-lg-8">
            <!-- Карточка с основной информацией -->
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <!-- Заголовок -->
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-car me-2"></i>{{ $car->brand }} {{ $car->model }}
                    </h1>
                </div>

                <!-- Тело карточки -->
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Изображение -->
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="position-relative">
                                <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" 
                                     class="img-fluid rounded-3 shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary fs-6 py-2 px-3">
                                        {{ $car->body_type }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Основная информация -->
                        <div class="col-md-6">
                            <div class="d-flex flex-column h-100">
                                <!-- Характеристики -->
                                <div class="mb-4">
                                    <h4 class="h5 fw-bold mb-3">
                                        <i class="fas fa-list-alt me-2 text-primary"></i>Характеристики
                                    </h4>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Марка</div>
                                            <div class="fw-semibold">{{ $car->brand }}</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Модель</div>
                                            <div class="fw-semibold">{{ $car->model }}</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Год выпуска</div>
                                            <div class="fw-semibold">{{ $car->year }}</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Пробег</div>
                                            <div class="fw-semibold">{{ $car->formatted_mileage ?? number_format($car->mileage, 0, ',', ' ') . ' км' }}</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Цвет</div>
                                            <div class="fw-semibold">{{ $car->color }}</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-muted small">Тип кузова</div>
                                            <div class="fw-semibold">{{ $car->body_type }}</div>
                                        </div>
                                        
                                        <!-- Информация о владельце (для админа) -->
                                        @auth
                                            @if(auth()->user()->is_admin && $car->user)
                                                <div class="col-12 mt-3 pt-3 border-top">
                                                    <div class="text-muted small">Владелец</div>
                                                    <div class="fw-semibold">
                                                        <i class="fas fa-user me-1"></i>{{ $car->user->name }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Подробное описание объявления -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-align-left me-2 text-primary"></i>Подробное описание
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if($car->detailed_description)
                        <div class="fs-5 lh-base">
                            {{ $car->detailed_description }}
                        </div>
                    @else
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-info-circle me-2"></i>Подробное описание отсутствует
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Правая колонка: Действия -->
        <div class="col-lg-4">
            <!-- Карточка действий -->
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-dark text-white py-3 rounded-top-4">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-cogs me-2"></i>Действия
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <!-- Кнопка "Назад к списку" -->
                        <a href="{{ route('cars.index') }}" class="btn btn-outline-primary btn-lg py-3">
                            <i class="fas fa-arrow-left me-2"></i>Назад к списку
                        </a>

                        <!-- Кнопка "Редактировать" (только для владельца или админа) -->
                        @auth
                            @if(auth()->user()->is_admin || (auth()->id() == $car->user_id))
                                <a href="{{ route('cars.edit', $car) }}" class="btn btn-warning btn-lg py-3">
                                    <i class="fas fa-edit me-2"></i>Редактировать
                                </a>

                                <!-- Кнопка "Удалить" -->
                                <form action="{{ route('cars.destroy', $car) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-lg py-3" 
                                            onclick="return confirm('Вы уверены что хотите удалить этот автомобиль?')">
                                        <i class="fas fa-trash-alt me-2"></i>Удалить
                                    </button>
                                </form>
                            @endif
                        @endauth
                        
                        <!-- Для гостей -->
                        @guest
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Для редактирования или удаления автомобиля нужно войти в систему
                                <div class="mt-2">
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Войти</a>
                                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary ms-1">Регистрация</a>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Секция комментариев -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-light py-3">
                <h3 class="h5 mb-0">
                    <i class="fas fa-comments me-2 text-primary"></i>Комментарии 
                    <span class="badge bg-primary">{{ $car->comments->count() }}</span>
                </h3>
            </div>
            <div class="card-body p-4">
                
                <!-- Форма добавления комментария -->
                @auth
                    <div class="mb-4">
                        <form action="{{ route('comments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="car_id" value="{{ $car->id }}">
                            
                            <div class="mb-3">
                                <label for="content" class="form-label fw-semibold">
                                    Добавить комментарий
                                    @if(auth()->user()->isFriendWith($car->user))
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-user-friends me-1"></i>Друг автора
                                        </span>
                                    @endif
                                </label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="3" 
                                          placeholder="Напишите ваш комментарий..." required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Комментарии видны всем пользователям. Будьте вежливы.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Отправить
                                </button>
                            </div>
                        </form>
                    </div>
                    <hr>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Для добавления комментария нужно 
                        <a href="{{ route('login') }}" class="alert-link">войти в систему</a>.
                    </div>
                @endauth

                <!-- Список комментариев -->
                @if($car->comments->isEmpty())
                    <div class="text-center py-4">
                        <div class="text-muted mb-2">
                            <i class="fas fa-comment-slash fa-2x"></i>
                        </div>
                        <p class="text-muted">Пока нет комментариев. Будьте первым!</p>
                    </div>
                @else
                    <div class="comments-list">
                        @foreach($car->comments as $comment)
                            <div class="comment-item mb-4 pb-4 border-bottom 
                                @if($comment->isFromFriend()) border-start border-3 border-success border-opacity-75 ps-3 @endif">
                                
                                <!-- Заголовок комментария -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                {{ $comment->user->name }}
                                                @if($comment->user->is_admin)
                                                    <span class="badge bg-danger ms-1">
                                                        <i class="fas fa-crown"></i> Админ
                                                    </span>
                                                @endif
                                                @if($comment->isFromFriend())
                                                    <span class="badge bg-success ms-1">
                                                        <i class="fas fa-user-friends"></i> Друг
                                                    </span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $comment->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <!-- Действия с комментарием -->
                                    @auth
                                        @if(auth()->id() == $comment->user_id || auth()->user()->is_admin)
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary border-0" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if(auth()->id() == $comment->user_id)
                                                        <li>
                                                            <button class="dropdown-item" type="button" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editCommentModal{{ $comment->id }}">
                                                                <i class="fas fa-edit me-2"></i>Редактировать
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('Удалить комментарий?')">
                                                                <i class="fas fa-trash me-2"></i>Удалить
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                
                                <!-- Текст комментария -->
                                <div class="comment-content ms-1">
                                    <p class="mb-0">{{ $comment->content }}</p>
                                </div>
                                
                                <!-- Модальное окно для редактирования -->
                                @auth
                                    @if(auth()->id() == $comment->user_id)
                                        <div class="modal fade" id="editCommentModal{{ $comment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Редактировать комментарий</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="editContent{{ $comment->id }}" class="form-label">Текст комментария</label>
                                                                <textarea class="form-control" id="editContent{{ $comment->id }}" 
                                                                          name="content" rows="4" required>{{ $comment->content }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                            <button type="submit" class="btn btn-primary">Сохранить</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection