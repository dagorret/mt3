<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->nullOnDelete();
            
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('legajo', 30)->nullable()->unique();
            $table->string('sexo', 10)->nullable(); // Cambiado para mayor compatibilidad con SQLite
            $table->unsignedSmallInteger('ano_nacimiento')->nullable();
            
            $table->string('movil', 50)->nullable();
            $table->string('interno', 20)->nullable();
            $table->string('oficina', 100)->nullable();
            $table->string('domicilio')->nullable();

            $table->string('cargo')->nullable();
            $table->string('titulo')->nullable();
            $table->unsignedSmallInteger('ano_ingreso')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
