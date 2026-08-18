<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Models\Unidad;
use App\Models\Rol;
use Illuminate\Http\Request;

class CatalogosController extends Controller
{

    public function sedes()
    {
        return response()->json(Sede::with('unidades')->orderBy('nombre_sede')->get());
    }

    public function unidadesPorSede(string $idSede)
    {
        $sede = Sede::with('unidades')->findOrFail($idSede);
        return response()->json($sede->unidades()->orderBy('nombre_unidad')->get());
    }

    public function roles()
    {
        return response()->json(Rol::orderBy('nombre_rol')->get());
    }

    public function crearSede(Request $request)
    {
        $request->validate([
            'nombre_sede' => 'required|string|max:100',
            'direccion'   => 'required|string',
        ]);

        $sede = Sede::create($request->only(['nombre_sede', 'direccion']));

        return response()->json([
            'mensaje' => 'Sede creada con éxito.',
            'sede'    => $sede,
        ], 201);
    }

    public function crearUnidad(Request $request)
    {
        $request->validate([
            'id_sede'      => 'required|exists:sedes,id_sede',
            'nombre_unidad' => 'required|string|max:100',
        ]);

        $unidad = Unidad::create($request->only(['id_sede', 'nombre_unidad']));

        return response()->json([
            'mensaje' => 'Unidad creada con éxito.',
            'unidad'  => $unidad->load('sede'),
        ], 201);
    }

    public function actualizarSede(Request $request, string $id)
    {
        $sede = Sede::findOrFail($id);
        $request->validate([
            'nombre_sede' => 'required|string|max:100',
            'direccion'   => 'required|string',
        ]);

        $sede->update($request->only(['nombre_sede', 'direccion']));

        return response()->json([
            'mensaje' => 'Sede actualizada con éxito.',
            'sede'    => $sede,
        ]);
    }

    public function eliminarSede(string $id)
    {
        $sede = Sede::withCount('unidades')->findOrFail($id);
        if ($sede->unidades_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la sede porque tiene unidades médicas asociadas. Elimine o mueva las unidades primero.',
            ], 422);
        }

        $sede->delete();

        return response()->json([
            'mensaje' => 'Sede eliminada con éxito.',
        ]);
    }

    public function actualizarUnidad(Request $request, string $id)
    {
        $unidad = Unidad::findOrFail($id);
        $request->validate([
            'nombre_unidad' => 'required|string|max:100',
            'id_sede'       => 'sometimes|exists:sedes,id_sede',
        ]);

        $unidad->update($request->only(['nombre_unidad', 'id_sede']));

        return response()->json([
            'mensaje' => 'Unidad actualizada con éxito.',
            'unidad'  => $unidad->load('sede'),
        ]);
    }

    public function eliminarUnidad(string $id)
    {
        $unidad = Unidad::findOrFail($id);
        $unidad->delete();

        return response()->json([
            'mensaje' => 'Unidad eliminada con éxito.',
        ]);
    }
}
