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
        Schema::create('linea_conexiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_id')->constrained('linea_narracion')->onDelete('cascade');
            $table->foreignId('to_id')->constrained('linea_narracion')->onDelete('cascade');
            $table->string('etiqueta')->nullable(); // texto del enlace mostrado al lector
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_conexiones');
    }
};
