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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_make_id')->constrained();
            $table->foreignId('car_model_id')->nullable()->constrained();
            $table->year('manufacture_year');
            $table->unsignedInteger('mileage')->nullable();
            $table->enum('transmission_type', ['automatic', 'manual']);
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid']);
            $table->enum('exterior_color', [
                'black',
                'white',
                'silver',
                'gray',
                'blue',
                'red',
                'green',
                'brown',
                'beige',
                'gold',
                'orange',
                'purple',
                'yellow',
                'maroon',
                'other'
            ]);
            $table->enum('interior_color', [
                'black',
                'white',
                'silver',
                'gray',
                'blue',
                'red',
                'green',
                'brown',
                'beige',
                'gold',
                'orange',
                'purple',
                'yellow',
                'maroon',
                'other'
            ]);
            $table->unsignedTinyInteger('doors');
            $table->unsignedTinyInteger('seating_capacity')->nullable();
            $table->unsignedSmallInteger('horsepower')->nullable();
            $table->enum('steering_side', ['right-hand', 'left-hand']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
