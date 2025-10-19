<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ใช้คำสั่ง SQL ตรงเพื่อหลีกเลี่ยงการพึ่งพา doctrine/dbal
        DB::statement('ALTER TABLE reviews MODIFY rating DECIMAL(2,1) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE reviews MODIFY rating INT NULL');
    }
};

