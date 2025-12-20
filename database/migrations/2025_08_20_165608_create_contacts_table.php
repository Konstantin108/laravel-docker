<?php

use App\Dictionaries\TableDictionary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* TODO kpstya
    - telegram должен быть unique
    - user_id - кажется unique тут лишний
    - проверить корректность остальных миграций */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableDictionary::CONTACTS, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained(TableDictionary::USERS)
                ->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('telegram')->nullable();
            $table->timestamps();
        });
    }

    // TODO kpstya - assertDispatchedTimes() добавить в тесты

    public function down(): void
    {
        Schema::dropIfExists(TableDictionary::CONTACTS);
    }
};
