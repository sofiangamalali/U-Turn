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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('body_type', ['sedan', 'hatchback', 'suv', 'coupe', 'convertible', 'pickup', 'van', 'wagon', 'crossover'])->nullable();
            $table->decimal('consumption', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('body_type');
            $table->dropColumn('consumption');
        });
    }
};
