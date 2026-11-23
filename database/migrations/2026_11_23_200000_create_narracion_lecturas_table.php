<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narracion_lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('narracion_id')->constrained('narracions')->cascadeOnDelete();
            $table->string('identifier', 128);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['narracion_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narracion_lecturas');
    }
};
