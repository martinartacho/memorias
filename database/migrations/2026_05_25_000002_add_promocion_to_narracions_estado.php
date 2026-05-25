<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE narracions MODIFY COLUMN estado ENUM('borrador','publicado','promocion') NOT NULL DEFAULT 'borrador'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE narracions MODIFY COLUMN estado ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador'");
    }
};
