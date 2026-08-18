<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Asignacion;
use App\Models\User;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;

class CoordinadorController extends Controller
{

    public function solicitudesPendientes(Request $request)
    {
        $query = Solicitud::with(['solicitante', 'unidad.sede', 'evidencias'])
            ->whereIn('estado_solicitud', ['Abierta', 'Devuelta']);

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('id_unidad')) {
            $query->where('id_unidad', $request->id_unidad);
        }

        $perPage = $request->integer('per_page', 15);
        return response()->json($query->oldest()->paginate($perPage));
    }

    public function historialCompleto(Request $request)
    {
        $query = Solicitud::with(['solicitante', 'unidad.sede', 'asignacion.tecnico', 'asignacion.bitacoras', 'evidencias']);

        if ($request->filled('buscar')) {
            $raw = trim($request->buscar);
            $clean = preg_replace('/^#(sol|req)-/i', '', $raw);
            $clean = ltrim($clean, '#');
            $term = '%' . $clean . '%';
            $termRaw = '%' . $raw . '%';

            $query->where(function ($q) use ($term, $termRaw) {
                $q->where('titulo', 'like', $termRaw)
                  ->orWhere('descripcion', 'like', $termRaw)
                  ->orWhere('id_solicitud', 'like', $term)
                  ->orWhere('codigo_verificacion', 'like', $term)
                  ->orWhere('prioridad', 'like', $termRaw)
                  ->orWhere('estado_solicitud', 'like', $termRaw)
                  ->orWhereHas('unidad', function ($qu) use ($termRaw) {
                      $qu->where('nombre_unidad', 'like', $termRaw)
                        ->orWhereHas('sede', function ($qs) use ($termRaw) {
                            $qs->where('nombre_sede', 'like', $termRaw);
                        });
                  })
                  ->orWhereHas('solicitante', function ($qs) use ($termRaw) {
                      $qs->where('nombre', 'like', $termRaw)
                        ->orWhere('apellido', 'like', $termRaw)
                        ->orWhere('correo', 'like', $termRaw);
                  })
                  ->orWhereHas('asignacion.tecnico', function ($qt) use ($termRaw) {
                      $qt->where('nombre', 'like', $termRaw)
                        ->orWhere('apellido', 'like', $termRaw);
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado_solicitud', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('id_sede')) {
            $query->whereHas('unidad', function ($q) use ($request) {
                $q->where('id_sede', $request->id_sede);
            });
        }

        $perPage = $request->integer('per_page', 15);
        return response()->json($query->latest()->paginate($perPage));
    }

    public function clasificar(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $request->validate([
            'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
        ]);

        $anterior = $solicitud->prioridad;
        $solicitud->update(['prioridad' => $request->prioridad]);

        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'prioridad', $anterior, $request->prioridad);

        return response()->json([
            'mensaje'   => 'Prioridad asignada con éxito.',
            'solicitud' => $solicitud,
        ]);
    }

    public function devolver(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $request->validate([
            'motivo' => 'required|string',
        ]);

        $estadoAnterior = $solicitud->estado_solicitud;
        $solicitud->update(['estado_solicitud' => 'Devuelta']);

        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'estado_solicitud', $estadoAnterior, 'Devuelta');

        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'motivo_devolucion', null, $request->motivo);

        return response()->json(['mensaje' => 'La solicitud ha sido devuelta al solicitante de manera formal.']);
    }

    public function asignar(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if (in_array($solicitud->estado_solicitud, ['Cerrada', 'Validada', 'Cancelada'])) {
            return response()->json([
                'mensaje' => 'No se puede asignar un técnico a una solicitud finalizada o cancelada.',
            ], 422);
        }

        $request->validate([
            'id_usuario_tecnico' => 'required|exists:users,id_usuario',
        ]);

        $tecnico = User::with('rol')->findOrFail($request->id_usuario_tecnico);
        if (!$tecnico->tieneRol('Técnico')) {
            return response()->json(['mensaje' => 'El usuario seleccionado no posee el rol de Técnico. Favor verificar.'], 422);
        }

        Asignacion::where('id_solicitud', $solicitud->id_solicitud)->delete();

        $asignacion = Asignacion::create([
            'id_solicitud'           => $solicitud->id_solicitud,
            'id_usuario_tecnico'     => $request->id_usuario_tecnico,
            'id_usuario_coordinador' => $request->user()->id_usuario,
            'fecha_asignacion'       => now(),
        ]);

        $estadoAnterior = $solicitud->estado_solicitud;
        $solicitud->update(['estado_solicitud' => 'Asignada']);

        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'estado_solicitud', $estadoAnterior, 'Asignada');

        return response()->json([
            'mensaje'    => 'La solicitud ha sido asignada formalmente al técnico correspondiente.',
            'asignacion' => $asignacion->load('tecnico'),
            'solicitud'  => $solicitud->fresh()->load(['unidad.sede', 'asignacion.tecnico', 'asignacion.bitacoras', 'evidencias', 'historial.usuario', 'solicitante']),
        ], 201);
    }

    public function validarCierre(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado_solicitud !== 'Resuelta') {
            return response()->json([
                'mensaje' => 'No es posible realizar la acción. Solo se puede cerrar una solicitud que se encuentre resuelta.',
            ], 422);
        }

        $solicitud->update(['estado_solicitud' => 'Cerrada']);
        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'estado_solicitud', 'Resuelta', 'Cerrada');

        return response()->json(['mensaje' => 'Solicitud validada y cerrada correctamente.']);
    }

    public function listarReasignacionesPendientes(Request $request)
    {
        $perPage = $request->integer('per_page', 15);
        $reasignaciones = \App\Models\SolicitudReasignacion::with([
            'solicitud.unidad.sede',
            'tecnicoSolicitante',
            'tecnicoPropuesto'
        ])->where('estado', 'Pendiente')
          ->latest()
          ->paginate($perPage);

        return response()->json($reasignaciones);
    }

    public function obtenerCargaTrabajoTecnicos(Request $request)
    {
        $tecnicos = \App\Models\User::whereHas('rol', fn($q) => $q->where('nombre_rol', 'Técnico'))
            ->where('estado', 'Activo')
            ->withCount(['asignacionesTecnico as tickets_activos' => function ($q) {
                $q->whereHas('solicitud', fn($sol) => $sol->whereIn('estado_solicitud', ['Asignada', 'En Proceso']));
            }])
            ->orderBy('tickets_activos', 'asc')
            ->get()
            ->map(fn($t) => [
                'id_usuario'      => $t->id_usuario,
                'nombre_completo' => $t->nombre . ' ' . $t->apellido,
                'tickets_activos' => $t->tickets_activos,
            ]);

        return response()->json($tecnicos);
    }

    public function resolverReasignacion(Request $request, string $idReasignacion)
    {
        $reasignacion = \App\Models\SolicitudReasignacion::where('estado', 'Pendiente')
            ->findOrFail($idReasignacion);

        $request->validate([
            'decision' => 'required|in:Aprobada,Rechazada',
            'id_usuario_tecnico_nuevo' => 'required_if:decision,Aprobada|exists:users,id_usuario',
            'comentarios_coordinador' => 'nullable|string',
        ]);

        $solicitud = $reasignacion->solicitud;

        if ($request->decision === 'Rechazada') {
            $reasignacion->update([
                'estado' => 'Rechazada',
                'id_usuario_coordinador' => $request->user()->id_usuario,
                'comentarios_coordinador' => $request->comentarios_coordinador,
            ]);

            return response()->json([
                'mensaje' => 'La solicitud de reasignación ha sido rechazada de manera formal.',
                'reasignacion' => $reasignacion,
            ]);
        }

        $nuevoTecnico = \App\Models\User::with('rol')->findOrFail($request->id_usuario_tecnico_nuevo);
        if (!$nuevoTecnico->tieneRol('Técnico')) {
            return response()->json(['mensaje' => 'El nuevo usuario propuesto no posee el rol de Técnico.'], 422);
        }

        $asignacion = Asignacion::where('id_solicitud', $solicitud->id_solicitud)->first();
        $tecnicoAnteriorId = $asignacion ? $asignacion->id_usuario_tecnico : null;

        if ($asignacion) {
            $asignacion->update([
                'id_usuario_tecnico' => $request->id_usuario_tecnico_nuevo,
                'id_usuario_coordinador' => $request->user()->id_usuario,
                'fecha_asignacion' => now(),
            ]);
        } else {
            Asignacion::create([
                'id_solicitud' => $solicitud->id_solicitud,
                'id_usuario_tecnico' => $request->id_usuario_tecnico_nuevo,
                'id_usuario_coordinador' => $request->user()->id_usuario,
                'fecha_asignacion' => now(),
            ]);
        }

        $tecnicoAnteriorNombre = 'Ninguno';
        if ($tecnicoAnteriorId) {
            $tecnicoAnterior = \App\Models\User::find($tecnicoAnteriorId);
            $tecnicoAnteriorNombre = $tecnicoAnterior ? $tecnicoAnterior->nombre . ' ' . $tecnicoAnterior->apellido : 'Técnico Anterior';
        }
        $nuevoTecnicoNombre = $nuevoTecnico->nombre . ' ' . $nuevoTecnico->apellido;

        $estadoAnterior = $solicitud->estado_solicitud;
        if ($estadoAnterior === 'En Proceso') {
            $solicitud->update([
                'estado_solicitud' => 'Asignada',
            ]);
            $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'estado_solicitud', 'En Proceso', 'Asignada');
        }

        $this->registrarHistorial(
            $solicitud,
            $request->user()->id_usuario,
            'tecnico_asignado',
            $tecnicoAnteriorNombre,
            $nuevoTecnicoNombre
        );

        $reasignacion->update([
            'estado' => 'Aprobada',
            'id_usuario_coordinador' => $request->user()->id_usuario,
            'comentarios_coordinador' => $request->comentarios_coordinador,
        ]);

        return response()->json([
            'mensaje' => 'La solicitud de reasignación ha sido aprobada y el ticket reasignado con éxito.',
            'reasignacion' => $reasignacion->load('solicitud'),
        ]);
    }

    public function reabrir(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $request->validate([
            'id_usuario_tecnico' => 'required|exists:users,id_usuario',
            'motivo'             => 'required|string|min:5',
        ]);

        $tecnico = User::with('rol')->findOrFail($request->id_usuario_tecnico);
        if (!$tecnico->tieneRol('Técnico')) {
            return response()->json(['mensaje' => 'El usuario seleccionado no posee el rol de Técnico.'], 422);
        }

        $estadoAnterior = $solicitud->estado_solicitud;

        // Eliminar asignaciones previas y crear una nueva
        Asignacion::where('id_solicitud', $solicitud->id_solicitud)->delete();

        $asignacion = Asignacion::create([
            'id_solicitud'           => $solicitud->id_solicitud,
            'id_usuario_tecnico'     => $request->id_usuario_tecnico,
            'id_usuario_coordinador' => $request->user()->id_usuario,
            'fecha_asignacion'       => now(),
        ]);

        // Cambiar el estado de la solicitud a 'Asignada'
        $solicitud->update(['estado_solicitud' => 'Asignada']);

        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'estado_solicitud', $estadoAnterior, 'Asignada');
        $this->registrarHistorial($solicitud, $request->user()->id_usuario, 'motivo_reapertura', null, $request->motivo);

        // Crear registro en la bitacora de la nueva asignacion indicando la reapertura
        \App\Models\Bitacora::create([
            'id_asignacion' => $asignacion->id_asignacion,
            'descripcion_trabajo' => '[TICKET REABIERTO] Motivo: ' . $request->motivo,
            'fecha_inicio' => now(),
        ]);

        return response()->json([
            'mensaje' => 'El ticket ha sido reabierto y reasignado exitosamente.',
            'solicitud' => $solicitud->load(['unidad.sede', 'asignacion.tecnico', 'asignacion.bitacoras', 'evidencias']),
        ]);
    }

    public function logsAuditoria(Request $request)
    {
        $logs = HistorialCambio::with(['solicitud', 'usuario'])
            ->latest()
            ->paginate($request->integer('per_page', 25));

        return response()->json($logs);
    }

    private function registrarHistorial(Solicitud $solicitud, string $idUsuario, string $campo, ?string $anterior, ?string $nuevo): void
    {
        HistorialCambio::create([
            'id_solicitud'     => $solicitud->id_solicitud,
            'id_usuario'       => $idUsuario,
            'campo_modificado' => $campo,
            'valor_anterior'   => $anterior,
            'valor_nuevo'      => $nuevo,
        ]);
    }
}
