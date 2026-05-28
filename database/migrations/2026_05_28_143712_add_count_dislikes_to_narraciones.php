<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            $table->integer('count_dislikes')->default(0)->after('count_feedback');
        });
    }

    public function down(): void
    {
        Schema::table('narracions', function (Blueprint $table) {
            $table->dropColumn('count_dislikes');
        });
    }
};
