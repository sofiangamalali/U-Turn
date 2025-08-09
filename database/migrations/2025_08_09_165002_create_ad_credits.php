<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('ad_credits', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->foreignId('per_ad_feature_id')->constrained('per_ad_features')->cascadeOnDelete();
        $t->unsignedInteger('quantity')->default(1); // عادة 1 لكل عملية شراء
        $t->timestamp('expires_at')->nullable(); // لو عايز تحط صلاحية للكريدت نفسه (اختياري)
        $t->timestamps();
      });
    }
    public function down(): void {
      Schema::dropIfExists('ad_credits');
    }
  };
  
