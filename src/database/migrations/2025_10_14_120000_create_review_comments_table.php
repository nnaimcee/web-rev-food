<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('review_comments')) {
            return; // ตารางมีอยู่แล้ว ข้าม
        }
        Schema::create('review_comments', function (Blueprint $table) {
            $table->integer('comment_id', true);
            $table->unsignedInteger('review_id');
            $table->unsignedInteger('user_id');
            $table->integer('parent_id')->nullable();
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['review_id']);
            $table->index(['parent_id']);

            $table->foreign('review_id')->references('review_id')->on('reviews')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
    }
};
