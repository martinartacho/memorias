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
        Schema::create('linea_narracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linea_id')->constrained()->onDelete('cascade');
            $table->foreignId('narracion_id')->constrained()->onDelete('cascade');
            $table->string('etiqueta')->nullable(); // "1.1", "2.1", null para modo lineal
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
            $table->unique(['linea_id', 'narracion_id']); // cada narración aparece una vez por línea
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_narracion');
    }
};
