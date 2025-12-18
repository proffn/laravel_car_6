<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCarsTable extends Migration
{
    public function up()
    {
        // Проверяем, существует ли колонка
        if (!Schema::hasColumn('cars', 'user_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('cars', 'user_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
}