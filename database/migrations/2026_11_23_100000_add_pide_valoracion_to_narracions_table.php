<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            // Serie por capítulos: si es true, para abrir el capítulo siguiente hay que valorar este con 👍/👎
            $table->boolean('pide_valoracion')->default(false)->after('after_id');
        });
    }

    public function down(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            $table->dropColumn('pide_valoracion');
        });
    }
};
