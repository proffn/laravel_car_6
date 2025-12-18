@extends('layouts.app')

@section('title', 'Добавить автомобиль')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3 rounded-top-4">
                <h2 class="h4 mb-0">
                    <i class="fas fa-car me-2"></i>Добавить новый автомобиль
                </h2>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Марка -->
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label fw-semibold">
                                Марка <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                   id="brand" name="brand" value="{{ old('brand') }}" 
                                   placeholder="Toyota, Ford, Honda..." required>
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Модель -->
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label fw-semibold">
                                Модель <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                   id="model" name="model" value="{{ old('model') }}" 
                                   placeholder="Camry, Focus, Civic..." required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Год выпуска -->
                        <div class="col-md-6 mb-3">
                            <label for="year" class="form-label fw-semibold">
                                Год выпуска <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                   id="year" name="year" value="{{ old('year') }}" 
                                   min="1900" max="{{ date('Y') + 1 }}" 
                                   placeholder="2020" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Пробег -->
                        <div class="col-md-6 mb-3">
                            <label for="mileage" class="form-label fw-semibold">
                                Пробег (км) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('mileage') is-invalid @enderror" 
                                   id="mileage" name="mileage" value="{{ old('mileage') }}" 
                                   min="0" placeholder="85000" required>
                            @error('mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Цвет -->
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label fw-semibold">
                                Цвет <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                   id="color" name="color" value="{{ old('color') }}" 
                                   placeholder="Белый, Черный, Синий..." required>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Тип кузова -->
                        <div class="col-md-6 mb-3">
                            <label for="body_type" class="form-label fw-semibold">
                                Тип кузова <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('body_type') is-invalid @enderror" 
                                    id="body_type" name="body_type" required>
                                <option value="">Выберите тип кузова</option>
                                <option value="Седан" {{ old('body_type') == 'Седан' ? 'selected' : '' }}>Седан</option>
                                <option value="Универсал" {{ old('body_type') == 'Универсал' ? 'selected' : '' }}>Универсал</option>
                                <option value="Хэтчбек" {{ old('body_type') == 'Хэтчбек' ? 'selected' : '' }}>Хэтчбек</option>
                                <option value="Внедорожник" {{ old('body_type') == 'Внедорожник' ? 'selected' : '' }}>Внедорожник</option>
                                <option value="Купе" {{ old('body_type') == 'Купе' ? 'selected' : '' }}>Купе</option>
                                <option value="Минивэн" {{ old('body_type') == 'Минивэн' ? 'selected' : '' }}>Минивэн</option>
                                <option value="Пикап" {{ old('body_type') == 'Пикап' ? 'selected' : '' }}>Пикап</option>
                            </select>
                            @error('body_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Изображение -->
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label fw-semibold">Изображение</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG, GIF</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Подробное описание -->
                        <div class="col-12 mb-4">
                            <label for="detailed_description" class="form-label fw-semibold">
                                Подробное описание объявления <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('detailed_description') is-invalid @enderror" 
                                      id="detailed_description" name="detailed_description" 
                                      rows="6" placeholder="Опишите состояние автомобиля, особенности, историю обслуживания..." required>{{ old('detailed_description') }}</textarea>
                            @error('detailed_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i>Назад
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i>Добавить автомобиль
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection