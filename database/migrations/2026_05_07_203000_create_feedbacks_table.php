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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('narracion_id')->constrained('narraciones')->onDelete('cascade');
            $table->string('tipo_feedback'); // excelente, bueno, regular
            $table->text('comentario')->nullable();
            $table->string('email');
            $table->string('nombre')->nullable(); // nombre del usuario (opcional)
            $table->ipAddress('ip_address')->nullable(); // para prevenir spam
            $table->boolean('aprobado')->default(false); // para moderación
            $table->timestamps();
            
            // Índices
            $table->index(['narracion_id', 'aprobado']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
