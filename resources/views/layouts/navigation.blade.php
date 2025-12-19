<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Логотип и бренд -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('cars.index') }}">
            <img src="{{ asset('images/logo.png') }}" 
                alt="Логотип" 
                style="height: 60px; width: auto;" 
                class="me-2">
            <div>
                <span class="fs-5 fw-bold">Доска объявлений "Отдам даром"</span>
            </div>
        </a>

        <!-- Кнопка для мобильной версии -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Навигационное меню -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cars.index') ? 'active' : '' }}" 
                       href="{{ route('cars.index') }}">
                        <i class="fas fa-list me-1"></i>Все автомобили
                    </a>
                </li>
                
                <!-- Лента друзей (только для авторизованных) -->
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('feed') ? 'active' : '' }}" 
                           href="{{ route('feed') }}">
                            <i class="fas fa-newspaper me-1"></i>Лента друзей
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cars.create') ? 'active' : '' }}" 
                           href="{{ route('cars.create') }}">
                            <i class="fas fa-plus-circle me-1"></i>Добавить автомобиль
                        </a>
                    </li>
                    
                    @if(auth()->user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cars.trash') ? 'active' : '' }}" 
                               href="{{ route('cars.trash') }}">
                                <i class="fas fa-trash-restore me-1"></i>Корзина
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Правая часть навигации -->
            <ul class="navbar-nav ms-auto">
                <!-- Ссылка на список пользователей (для всех) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" 
                       href="{{ route('users.index') }}">
                        <i class="fas fa-users me-1"></i>Пользователи
                    </a>
                </li>
                
                @auth
                    <!-- Ссылка на друзей с счетчиком запросов -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('friends.*') ? 'active' : '' }} position-relative" 
                           href="{{ route('friends.index') }}">
                            <i class="fas fa-user-friends me-1"></i>Друзья
                            @if(auth()->user()->pendingFriendRequestsCount() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                      style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                                    {{ auth()->user()->pendingFriendRequestsCount() }}
                                    <span class="visually-hidden">новых запросов</span>
                                </span>
                            @endif
                        </a>
                    </li>

                    <!-- Dropdown пользователя -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2 fs-5"></i>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                <small class="text-white-50">
                                    @if(Auth::user()->is_admin)
                                        <span class="badge bg-danger">Администратор</span>
                                    @else
                                        Пользователь
                                    @endif
                                </small>
                            </div>
                            
                            <!-- Счетчик запросов возле аватара -->
                            @if(Auth::user()->pendingFriendRequestsCount() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                      style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                                    {{ Auth::user()->pendingFriendRequestsCount() }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Панель управления
                            </a>
                            <a class="dropdown-item" href="{{ route('users.cars', Auth::user()->name) }}">
                                <i class="fas fa-car me-2"></i>Мои автомобили
                            </a>
                            <a class="dropdown-item" href="{{ route('friends.index') }}">
                                <i class="fas fa-users me-2"></i>Мои друзья
                                @if(Auth::user()->pendingFriendRequestsCount() > 0)
                                    <span class="badge bg-danger float-end">{{ Auth::user()->pendingFriendRequestsCount() }}</span>
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ route('friends.requests') }}">
                                <i class="fas fa-user-clock me-2"></i>Запросы в друзья
                                @if(Auth::user()->pendingFriendRequestsCount() > 0)
                                    <span class="badge bg-danger float-end">{{ Auth::user()->pendingFriendRequestsCount() }}</span>
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ route('profile.tokens') }}">
                                <i class="fas fa-key me-2"></i>API Токены
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none p-0">
                                    <i class="fas fa-sign-out-alt me-2"></i>Выйти
                                </button>
                            </form>
                        </div>
                    </li>
                @else
                    <!-- Ссылки для неавторизованных -->
                    <li class="nav-item">
                        <a class="btn btn-outline-light me-2" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Войти
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-2"></i>Регистрация
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>