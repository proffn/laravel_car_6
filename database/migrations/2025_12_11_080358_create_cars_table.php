<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand');              // Марка
            $table->string('model');              // Модель
            $table->integer('year');              // Год
            $table->integer('mileage');           // Пробег
            $table->string('color');              // Цвет
            $table->string('body_type');          // Тип кузова
            $table->text('detailed_description'); // Подробное описание
            $table->string('image')->nullable();  // Изображение
            $table->softDeletes();                // Мягкое удаление
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cars');
    }
}