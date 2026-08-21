<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 15);
        $paginator = User::with(['rol', 'sede'])
            ->orderBy('nombre')
            ->paginate($perPage);

        $paginator->through(fn($u) => $this->formatear($u));

        return response()->json($paginator);
    }

    public function store(Request $request)
    {
        $rol = Rol::findOrFail($request->id_rol);
        $esOperativo = in_array($rol->nombre_rol, ['Técnico', 'Coordinador', 'Solicitante']);

        $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'correo'        => 'required|email|unique:users,correo',
            'id_rol'        => 'required|exists:roles,id_rol',
            'id_sede'       => ($esOperativo ? 'required' : 'nullable') . '|exists:sedes,id_sede',
            'area_servicio' => $rol->nombre_rol === 'Técnico'
                ? 'required|in:Servicios Generales,Soporte Técnico'
                : 'nullable',
            'password'      => 'nullable|string|min:6',
        ]);

        $passwordGuardar = $request->filled('password') ? $request->password : Str::random(10);

        $usuario = User::create([
            'id_rol'        => $request->id_rol,
            'id_sede'       => $request->id_sede,
            'area_servicio' => $rol->nombre_rol === 'Técnico' ? $request->area_servicio : null,
            'nombre'        => $request->nombre,
            'apellido'      => $request->apellido,
            'correo'        => $request->correo,
            'password'      => Hash::make($passwordGuardar),
            'estado'        => 'Activo',
        ]);

        return response()->json([
            'mensaje'         => 'Usuario creado correctamente en el sistema.',
            'usuario'         => $this->formatear($usuario->load(['rol', 'sede'])),
            'password_temp'   => $passwordGuardar,
        ], 201);
    }

    public function show(string $id)
    {
        $usuario = User::with(['rol', 'sede'])->findOrFail($id);

        return response()->json($this->formatear($usuario));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $idRol = $request->id_rol ?? $usuario->id_rol;
        $rol = Rol::find($idRol);

        $request->validate([
            'nombre'        => 'sometimes|string|max:100',
            'apellido'      => 'sometimes|string|max:100',
            'correo'        => 'sometimes|email|unique:users,correo,' . $id . ',id_usuario',
            'id_rol'        => 'sometimes|exists:roles,id_rol',
            'id_sede'       => 'sometimes|nullable|exists:sedes,id_sede',
            'area_servicio' => 'sometimes|nullable|string',
            'password'      => 'nullable|string|min:6',
        ]);

        $datos = array_filter(
            $request->only(['nombre', 'apellido', 'correo', 'id_rol', 'id_sede', 'area_servicio']),
            fn($val) => !is_null($val)
        );

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return response()->json([
            'mensaje' => 'Usuario actualizado con éxito.',
            'usuario' => $this->formatear($usuario->load(['rol', 'sede'])),
        ]);
    }

    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->estado === 'Activo') {
            return response()->json([
                'message' => 'No se puede eliminar un usuario activo. Primero debe deshabilitarlo.',
            ], 422);
        }

        $usuario->delete();

        return response()->json([
            'mensaje' => 'Usuario eliminado de la base de datos correctamente.',
        ]);
    }

    public function deshabilitar(string $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'Inactivo']);

        return response()->json([
            'mensaje' => 'El usuario ha sido deshabilitado correctamente. Ya no podrá ingresar al sistema.',
        ]);
    }

    public function habilitar(string $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'Activo']);

        return response()->json([
            'mensaje' => 'El usuario ha sido reactivado de forma exitosa.',
        ]);
    }

    private function formatear(User $u): array
    {
        return [
            'id'             => $u->id_usuario,
            'nombre'         => $u->nombre . ' ' . $u->apellido,
            'nombre_solo'    => $u->nombre,
            'apellido'       => $u->apellido,
            'correo'         => $u->correo,
            'estado'         => $u->estado,
            'rol'            => $u->rol->nombre_rol ?? null,
            'id_rol'         => $u->id_rol,
            'id_sede'        => $u->id_sede,
            'sede'           => $u->sede->nombre_sede ?? 'Global',
            'area_servicio'  => $u->area_servicio,
        ];
    }
}
