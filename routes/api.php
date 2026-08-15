<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsuariosController;
use App\Http\Controllers\Api\SolicitudesController;
use App\Http\Controllers\Api\CoordinadorController;
use App\Http\Controllers\Api\TecnicoController;
use App\Http\Controllers\Api\EncuestasController;
use App\Http\Controllers\Api\ReportesController;
use App\Http\Controllers\Api\CatalogosController;

// Rutas publicas

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'enviarEnlaceRecuperacion']);
Route::post('/reset-password', [AuthController::class, 'restablecerPassword']);

// Catalogos
Route::get('/catalogos/sedes', [CatalogosController::class, 'sedes']);
Route::get('/catalogos/sedes/{id}/unidades', [CatalogosController::class, 'unidadesPorSede']);
Route::get('/catalogos/roles', [CatalogosController::class, 'roles']);
Route::post('/pdf/download', [ReportesController::class, 'descargarPDFProxy']);

// Rutas protegidas

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin
    
    Route::middleware('rol:Administrador')->prefix('admin')->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index']);
        Route::post('/usuarios', [UsuariosController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuariosController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuariosController::class, 'update']);
        Route::patch('/usuarios/{id}/deshabilitar', [UsuariosController::class, 'deshabilitar']);
        Route::patch('/usuarios/{id}/habilitar', [UsuariosController::class, 'habilitar']);

        // Catalogos admin
        Route::post('/catalogos/sedes', [CatalogosController::class, 'crearSede']);
        Route::post('/catalogos/unidades', [CatalogosController::class, 'crearUnidad']);
    });

    // Solicitante
    
    Route::middleware('rol:Solicitante,Administrador')->prefix('solicitante')->group(function () {
        Route::get('/solicitudes', [SolicitudesController::class, 'index']);
        Route::post('/solicitudes', [SolicitudesController::class, 'store']);
        Route::get('/solicitudes/{id}', [SolicitudesController::class, 'show'])->middleware('integridad');
        Route::put('/solicitudes/{id}', [SolicitudesController::class, 'update'])->middleware('integridad');
        Route::patch('/solicitudes/{id}/cancelar', [SolicitudesController::class, 'cancelar'])->middleware('integridad');
        Route::post('/solicitudes/{id}/evidencias', [SolicitudesController::class, 'adjuntarEvidencia']);

        // Encuestas
        Route::post('/solicitudes/{id}/encuesta', [EncuestasController::class, 'store']);
    });

    // Coordinador
    
    Route::middleware('rol:Coordinador,Administrador')->prefix('coordinador')->group(function () {
        Route::get('/solicitudes/pendientes', [CoordinadorController::class, 'solicitudesPendientes']);
        Route::get('/solicitudes/historial', [CoordinadorController::class, 'historialCompleto']);
        Route::patch('/solicitudes/{id}/clasificar', [CoordinadorController::class, 'clasificar'])->middleware('integridad');
        Route::patch('/solicitudes/{id}/devolver', [CoordinadorController::class, 'devolver'])->middleware('integridad');
        Route::post('/solicitudes/{id}/asignar', [CoordinadorController::class, 'asignar'])->middleware('integridad');
        Route::patch('/solicitudes/{id}/validar-cierre', [CoordinadorController::class, 'validarCierre'])->middleware('integridad');
        
        // Reasignaciones
        Route::get('/reasignaciones/pendientes', [CoordinadorController::class, 'listarReasignacionesPendientes']);
        Route::get('/tecnicos/carga-trabajo', [CoordinadorController::class, 'obtenerCargaTrabajoTecnicos']);
        Route::patch('/reasignaciones/{id}/resolver', [CoordinadorController::class, 'resolverReasignacion']);
    });

    // Tecnico
    
    Route::middleware('rol:Técnico,Administrador')->prefix('tecnico')->group(function () {
        Route::get('/asignaciones', [TecnicoController::class, 'misAsignaciones']);
        Route::patch('/asignaciones/{id}/iniciar', [TecnicoController::class, 'iniciarAtencion'])->middleware('integridad');
        Route::post('/asignaciones/{id}/bitacoras', [TecnicoController::class, 'registrarBitacora'])->middleware('integridad');
        Route::get('/asignaciones/{id}/bitacoras', [TecnicoController::class, 'verBitacoras'])->middleware('integridad');
        Route::patch('/asignaciones/{id}/solicitar-cierre', [TecnicoController::class, 'solicitarCierre'])->middleware('integridad');
        Route::post('/asignaciones/{id}/evidencias', [TecnicoController::class, 'adjuntarEvidencia'])->middleware('integridad');
        
        // Reasignaciones tecnico
        Route::post('/asignaciones/{id}/solicitar-reasignacion', [TecnicoController::class, 'solicitarReasignacion'])->middleware('integridad');
    });

    // Reportes
    
    Route::middleware('rol:Coordinador,Administrador')->prefix('reportes')->group(function () {
        Route::get('/dashboard', [ReportesController::class, 'dashboard']);
        Route::get('/por-area', [ReportesController::class, 'porArea']);
        Route::get('/tecnicos', [ReportesController::class, 'rendimientoTecnicos']);
    });

    // Detalle de ticket
    Route::middleware('rol:Coordinador,Técnico,Administrador')
        ->get('/solicitudes/{id}', [SolicitudesController::class, 'show'])->middleware('integridad');
});
