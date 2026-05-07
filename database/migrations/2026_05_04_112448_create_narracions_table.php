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
        Schema::create('narracions', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->longText('contenido'); // Cambiado de text a longText para narraciones largas
            $table->string('slug')->unique();
            $table->date('fecha_publicacion');
            $table->enum('estado', ['borrador', 'publicado'])->default('borrador');
            
            // Nuevos campos
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Autor/creador
            $table->integer('orden')->default(1000); // Orden de presentación (1000 = preferente)
            $table->enum('permiso_lectura', ['publico', 'seguidores', 'privado'])->default('publico'); // Permisos de lectura
            $table->integer('count_feedback')->default(0); // Contador de feedback/votaciones
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narracions');
    }
};
