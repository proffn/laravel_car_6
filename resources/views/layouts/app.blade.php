<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Автомобили')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap и наши стили -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
    
    @stack('styles')
</head>
<body>
    @include('layouts.navigation')
    
    <main class="container py-4">
        <!-- Уведомления -->
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
        
        <!-- Заголовок страницы -->
        @if(isset($header) && !empty($header))
            <div class="mb-4">
                <h1 class="h2 mb-0 text-primary">
                    <i class="fas fa-car me-2"></i>{{ $header }}
                </h1>
                @if(isset($subheader) && !empty($subheader))
                    <p class="text-muted mt-2">{{ $subheader }}</p>
                @endif
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <p class="mb-2 mb-sm-0 text-center text-sm-start fw-medium">
            Работу выполнил: <span class="fw-bold">Рафаилов Никита</span>
        </p>
        <div class="d-flex gap-3 justify-content-center justify-content-sm-end">
            <a href="#" class="text-dark fs-4">
                <img src="{{ asset('images/facebooklogoincircularbuttonoutlinedsocialsymbol_79822.png') }}" 
                     alt="Facebook" width="30" height="30">
            </a>
            <a href="#" class="text-dark fs-4">
                <img src="{{ asset('images/Twitter_Rounded_icon-icons.com_61577.png') }}" 
                     alt="Twitter" width="30" height="30">
            </a>
            <a href="#" class="text-dark fs-4">
                <img src="{{ asset('images/instagram_icon-icons.com_65435.png') }}" 
                     alt="Instagram" width="30" height="30">
            </a>
        </div>
    </div>
    </footer>
    
    <!-- Наши скрипты -->
    <script src="{{ mix('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>