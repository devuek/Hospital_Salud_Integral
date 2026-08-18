<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Bitacora;
use App\Models\Solicitud;
use App\Models\HistorialCambio;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TecnicoController extends Controller
{

    public function misAsignaciones(Request $request)
    {
        $perPage = $request->integer('per_page', 15);
        $asignaciones = Asignacion::with(['solicitud.unidad.sede', 'solicitud.evidencias', 'bitacoras'])
            ->where('id_usuario_tecnico', $request->user()->id_usuario)
            ->latest()
            ->paginate($perPage);

        return response()->json($asignaciones);
    }

    public function iniciarAtencion(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $solicitud = $asignacion->solicitud;

        if (!in_array($solicitud->estado_solicitud, ['Asignada', 'Abierta', 'Devuelta'])) {
            return response()->json([
                'mensaje' => 'No es posible realizar la acción. Esta solicitud ya se encuentra en atención o fue cerrada.',
            ], 422);
        }

        $valorAnterior = $solicitud->estado_solicitud;
        $solicitud->update(['estado_solicitud' => 'En Proceso']);

        HistorialCambio::create([
            'id_solicitud'     => $solicitud->id_solicitud,
            'id_usuario'       => $request->user()->id_usuario,
            'campo_modificado' => 'estado_solicitud',
            'valor_anterior'   => $valorAnterior,
            'valor_nuevo'      => 'En Proceso',
        ]);

        return response()->json(['mensaje' => 'Estado de la solicitud actualizado a En Proceso.']);
    }

    public function registrarBitacora(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $request->validate([
            'descripcion_trabajo' => 'required|string',
            'materiales'          => 'nullable|string',
            'fecha_inicio'        => 'nullable|date',
            'fecha_fin'           => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $bitacora = Bitacora::create([
            'id_asignacion'      => $asignacion->id_asignacion,
            'descripcion_trabajo' => $request->descripcion_trabajo,
            'materiales'         => $request->materiales,
            'fecha_inicio'       => $request->fecha_inicio,
            'fecha_fin'          => $request->fecha_fin,
        ]);

        return response()->json([
            'mensaje'  => 'Detalle de la bitácora de trabajo guardado exitosamente.',
            'bitacora' => $bitacora,
        ], 201);
    }

    public function verBitacoras(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        return response()->json($asignacion->bitacoras()->latest()->get());
    }

    public function solicitarCierre(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $request->validate([
            'codigo_verificacion' => 'required|string|size:6',
        ]);

        $solicitud = $asignacion->solicitud;

        if ($solicitud->estado_solicitud !== 'En Proceso') {
            return response()->json([
                'mensaje' => 'Acción denegada. Solo se puede solicitar el cierre de solicitudes que estén en proceso.',
            ], 422);
        }

        if (strtoupper($request->codigo_verificacion) !== strtoupper($solicitud->codigo_verificacion)) {
            return response()->json([
                'mensaje' => 'El código de verificación física ingresado es incorrecto. Favor solicitar el OTP correcto al solicitante.',
            ], 422);
        }

        if ($asignacion->bitacoras()->count() === 0) {
            return response()->json([
                'mensaje' => 'No es posible solicitar el cierre. Debe registrar al menos una bitácora de trabajo.',
            ], 422);
        }

        $solicitud->update(['estado_solicitud' => 'Resuelta']);

        HistorialCambio::create([
            'id_solicitud'     => $solicitud->id_solicitud,
            'id_usuario'       => $request->user()->id_usuario,
            'campo_modificado' => 'estado_solicitud',
            'valor_anterior'   => 'En Proceso',
            'valor_nuevo'      => 'Resuelta',
        ]);

        return response()->json([
            'mensaje' => 'Solicitud de revisión para el cierre enviada de conformidad al coordinador.',
        ]);
    }

    public function solicitarReasignacion(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $solicitud = $asignacion->solicitud;

        if (!in_array($solicitud->estado_solicitud, ['Asignada', 'En Proceso'])) {
            return response()->json([
                'mensaje' => 'No es posible solicitar la reasignación. El ticket debe encontrarse en estado Asignada o En Proceso.',
            ], 422);
        }

        $request->validate([
            'motivo' => 'required|string|min:10',
            'id_usuario_tecnico_propuesto' => 'nullable|exists:users,id_usuario',
        ]);

        if ($request->filled('id_usuario_tecnico_propuesto')) {
            $tecnicoPropuesto = \App\Models\User::with('rol')->findOrFail($request->id_usuario_tecnico_propuesto);
            if (!$tecnicoPropuesto->tieneRol('Técnico')) {
                return response()->json([
                    'mensaje' => 'El usuario propuesto no posee el rol de Técnico.',
                ], 422);
            }
        }

        $solicitudReasignacion = \App\Models\SolicitudReasignacion::create([
            'id_solicitud' => $solicitud->id_solicitud,
            'id_usuario_tecnico_solicitante' => $request->user()->id_usuario,
            'id_usuario_tecnico_propuesto' => $request->id_usuario_tecnico_propuesto,
            'motivo' => $request->motivo,
            'estado' => 'Pendiente',
        ]);

        return response()->json([
            'mensaje' => 'Solicitud de reasignación enviada con éxito al líder técnico/coordinador.',
            'solicitud_reasignacion' => $solicitudReasignacion,
        ], 201);
    }

    public function adjuntarEvidencia(Request $request, string $idAsignacion)
    {
        Log::info('[TecnicoController::adjuntarEvidencia] Petición recibida:', [
            'idAsignacion' => $idAsignacion,
            'userId' => $request->user()?->id_usuario,
            'userRol' => $request->user()?->rol?->nombre_rol,
            'hasFile' => $request->hasFile('archivo')
        ]);

        try {
            $query = Asignacion::query();
            if (!$request->user()->tieneRol('Administrador')) {
                $query->where('id_usuario_tecnico', $request->user()->id_usuario);
            }
            $asignacion = $query->findOrFail($idAsignacion);

            $solicitud = $asignacion->solicitud;

            if ($solicitud->estado_solicitud !== 'En Proceso') {
                Log::warning('[TecnicoController::adjuntarEvidencia] La solicitud no está en proceso:', [
                    'solicitudId' => $solicitud->id_solicitud,
                    'estado' => $solicitud->estado_solicitud
                ]);
                return response()->json([
                    'mensaje' => 'Acción denegada. Solo se pueden adjuntar evidencias a solicitudes en proceso.',
                ], 422);
            }

            if (!$request->hasFile('archivo')) {
                Log::error('[TecnicoController::adjuntarEvidencia] No se subió ningún archivo en el request.');
                return response()->json([
                    'mensaje' => 'No se recibió ningún archivo.',
                ], 422);
            }

            try {
                $request->validate([
                    'archivo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                ]);
            } catch (\Illuminate\Validation\ValidationException $ve) {
                Log::error('[TecnicoController::adjuntarEvidencia] Error de validación del archivo:', [
                    'errors' => $ve->errors()
                ]);
                return response()->json([
                    'mensaje' => 'Error de validación: ' . implode(', ', array_flatten($ve->errors())),
                    'errors' => $ve->errors()
                ], 422);
            }

            $path = $request->file('archivo')->store('evidencias', 'public');
            Log::info('[TecnicoController::adjuntarEvidencia] Archivo almacenado exitosamente:', [
                'path' => $path
            ]);

            $evidencia = Evidencia::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'url_recurso'  => Storage::url($path),
                'tipo_archivo' => $request->file('archivo')->getMimeType(),
            ]);

            Log::info('[TecnicoController::adjuntarEvidencia] Registro de evidencia creado con éxito:', [
                'evidenciaId' => $evidencia->id_evidencia,
                'url' => $evidencia->url_recurso
            ]);

            return response()->json([
                'mensaje'   => 'Archivo de evidencia de cierre adjuntado con éxito.',
                'evidencia' => $evidencia,
            ], 201);
        } catch (\Exception $e) {
            Log::error('[TecnicoController::adjuntarEvidencia] Excepción inesperada capturada:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'mensaje' => 'Error interno al procesar el archivo: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pausarTarea(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $request->validate([
            'motivo' => 'required|string|min:5',
        ]);

        $asignacion->update([
            'is_pausada' => true,
            'motivo_pausa' => $request->motivo,
        ]);

        // Registrar en la bitácora de trabajo de la asignación
        Bitacora::create([
            'id_asignacion' => $asignacion->id_asignacion,
            'descripcion_trabajo' => 'Tarea pausada: ' . $request->motivo,
            'materiales' => null,
            'fecha_inicio' => now(),
            'fecha_fin' => now(),
        ]);

        // Registrar en el historial de cambios del ticket
        HistorialCambio::create([
            'id_solicitud'     => $asignacion->id_solicitud,
            'id_usuario'       => $request->user()->id_usuario,
            'campo_modificado' => 'estado_solicitud',
            'valor_anterior'   => $asignacion->solicitud->estado_solicitud,
            'valor_nuevo'      => 'Pausada',
        ]);

        return response()->json([
            'mensaje' => 'La tarea ha sido pausada exitosamente.',
            'asignacion' => $asignacion,
        ]);
    }

    public function reanudarTarea(Request $request, string $idAsignacion)
    {
        $query = Asignacion::query();
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->findOrFail($idAsignacion);

        $asignacion->update([
            'is_pausada' => false,
            'motivo_pausa' => null,
        ]);

        // Registrar en la bitácora
        Bitacora::create([
            'id_asignacion' => $asignacion->id_asignacion,
            'descripcion_trabajo' => 'Tarea reanudada.',
            'materiales' => null,
            'fecha_inicio' => now(),
            'fecha_fin' => now(),
        ]);

        return response()->json([
            'mensaje' => 'La tarea ha sido reanudada con éxito.',
            'asignacion' => $asignacion,
        ]);
    }

    public function cancelarTarea(Request $request, string $idAsignacion)
    {
        $query = Asignacion::with('solicitud');
        if (!$request->user()->tieneRol('Administrador')) {
            $query->where('id_usuario_tecnico', $request->user()->id_usuario);
        }
        $asignacion = $query->where(function ($q) use ($idAsignacion) {
            $q->where('id_asignacion', $idAsignacion)
              ->orWhere('id_solicitud', $idAsignacion);
        })->firstOrFail();

        $solicitud = $asignacion->solicitud;

        $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        $motivo = $request->input('motivo', 'Cancelación solicitada por el técnico.');
        $tecnicoNombre = $request->user()->nombre . ' ' . $request->user()->apellido;

        // Registrar en bitácora
        Bitacora::create([
            'id_asignacion'       => $asignacion->id_asignacion,
            'descripcion_trabajo' => "Cancelación de tarea por el técnico {$tecnicoNombre}. Motivo: {$motivo}. Notificación enviada al Coordinador para reasignación.",
            'materiales'          => null,
            'fecha_inicio'        => now(),
            'fecha_fin'           => now(),
        ]);

        // Registrar en auditoría la cancelación específica del técnico para notificación
        HistorialCambio::create([
            'id_solicitud'     => $solicitud->id_solicitud,
            'id_usuario'       => $request->user()->id_usuario,
            'campo_modificado' => 'cancelacion_tecnico',
            'valor_anterior'   => $solicitud->estado_solicitud,
            'valor_nuevo'      => "El técnico {$tecnicoNombre} canceló la tarea. Motivo: {$motivo}",
        ]);

        // Regresar solicitud a estado Abierta para que el Coordinador la reasigne
        $solicitud->update(['estado_solicitud' => 'Abierta']);

        // Eliminar la asignación actual para liberar el ticket
        $asignacion->delete();

        return response()->json([
            'mensaje' => 'Se ha enviado la notificación al coordinador y la tarea fue cancelada exitosamente.',
        ]);
    }
}
