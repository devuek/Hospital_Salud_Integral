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
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

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
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

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
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

        return response()->json($asignacion->bitacoras()->latest()->get());
    }

    public function solicitarCierre(Request $request, string $idAsignacion)
    {
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

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
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

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
        $asignacion = Asignacion::where('id_usuario_tecnico', $request->user()->id_usuario)
            ->findOrFail($idAsignacion);

        $solicitud = $asignacion->solicitud;

        if ($solicitud->estado_solicitud !== 'En Proceso') {
            return response()->json([
                'mensaje' => 'Acción denegada. Solo se pueden adjuntar evidencias a solicitudes en proceso.',
            ], 422);
        }

        $request->validate([
            'archivo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('archivo')->store('evidencias', 'public');

        $evidencia = Evidencia::create([
            'id_solicitud' => $solicitud->id_solicitud,
            'url_recurso'  => Storage::url($path),
            'tipo_archivo' => $request->file('archivo')->getMimeType(),
        ]);

        return response()->json([
            'mensaje'   => 'Archivo de evidencia de cierre adjuntado con éxito.',
            'evidencia' => $evidencia,
        ], 201);
    }
}
