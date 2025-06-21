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
        Schema::create('per_ad_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('duration_days')->nullable();
            $table->boolean('is_free')->default(false);
            $table->integer('order')->default(0);
            $table->enum('level', ['basic', 'highlight', 'top', 'premium'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('per_ad_features');
    }
};
