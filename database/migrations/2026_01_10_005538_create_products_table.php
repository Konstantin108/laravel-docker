<?php

use App\Dictionaries\TableDictionary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TableDictionary::PRODUCTS, static function (Blueprint $table): void {
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
