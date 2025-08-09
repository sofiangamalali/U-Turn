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
        Schema::table('listings', function (Blueprint $t) {
            $t->enum('status', ['draft', 'active', 'expired', 'pending_review', 'rejected'])
                ->default('active')->after('type');
            $t->enum('level', ['basic', 'highlight', 'premium'])
                ->default('basic')->after('status');
            $t->timestamp('published_at')->nullable()->after('level');
            $t->timestamp('expires_at')->nullable()->after('published_at');

            // مصدر الاستهلاك: per_ad أو subscription مع الـ id المرجعي
            $t->enum('publish_source', ['per_ad', 'subscription'])->nullable()->after('expires_at');
            $t->unsignedBigInteger('publish_source_id')->nullable()->after('publish_source');

            $t->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $t) {
            $t->dropIndex(['status', 'expires_at']);
            $t->dropColumn(['status', 'level', 'published_at', 'expires_at', 'publish_source', 'publish_source_id']);
        });
    }
};
