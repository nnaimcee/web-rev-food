<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('review_hashtags')) {
            return; // ตารางมีอยู่แล้ว ข้าม
        }
        Schema::create('review_hashtags', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('review_id');
            $table->string('tag');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tag']);
            $table->index(['review_id']);
            $table->foreign('review_id')->references('review_id')->on('reviews')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_hashtags');
    }
};
