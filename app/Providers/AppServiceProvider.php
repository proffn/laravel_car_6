<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport; // ← ДОБАВИТЬ

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Регистрируем маршруты Passport
        Passport::routes();
        
        // Устанавливаем время жизни токенов
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        
        // Настройка скоупов для API (опционально, но полезно)
        Passport::tokensCan([
            'view-cars' => 'View cars',
            'create-cars' => 'Create new cars',
            'update-cars' => 'Update cars',
            'delete-cars' => 'Delete cars',
            'view-comments' => 'View comments',
            'create-comments' => 'Create comments',
            'manage-friends' => 'Manage friends',
        ]);
        
        // Можно указать по умолчанию
        Passport::setDefaultScope([
            'view-cars',
            'view-comments',
        ]);
    }
}