<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Автомобили')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* Основные стили для страниц аутентификации */
        body {
            font-family: 'Play', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .auth-container {
            width: 100%;
            max-width: 450px;
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .auth-logo a {
            text-decoration: none;
            color: #2d3748;
            display: inline-flex;
            align-items: center;
            gap: 15px;
            font-size: 2.2rem;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .auth-logo a:hover {
            transform: translateY(-3px);
        }
        
        .auth-logo i {
            color: #4e73df;
            font-size: 2.8rem;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .auth-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .auth-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .auth-body {
            padding: 2.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-auth {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
            color: white;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(78, 115, 223, 0.3);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .auth-links a {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .auth-links a:hover {
            color: #224abe;
            text-decoration: underline;
        }
        
        .password-toggle {
            cursor: pointer;
            background-color: #f8f9fc;
            border: 2px solid #e2e8f0;
            border-left: none;
            color: #6c757d;
        }
        
        .input-group-text {
            background-color: #f8f9fc;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: #6c757d;
        }
        
        .footer-text {
            text-align: center;
            color: #718096;
            font-size: 0.9rem;
        }
        
        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .auth-card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <!-- Логотип -->
        <div class="auth-logo">
            <a href="{{ route('cars.index') }}">
                <i class="fas fa-car"></i>
                <span>Автомобили</span>
            </a>
        </div>
        
        <!-- Основная карточка -->
        <div class="auth-card">
            <!-- Заголовок -->
            <div class="auth-header">
                <h1>@yield('auth-title', 'Добро пожаловать')</h1>
                <p>@yield('auth-subtitle', 'Лабораторная работа №4')</p>
            </div>
            
            <!-- Контент -->
            <div class="auth-body">
                <!-- Сообщения об ошибках -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Ошибка!</strong> Пожалуйста, исправьте следующие ошибки:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Сообщения об успехе -->
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Основной контент формы -->
                @yield('content')
            </div>
        </div>
        
        <!-- Футер -->
        <div class="footer-text">
            <p>Многопользовательское приложение • Лабораторная работа №4</p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Кастомный JS для переключения видимости пароля -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Функция переключения видимости пароля
            function togglePasswordVisibility(inputId, toggleButton) {
                const input = document.getElementById(inputId);
                const icon = toggleButton.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
            
            // Находим все кнопки переключения пароля
            const toggleButtons = document.querySelectorAll('.password-toggle');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const inputId = this.getAttribute('data-target');
                    togglePasswordVisibility(inputId, this);
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>