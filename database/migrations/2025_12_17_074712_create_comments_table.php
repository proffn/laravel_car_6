<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade'); // Связь с автомобилем
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Кто оставил комментарий
            $table->text('content'); // Текст комментария
            $table->softDeletes(); // Мягкое удаление
            $table->timestamps();
            
            // Индекс для быстрого поиска комментариев по автомобилю
            $table->index(['car_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}