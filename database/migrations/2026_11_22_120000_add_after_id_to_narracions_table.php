<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            // Serie por capítulos: esta narración va después de otra (null = suelta o capítulo 1)
            $table->foreignId('after_id')->nullable()->after('orden')
                ->constrained('narracions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('after_id');
        });
    }
};
