<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Car;

class FixCarOwners extends Command
{
    protected $signature = 'fix:owners';
    protected $description = 'Fix car ownership';

    public function handle()
    {
        $this->info('=== Исправление владельцев автомобилей ===');
        
        // Найти пользователей
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'user@example.com')->first();
        
        $this->info("Admin: ID = {$admin->id}, Name = {$admin->name}");
        $this->info("User: ID = {$user->id}, Name = {$user->name}");
        
        // Исправить владельцев
        $updated = Car::query()->update(['user_id' => null]);
        $this->info("Сброшено владельцев у {$updated} машин");
        
        // Назначить правильных владельцев
        Car::where('brand', 'Toyota')->update(['user_id' => $admin->id]);
        Car::whereIn('brand', ['Ford', 'Honda'])->update(['user_id' => $user->id]);
        
        // Проверить
        $this->info("\n=== Результат ===");
        $cars = Car::with('user')->get();
        foreach ($cars as $car) {
            $owner = $car->user ? $car->user->name : 'НЕТ';
            $this->line("{$car->brand} {$car->model}: user_id = {$car->user_id} (владелец: {$owner})");
        }
        
        return Command::SUCCESS;
    }
}