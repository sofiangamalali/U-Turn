<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::table('subscriptions', function (Blueprint $t) {
        $t->string('provider')->default('2checkout');
        $t->string('provider_subscription_id')->nullable(); 
        $t->unsignedInteger('max_ads')->nullable();
        $t->unsignedInteger('remaining_ads')->nullable();  
        $t->string('status')->default('active'); 
      });
    }
    public function down(): void {
      Schema::table('subscriptions', function (Blueprint $t) {
        $t->dropColumn(['provider','provider_subscription_id','max_ads','remaining_ads','status']);
      });
    }
  };
  
