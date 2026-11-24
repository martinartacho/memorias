<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            // Serie por capítulos: sinopsis que presenta la serie (se escribe en el capítulo 1)
            $table->text('sinopsis')->nullable()->after('contenido');
        });
    }

    public function down(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            $table->dropColumn('sinopsis');
        });
    }
};
