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
        Schema::create('narracion_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('narracion_id')->constrained('narracions')->cascadeOnDelete();
            $table->string('identifier', 128); // token suscriptor | "u:{user_id}" | "s:{session_id}"
            $table->enum('tipo', ['like', 'dislike']);
            $table->timestamp('created_at')->useCurrent();

            // Un voto por persona y narración
            $table->unique(['narracion_id', 'identifier']);
            $table->index('narracion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narracion_reactions');
    }
};
