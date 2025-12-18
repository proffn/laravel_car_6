<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendshipsTable extends Migration
{
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Кто отправляет запрос
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade'); // Кого добавляют
            $table->enum('status', ['pending', 'accepted', 'rejected', 'blocked'])->default('pending');
            $table->timestamps();
            
            // Уникальная комбинация, чтобы нельзя было отправить несколько одинаковых запросов
            $table->unique(['user_id', 'friend_id']);
            
            // Индекс для быстрого поиска запросов дружбы
            $table->index(['user_id', 'status']);
            $table->index(['friend_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('friendships');
    }
}