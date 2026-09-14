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
            
            // Relación 1:1 estricta con Users (Opcional)
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            // Relación con Unidad
            $table->foreignId('unidad_id')
                ->constrained('unidades')
                ->cascadeOnDelete();

            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('legajo')->nullable()->unique();
            $table->enum('sexo', ['M', 'F', 'X'])->nullable();
            $table->unsignedSmallInteger('ano_nacimiento')->nullable();
            $table->string('movil')->nullable();
            $table->string('interno')->nullable();
            $table->string('oficina')->nullable();
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
