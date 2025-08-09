<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('context_type'); 
            $t->unsignedBigInteger('context_id');
            $t->string('ext_ref')->unique();     
            $t->string('provider_ref')->nullable(); 
            $t->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $t->decimal('amount', 10, 2)->nullable();
            $t->string('currency', 3)->nullable();
            $t->timestamps();
            $t->index(['context_type', 'context_id']);
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->decimal('amount', 10, 2)->nullable();
            $t->string('currency', 3)->nullable();
            $t->string('provider_order_ref')->nullable(); // REFNO/INVOICE
            $t->enum('event', ['initial', 'recurring', 'refund', 'chargeback'])->default('initial');
            $t->string('status')->default('paid');
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->unique(['provider_order_ref', 'event']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
    }
};
