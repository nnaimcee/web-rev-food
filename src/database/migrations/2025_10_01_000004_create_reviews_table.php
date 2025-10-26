<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->increments('review_id');
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('restaurant_id')->nullable();
                $table->unsignedInteger('menu_id')->nullable();
                $table->integer('rating')->nullable();
                $table->text('comment')->nullable();
                $table->string('menu_name')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['user_id']);
                $table->index(['restaurant_id']);
                $table->index(['menu_id']);

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('restaurant_id')->references('restaurant_id')->on('restaurants')->onDelete('cascade');
                $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
