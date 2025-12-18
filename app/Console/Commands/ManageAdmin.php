<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ManageAdmin extends Command
{
    protected $signature = 'admin:manage';
    protected $description = 'Интерактивное управление администраторами';

    public function handle()
    {
        $this->showHeader();
        
        while (true) {
            $this->showMenu();
            $choice = $this->ask('Выберите действие (1-4):');
            
            switch ($choice) {
                case '1':
                    $this->listUsers();
                    break;
                    
                case '2':
                    $this->makeAdmin();
                    break;
                    
                case '3':
                    $this->removeAdmin();
                    break;
                    
                case '4':
                    $this->info('Выход...');
                    return Command::SUCCESS;
                    
                default:
                    $this->error('Неверный выбор!');
            }
            
            $this->line(str_repeat('=', 50));
        }
    }
    
    private function showHeader()
    {
        $this->line(' УПРАВЛЕНИЕ АДМИНИСТРАТОРАМИ АВТОМОБИЛЕЙ');
        $this->newLine();
    }
    
    private function showMenu()
    {
        $this->info(' МЕНЮ:');
        $this->line('1.  Список всех пользователей');
        $this->line('2.  Назначить администратором');
        $this->line('3.  Убрать права администратора');
        $this->line('4.  Выход');
        $this->newLine();
    }
    
    private function listUsers()
    {
        $this->info(' СПИСОК ПОЛЬЗОВАТЕЛЕЙ:');
        $this->newLine();
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->warn('Пользователей нет в системе!');
            return;
        }
        
        $headers = ['ID', 'Имя', 'Email', 'Админ?', 'Автомобилей', 'Создан'];
        $rows = [];
        
        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->is_admin ? ' да' : ' нет',
                $user->cars()->count(),
                $user->created_at->format('d.m.Y')
            ];
        }
        
        $this->table($headers, $rows);
        $this->newLine();
    }
    
    private function makeAdmin()
    {
        $this->info(' НАЗНАЧЕНИЕ АДМИНИСТРАТОРА:');
        $this->newLine();
        
        // Показать пользователей без админки
        $users = User::where('is_admin', false)->get();
        
        if ($users->isEmpty()) {
            $this->warn('Все пользователи уже администраторы!');
            return;
        }
        
        $this->line('Пользователи без прав администратора:');
        $this->newLine();
        
        $choices = [];
        foreach ($users as $user) {
            $choices[$user->id] = "{$user->name} ({$user->email})";
            $this->line("  [{$user->id}] {$user->name} <{$user->email}>");
        }
        
        $this->newLine();
        $userId = $this->ask('Введите ID пользователя для назначения администратором:');
        
        if (!isset($choices[$userId])) {
            $this->error("Пользователь с ID {$userId} не найден или уже администратор!");
            return;
        }
        
        // Подтверждение
        $user = User::find($userId);
        if (!$this->confirm("Назначить пользователя '{$user->name}' администратором?")) {
            $this->info('Отменено.');
            return;
        }
        
        $user->is_admin = true;
        $user->save();
        
        $this->newLine();
        $this->info(" Пользователь '{$user->name}' успешно назначен администратором!");
        $this->line(" Email: {$user->email}");
        $this->line(" ID: {$user->id}");
        $this->newLine();
    }
    
    private function removeAdmin()
    {
        $this->info(' УДАЛЕНИЕ ПРАВ АДМИНИСТРАТОРА:');
        $this->newLine();
        
        // Показать администраторов
        $admins = User::where('is_admin', true)->get();
        
        if ($admins->isEmpty()) {
            $this->warn('В системе нет администраторов!');
            return;
        }
        
        $this->line('Текущие администраторы:');
        $this->newLine();
        
        $choices = [];
        foreach ($admins as $admin) {
            $choices[$admin->id] = "{$admin->name} ({$admin->email})";
            $this->line("  [{$admin->id}] {$admin->name} <{$admin->email}>");
        }
        
        $this->newLine();
        $userId = $this->ask('Введите ID администратора для снятия прав:');
        
        if (!isset($choices[$userId])) {
            $this->error("Администратор с ID {$userId} не найден!");
            return;
        }
        
        $user = User::find($userId);
        
        // Нельзя снять админку с самого себя
        if ($user->id === 1) { 
            $this->error(' Нельзя снять права с главного администратора (ID 1)!');
            return;
        }
        
        if (!$this->confirm("Убрать права администратора у пользователя '{$user->name}'?")) {
            $this->info('Отменено.');
            return;
        }
        
        $user->is_admin = false;
        $user->save();
        
        $this->newLine();
        $this->info(" Пользователь '{$user->name}' больше не администратор!");
        $this->line(" Email: {$user->email}");
        $this->line(" ID: {$user->id}");
        $this->newLine();
    }
}