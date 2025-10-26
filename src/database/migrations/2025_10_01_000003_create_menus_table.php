<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->increments('menu_id');
                $table->unsignedInteger('restaurant_id');
                $table->string('name', 100);
                $table->string('menu_img', 255)->default('');
                $table->decimal('price', 10, 2)->nullable();
                $table->text('description')->nullable();

                $table->index(['restaurant_id']);
                $table->foreign('restaurant_id')->references('restaurant_id')->on('restaurants')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
