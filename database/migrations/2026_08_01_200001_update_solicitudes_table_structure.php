<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->enum('tipo_servicio', ['Soporte Técnico', 'Servicios Generales'])
                ->after('id_unidad')
                ->default('Soporte Técnico');
        });

        DB::statement("ALTER TABLE solicitudes MODIFY COLUMN estado_solicitud ENUM('Abierta', 'Asignada', 'En Proceso', 'Resuelta', 'Validada', 'Cerrada', 'Devuelta', 'Cancelada') NOT NULL DEFAULT 'Abierta'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn('tipo_servicio');
        });

        DB::statement("ALTER TABLE solicitudes MODIFY COLUMN estado_solicitud ENUM('Abierta', 'Asignada', 'En Proceso', 'Validada', 'Cerrada', 'Devuelta', 'Cancelada') NOT NULL DEFAULT 'Abierta'");
    }
};
