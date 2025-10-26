<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('review_likes')) {
            Schema::create('review_likes', function (Blueprint $table) {
                $table->increments('like_id');
                $table->unsignedInteger('review_id');
                $table->unsignedInteger('user_id');
                $table->timestamp('created_at')->useCurrent();

                $table->index(['review_id']);
                $table->index(['user_id']);

                $table->foreign('review_id')->references('review_id')->on('reviews')->onDelete('cascade');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('review_likes');
    }
};
