<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('review_comments')) {
            Schema::table('review_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('review_comments', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('review_comments') && Schema::hasColumn('review_comments', 'updated_at')) {
            Schema::table('review_comments', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};

