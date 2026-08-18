<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Asignacion;
use App\Models\Encuesta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{

    public function dashboard()
    {
        $totalesPorEstado = Solicitud::select('estado_solicitud', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud')
            ->pluck('total', 'estado_solicitud');

        $totalesPorPrioridad = Solicitud::whereNotNull('prioridad')
            ->select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->pluck('total', 'prioridad');

        $tecnicosMasActivos = Asignacion::with('tecnico')
            ->whereHas('solicitud', fn($q) => $q->whereIn('estado_solicitud', ['Validada', 'Cerrada', 'Resuelta']))
            ->where('created_at', '>=', now()->subDays(30))
            ->select('id_usuario_tecnico', DB::raw('count(*) as total'))
            ->groupBy('id_usuario_tecnico')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'tecnico' => $a->tecnico ? ($a->tecnico->nombre . ' ' . $a->tecnico->apellido) : 'Técnico',
                'total'   => $a->total,
            ]);

        $promedioEncuestas = Encuesta::select(
            DB::raw('round(avg(calificacion_rapidez), 1) as rapidez'),
            DB::raw('round(avg(calificacion_calidad), 1) as calidad'),
            DB::raw('round(avg(calificacion_amabilidad), 1) as amabilidad'),
            DB::raw('count(*) as total_respondidas')
        )->first();

        $totalSolicitudes = Solicitud::count();
        $totalCerradas = Solicitud::whereIn('estado_solicitud', ['Validada', 'Cerrada', 'Resuelta'])->count();
        $tasaResolucion = $totalSolicitudes > 0 ? round(($totalCerradas / $totalSolicitudes) * 100, 1) : 0;
        $tasaEncuestas = $totalCerradas > 0 ? round((($promedioEncuestas->total_respondidas ?? 0) / $totalCerradas) * 100, 1) : 0;

        return response()->json([
            'por_estado'                  => $totalesPorEstado,
            'por_prioridad'               => $totalesPorPrioridad,
            'tecnicos_activos'            => $tecnicosMasActivos,
            'promedio_encuestas'          => $promedioEncuestas,
            'total_solicitudes'           => $totalSolicitudes,
            'total_completadas'           => $totalCerradas,
            'tasa_resolucion_global'      => $tasaResolucion . '%',
            'tasa_respuesta_encuestas'    => $tasaEncuestas . '%',
        ]);
    }

    public function porArea(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        $datos = Solicitud::with('unidad.sede')
            ->when($request->desde, fn($q) => $q->where('fecha_apertura', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('fecha_apertura', '<=', $request->hasta . ' 23:59:59'))
            ->select('id_unidad', 'estado_solicitud', DB::raw('count(*) as total'))
            ->groupBy('id_unidad', 'estado_solicitud')
            ->get();

        return response()->json($datos);
    }

    public function rendimientoTecnicos(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        $datos = DB::table('users as u')
            ->join('roles as r', 'r.id_rol', '=', 'u.id_rol')
            ->leftJoin('asignaciones as a', 'a.id_usuario_tecnico', '=', 'u.id_usuario')
            ->leftJoin('solicitudes as s', 's.id_solicitud', '=', 'a.id_solicitud')
            ->where('r.nombre_rol', '=', 'Técnico')
            ->when($request->desde, fn($q) => $q->where('a.created_at', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('a.created_at', '<=', $request->hasta . ' 23:59:59'))
            ->select(
                'u.id_usuario',
                DB::raw("concat(u.nombre, ' ', u.apellido) as tecnico"),
                DB::raw("count(a.id_asignacion) as total_asignadas"),
                DB::raw("sum(case when s.estado_solicitud in ('Resuelta', 'Cerrada', 'Validada') then 1 else 0 end) as total_resueltas"),
                DB::raw("round(coalesce(100 * sum(case when s.estado_solicitud in ('Resuelta', 'Cerrada', 'Validada') then 1 else 0 end) / nullif(count(a.id_asignacion), 0), 0), 1) as tasa_resolucion")
            )
            ->groupBy('u.id_usuario', 'u.nombre', 'u.apellido')
            ->orderBy('tecnico')
            ->get()
            ->map(fn($t) => [
                'id_usuario'      => $t->id_usuario,
                'tecnico'         => $t->tecnico,
                'total_asignadas' => (int) $t->total_asignadas,
                'total_resueltas' => (int) $t->total_resueltas,
                'tasa_resolucion' => $t->tasa_resolucion . '%',
            ]);

        return response()->json($datos);
    }

    public function tiemposAtencion(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        // 1. Tiempo promedio de asignación (horas entre apertura y asignación)
        $tiempoAsignacion = DB::table('solicitudes as s')
            ->join('asignaciones as a', 'a.id_solicitud', '=', 's.id_solicitud')
            ->when($request->desde, fn($q) => $q->where('s.fecha_apertura', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('s.fecha_apertura', '<=', $request->hasta . ' 23:59:59'))
            ->select(DB::raw('round(avg(timestampdiff(minute, s.fecha_apertura, a.fecha_asignacion)) / 60, 2) as horas_promedio_asignacion'))
            ->value('horas_promedio_asignacion') ?? 0;

        // 2. Duración promedio de trabajo de técnicos en bitácoras (horas entre fecha_inicio y fecha_fin)
        $tiempoTrabajo = DB::table('bitacoras as b')
            ->whereNotNull('b.fecha_inicio')
            ->whereNotNull('b.fecha_fin')
            ->when($request->desde, fn($q) => $q->where('b.fecha_inicio', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('b.fecha_inicio', '<=', $request->hasta . ' 23:59:59'))
            ->select(DB::raw('round(avg(timestampdiff(minute, b.fecha_inicio, b.fecha_fin)) / 60, 2) as horas_promedio_trabajo'))
            ->value('horas_promedio_trabajo') ?? 0;

        // 3. Tiempo promedio de resolución total (horas desde apertura hasta cierre)
        $tiempoResolucion = DB::table('solicitudes as s')
            ->whereIn('s.estado_solicitud', ['Validada', 'Cerrada', 'Resuelta'])
            ->when($request->desde, fn($q) => $q->where('s.fecha_apertura', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('s.fecha_apertura', '<=', $request->hasta . ' 23:59:59'))
            ->select(DB::raw('round(avg(timestampdiff(minute, s.fecha_apertura, s.updated_at)) / 60, 2) as horas_promedio_resolucion'))
            ->value('horas_promedio_resolucion') ?? 0;

        return response()->json([
            'horas_promedio_asignacion' => (float) $tiempoAsignacion,
            'horas_promedio_trabajo'    => (float) $tiempoTrabajo,
            'horas_promedio_resolucion' => (float) $tiempoResolucion,
        ]);
    }

    public function tendencias(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        $tendencias = Solicitud::when($request->desde, fn($q) => $q->where('fecha_apertura', '>=', $request->desde))
            ->when($request->hasta, fn($q) => $q->where('fecha_apertura', '<=', $request->hasta . ' 23:59:59'))
            ->select(
                DB::raw("date_format(fecha_apertura, '%Y-%m') as periodo"),
                DB::raw("count(*) as total"),
                DB::raw("sum(case when estado_solicitud in ('Validada', 'Cerrada', 'Resuelta') then 1 else 0 end) as resueltas"),
                DB::raw("sum(case when estado_solicitud in ('Abierta', 'Devuelta') then 1 else 0 end) as pendientes")
            )
            ->groupBy('periodo')
            ->orderBy('periodo', 'asc')
            ->get();

        return response()->json($tendencias);
    }

    public function descargarPDFProxy(Request $request)
    {
        $request->validate([
            'pdf' => 'required|string',
            'filename' => 'required|string',
        ]);

        $pdfBase64 = $request->input('pdf');
        if (str_starts_with($pdfBase64, 'data:')) {
            $parts = explode(',', $pdfBase64);
            $pdfBase64 = end($parts);
        }

        $pdfBinary = base64_decode($pdfBase64);
        $filename = $request->input('filename', 'reporte.pdf');

        return response($pdfBinary, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
