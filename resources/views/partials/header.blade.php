<header class="border-bottom border-2 border-dark py-3">
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-transparent">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('cars.index') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Логотип" width="70" height="70" class="d-inline-block align-text-top">
                    <span class="fw-bold fs-3" style="font-family: 'Play', sans-serif;">
                        Доска объявлений "Отдам даром"
                    </span>
                </a>
                <div class="d-flex gap-3">
                    <a href="{{ route('cars.create') }}" class="btn btn-success px-4 py-2 fs-5">
                        <i class="fas fa-plus me-2"></i>Добавить
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>