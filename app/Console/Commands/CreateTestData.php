<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Car;
use Illuminate\Support\Facades\Hash;

class CreateTestData extends Command
{
    protected $signature = 'test:data';
    protected $description = 'Create test users and cars';

    public function handle()
    {
        // Очищаем
        Car::query()->delete();
        User::query()->delete();

        // Создаем администратора
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Создаем обычного пользователя
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Создаем автомобили
        Car::create([
            'brand' => 'Toyota',
            'model' => 'Camry',
            'year' => 2018,
            'mileage' => 85000,
            'color' => 'Белый',
            'body_type' => 'Седан',
            'detailed_description' => 'Toyota Camry 2018 года в отличном состоянии.',
            'user_id' => $admin->id,
        ]);

        Car::create([
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2017,
            'mileage' => 120000,
            'color' => 'Синий',
            'body_type' => 'Хэтчбек',
            'detailed_description' => 'Ford Focus 2017 года, универсал.',
            'user_id' => $user->id,
        ]);

        Car::create([
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2019,
            'mileage' => 45000,
            'color' => 'Красный',
            'body_type' => 'Седан',
            'detailed_description' => 'Honda Civic 2019 года, хэтчбек.',
            'user_id' => $user->id,
        ]);

        $this->info(' Администратор создан: admin@example.com / password');
        $this->info(' Пользователь создан: user@example.com / password');
        $this->info(' Автомобилей создано: ' . Car::count());
        
        return Command::SUCCESS;
    }
}