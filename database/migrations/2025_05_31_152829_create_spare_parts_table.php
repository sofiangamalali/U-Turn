<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_quantity')->default(0);
            $table->foreignId('car_make_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('car_model_id')->nullable()->constrained()->onDelete('set null');
            $table->year('compatible_year_from')->nullable();
            $table->year('compatible_year_to')->nullable();
            $table->enum('category', [
                'engine',
                'transmission',
                'brakes',
                'suspension',
                'electrical',
                'interior',
                'exterior',
                'cooling',
                'fuel_system',
                'body',
                'lighting',
                'other'
            ]);
            $table->enum('condition', ['new', 'used'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
