<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Car;

class CheckDatabase extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Check database state';

    public function handle()
    {
        $this->info('=== ПРОВЕРКА БАЗЫ ДАННЫХ ===');
        
        // Пользователи
        $this->info("\n1. ПОЛЬЗОВАТЕЛИ:");
        $users = User::all();
        foreach ($users as $user) {
            $status = $user->is_admin ? 'true' : 'false';
            $this->line("   - {$user->name} ({$user->email}): is_admin = {$status}");
        }
        
        // Автомобили
        $this->info("\n2. АВТОМОБИЛИ:");
        $cars = Car::all();
        $this->line("   Всего машин: " . $cars->count());
        foreach ($cars as $car) {
            $owner = $car->user ? $car->user->name : 'НЕТ';
            $this->line("   - {$car->brand} {$car->model}: user_id = {$car->user_id} (владелец: {$owner})");
        }
        
        // Обновляем администратора
        if ($users->count() > 0) {
            $this->info("\n3. ОБНОВЛЕНИЕ ПРАВ:");
            User::where('email', 'admin@example.com')->update(['is_admin' => true]);
            $admin = User::where('email', 'admin@example.com')->first();
            $this->line("   ✓ {$admin->name} теперь администратор: is_admin = " . ($admin->is_admin ? 'true' : 'false'));
        }
        
        return Command::SUCCESS;
    }
}