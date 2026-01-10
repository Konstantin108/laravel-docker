<?php

use App\Dictionaries\TableDictionary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TODO kpstya добавить сидеры

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableDictionary::PRODUCTS, function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->index()
                ->constrained(TableDictionary::PRODUCT_CATEGORIES)
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableDictionary::PRODUCTS);
    }
};
