<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta las reglas de inmutabilidad en PostgreSQL.
     */
    public function up(): void
    {
        /*
        DB::unprepared("
            CREATE OR REPLACE FUNCTION prevenir_modificacion_bitacora()
            RETURNS TRIGGER AS $$
            BEGIN
                RAISE EXCEPTION 'Operación no permitida: La bitácora es inmutable y no acepta UPDATE ni DELETE.'
                    USING ERRCODE = '45000';
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER tr_bitacora_inmutable
            BEFORE UPDATE OR DELETE ON bitacora
            FOR EACH ROW
            EXECUTE FUNCTION prevenir_modificacion_bitacora();
        ");
        */
    }

    /**
     * Revierte el trigger y la función.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_bitacora_inmutable ON bitacora;
            DROP FUNCTION IF EXISTS prevenir_modificacion_bitacora();
        ");
    }
};