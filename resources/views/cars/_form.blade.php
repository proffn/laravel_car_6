<div class="row">
    <!-- Марка -->
    <div class="col-md-6 mb-3">
        <label for="brand" class="form-label fw-semibold">
            Марка <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror" 
               id="brand" name="brand" value="{{ old('brand', $car->brand ?? '') }}" 
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
               id="model" name="model" value="{{ old('model', $car->model ?? '') }}" 
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
               id="year" name="year" value="{{ old('year', $car->year ?? '') }}" 
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
               id="mileage" name="mileage" value="{{ old('mileage', $car->mileage ?? '') }}" 
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
               id="color" name="color" value="{{ old('color', $car->color ?? '') }}" 
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
            @foreach(['Седан', 'Универсал', 'Хэтчбек', 'Внедорожник', 'Купе', 'Минивэн', 'Пикап'] as $type)
                <option value="{{ $type }}" 
                    {{ old('body_type', $car->body_type ?? '') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
        @error('body_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Изображение -->
    <div class="col-12 mb-3">
        <label for="image" class="form-label fw-semibold">Изображение</label>
        
        {{-- Показываем текущее изображение ТОЛЬКО при редактировании --}}
        @if(isset($car) && $car->image_url)
            <div class="mb-2">
                <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" 
                     class="img-thumbnail" style="max-height: 150px;">
                <div class="form-text mt-1">Текущее изображение</div>
            </div>
        @endif
        
        <input type="file" class="form-control @error('image') is-invalid @enderror" 
               id="image" name="image" accept="image/*">
        
        <div class="form-text">
            @if(isset($car))
                Оставьте пустым, чтобы сохранить текущее изображение. 
            @endif
            Максимальный размер: 5MB. Допустимые форматы: JPEG, PNG, JPG, GIF, WebP, BMP
        </div>
        
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
                  rows="6" placeholder="Опишите состояние автомобиля, особенности, историю обслуживания..." 
                  required>{{ old('detailed_description', $car->detailed_description ?? '') }}</textarea>
        @error('detailed_description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>