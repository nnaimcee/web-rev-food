<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('review_likes', function (Blueprint $table) {
            $table->unique(['review_id', 'user_id'], 'review_likes_unique_review_user');
        });
    }

    public function down(): void
    {
        Schema::table('review_likes', function (Blueprint $table) {
            $table->dropUnique('review_likes_unique_review_user');
        });
    }
};

