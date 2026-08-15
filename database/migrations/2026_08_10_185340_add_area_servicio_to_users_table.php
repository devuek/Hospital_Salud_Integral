<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Área de especialidad del técnico: define qué tipo de solicitudes puede atender
            $table->enum('area_servicio', [
                'Servicios Generales',
                'Soporte Técnico',
            ])->nullable()->after('id_sede');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('area_servicio');
        });
    }
};
