<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Asignacion;
use Illuminate\Support\Facades\Log;

class ValidarIntegridadTicket
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {

        $id = $request->route('id') ?? $request->route('solicitud') ?? $request->route('asignacion');

        if ($id) {

            $solicitud = Solicitud::find($id);

            if ($solicitud) {
                if (!$solicitud->validarIntegridad()) {
                    Log::critical("Alerta de Seguridad: Intento de acceso o manipulación de ticket corrupto / alterado.", [
                        'id_solicitud' => $solicitud->id_solicitud,
                        'id_usuario'   => $request->user()?->id_usuario,
                        'firma_esperada' => $solicitud->calcularFirma(),
                        'firma_recibida' => $solicitud->firma_integridad,
                    ]);

                    return response()->json([
                        'mensaje' => 'Verificación de integridad fallida. La firma digital del ticket no coincide, los datos fueron alterados.',
                        'error_code' => 'INTEGRITY_VERIFICATION_FAILED'
                    ], 500);
                }
            } else {

                $asignacion = Asignacion::with('solicitud')->find($id);

                if ($asignacion && $asignacion->solicitud) {
                    if (!$asignacion->solicitud->validarIntegridad()) {
                        Log::critical("Alerta de Seguridad: Intento de acceso a asignación vinculada a un ticket corrupto.", [
                            'id_asignacion' => $asignacion->id_asignacion,
                            'id_solicitud'  => $asignacion->solicitud->id_solicitud,
                            'id_usuario'    => $request->user()?->id_usuario,
                        ]);

                        return response()->json([
                            'mensaje' => 'Verificación de integridad fallida. El ticket vinculado a esta asignación ha sido alterado.',
                            'error_code' => 'INTEGRITY_VERIFICATION_FAILED'
                        ], 500);
                    }
                }
            }
        }

        return $next($request);
    }
}
