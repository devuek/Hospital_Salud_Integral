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
            ->whereHas('solicitud', fn($q) => $q->whereIn('estado_solicitud', ['Validada', 'Cerrada']))
            ->where('created_at', '>=', now()->subDays(30))
            ->select('id_usuario_tecnico', DB::raw('count(*) as total'))
            ->groupBy('id_usuario_tecnico')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'tecnico' => $a->tecnico->nombre . ' ' . $a->tecnico->apellido,
                'total'   => $a->total,
            ]);

        $promedioEncuestas = Encuesta::select(
            DB::raw('round(avg(calificacion_rapidez), 1) as rapidez'),
            DB::raw('round(avg(calificacion_calidad), 1) as calidad'),
            DB::raw('round(avg(calificacion_amabilidad), 1) as amabilidad'),
        )->first();

        return response()->json([
            'por_estado'         => $totalesPorEstado,
            'por_prioridad'      => $totalesPorPrioridad,
            'tecnicos_activos'   => $tecnicosMasActivos,
            'promedio_encuestas' => $promedioEncuestas,
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
        $datos = DB::table('users as u')
            ->join('roles as r', 'r.id_rol', '=', 'u.id_rol')
            ->leftJoin('asignaciones as a', 'a.id_usuario_tecnico', '=', 'u.id_usuario')
            ->leftJoin('solicitudes as s', 's.id_solicitud', '=', 'a.id_solicitud')
            ->where('r.nombre_rol', '=', 'Técnico')
            ->select(
                'u.id_usuario',
                DB::raw("concat(u.nombre, ' ', u.apellido) as tecnico"),
                DB::raw("count(a.id_asignacion) as total_asignadas"),
                DB::raw("sum(case when s.estado_solicitud in ('Resuelta', 'Cerrada') then 1 else 0 end) as total_resueltas"),
                DB::raw("round(coalesce(100 * sum(case when s.estado_solicitud in ('Resuelta', 'Cerrada') then 1 else 0 end) / nullif(count(a.id_asignacion), 0), 0), 1) as tasa_resolucion")
            )
            ->groupBy('u.id_usuario', 'u.nombre', 'u.apellido')
            ->orderBy('tecnico')
            ->get()
            ->map(fn($t) => [
                'tecnico'         => $t->tecnico,
                'total_asignadas' => (int) $t->total_asignadas,
                'total_resueltas' => (int) $t->total_resueltas,
                'tasa_resolucion' => $t->tasa_resolucion . '%',
            ]);

        return response()->json($datos);
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
