<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->index(['auditable_type', 'auditable_id']);

            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tipo_evento');
            $table->text('justificacion')->nullable();

            // Usamos json estándar para SQLite
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });

        // Trigger de inmutabilidad compatible con SQLite
        DB::unprepared("
            CREATE TRIGGER tr_bitacora_no_update
            BEFORE UPDATE ON bitacora
            FOR EACH ROW
            BEGIN
                SELECT RAISE(FAIL, 'Operación no permitida: La bitácora es inmutable y no acepta UPDATE.');
            END;

            CREATE TRIGGER tr_bitacora_no_delete
            BEFORE DELETE ON bitacora
            FOR EACH ROW
            BEGIN
                SELECT RAISE(FAIL, 'Operación no permitida: La bitácora es inmutable y no acepta DELETE.');
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS tr_bitacora_no_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_bitacora_no_delete;");
        Schema::dropIfExists('bitacora');
    }
};
