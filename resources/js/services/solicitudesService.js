/**
 * resources/js/services/solicitudesService.js
 * Servicio adaptado para mediar entre el frontend Vue.js de API simplificada
 * y el backend local robusto con acceso basado en roles y seguridad avanzada.
 */

import api from './api';
import authService from './authService';

// Traductores de campos y estados de Solicitud
function mapearAlFrontend(s) {
    if (!s) return null;

    // Normalizar estados
    // Backend local: 'Abierta', 'Asignada', 'En Proceso', 'Cerrada', 'Validada', 'Devuelta', 'Cancelada'
    // Frontend Vue: 'Pendiente', 'En Proceso', 'Completada', 'Cancelada'
    let estadoFE = 'Pendiente';
    const est = s.estado_solicitud || (s.solicitud ? s.solicitud.estado_solicitud : 'Abierta');
    if (est === 'Abierta' || est === 'Devuelta') {
        estadoFE = 'Pendiente';
    } else if (est === 'Asignada' || est === 'En Proceso') {
        estadoFE = 'En Proceso';
    } else if (est === 'Cerrada' || est === 'Validada') {
        estadoFE = 'Completada';
    } else if (est === 'Cancelada') {
        estadoFE = 'Cancelada';
    }

    // Si es un objeto de Asignacion del técnico, los datos de la solicitud están anidados
    const solicitud = s.solicitud || s;
    const esAsignacion = !!s.id_asignacion;
    const idAsignacion = esAsignacion ? s.id_asignacion : (s.asignacion ? s.asignacion.id_asignacion : null);

    return {
        id: solicitud.id_solicitud || s.id_solicitud,
        id_asignacion: idAsignacion,
        titulo: solicitud.titulo || s.titulo,
        subtitulo: solicitud.tipo_servicio || s.tipo_servicio || 'Soporte Técnico',
        descripcion: solicitud.descripcion || s.descripcion,
        ubicacion: solicitud.unidad && solicitud.unidad.sede 
            ? `${solicitud.unidad.sede.nombre_sede} - ${solicitud.unidad.nombre_unidad}` 
            : (s.unidad && s.unidad.sede ? `${s.unidad.sede.nombre_sede} - ${s.unidad.nombre_unidad}` : 'No especificada'),
        sede: solicitud.unidad?.sede?.nombre_sede || s.unidad?.sede?.nombre_sede || 'No especificada',
        departamento: solicitud.unidad ? solicitud.unidad.nombre_unidad : (s.unidad ? s.unidad.nombre_unidad : ''),
        fecha: solicitud.fecha_apertura ? solicitud.fecha_apertura.substring(0, 10) : (s.fecha_apertura ? s.fecha_apertura.substring(0, 10) : ''),
        prioridad: solicitud.prioridad || s.prioridad || 'Media',
        estado: estadoFE,
        tecnico_id: esAsignacion ? s.id_usuario_tecnico : (s.asignacion ? s.asignacion.id_usuario_tecnico : null),
        tecnico_nombre: esAsignacion && s.tecnico ? `${s.tecnico.nombre} ${s.tecnico.apellido}` : (s.asignacion && s.asignacion.tecnico ? `${s.asignacion.tecnico.nombre} ${s.asignacion.tecnico.apellido}` : null),
        codigo_verificacion: solicitud.codigo_verificacion || s.codigo_verificacion,
        original: s
    };
}

// Obtener catálogo de unidades para autoselección de ID al crear solicitudes
// Usa la sede del usuario autenticado para obtener el id_unidad correcto
let cacheUnidades = null;
async function obtenerIdUnidadPorDefecto() {
    if (cacheUnidades) return cacheUnidades;
    try {
        const resSedes = await api.get('/catalogos/sedes');
        if (!resSedes.data || resSedes.data.length === 0) throw new Error('Sin sedes');

        // Intentar usar la sede del usuario autenticado
        const user = authService.getUser();
        const userSedeId = user?.id_sede || user?.sede?.id_sede || null;

        let sedeTarget = null;
        if (userSedeId) {
            sedeTarget = resSedes.data.find(s => s.id_sede === userSedeId);
        }
        // Fallback: primera sede del catálogo
        if (!sedeTarget) {
            sedeTarget = resSedes.data[0];
        }

        // Las unidades ya vienen embebidas en la respuesta de /catalogos/sedes
        const unidades = sedeTarget.unidades || [];
        if (unidades.length > 0) {
            cacheUnidades = unidades[0].id_unidad;
            return cacheUnidades;
        }

        // Fallback: llamar al endpoint de unidades por sede
        const resUnidades = await api.get(`/catalogos/sedes/${sedeTarget.id_sede}/unidades`);
        if (resUnidades.data && resUnidades.data.length > 0) {
            cacheUnidades = resUnidades.data[0].id_unidad;
            return cacheUnidades;
        }
    } catch (e) {
        console.error('Error cargando catálogos de sedes/unidades:', e);
    }
    return null;
}

export default {
    async resumen() {
        const user = authService.getUser();
        const rol = user?.rol;

        // Si es coordinador o admin, usamos el dashboard de reportes del backend local
        if (rol === 'coordinador' || rol === 'admin') {
            try {
                const response = await api.get('/reportes/dashboard');
                const porEstado = response.data.por_estado || {};
                
                const pendientes = (porEstado['Abierta'] || 0) + (porEstado['Devuelta'] || 0);
                const enProceso = (porEstado['Asignada'] || 0) + (porEstado['En Proceso'] || 0);
                const completadas = (porEstado['Cerrada'] || 0) + (porEstado['Validada'] || 0);
                const total = pendientes + enProceso + completadas;

                return {
                    pendientes,
                    en_proceso: enProceso,
                    completadas,
                    total
                };
            } catch (e) {
                console.error(e);
            }
        }

        // Si falla o es otro rol (solicitante o técnico), calculamos el resumen localmente del listado
        try {
            const list = await this.listar();
            const pendientes = list.filter(s => s.estado === 'Pendiente').length;
            const enProceso = list.filter(s => s.estado === 'En Proceso').length;
            const completadas = list.filter(s => s.estado === 'Completada').length;
            return {
                pendientes,
                en_proceso: enProceso,
                completadas,
                total: list.length
            };
        } catch (e) {
            console.error(e);
            return { pendientes: 0, en_proceso: 0, completadas: 0, total: 0 };
        }
    },

    async listar(params = {}) {
        const user = authService.getUser();
        const rol = user?.rol;
        let url = '/solicitante/solicitudes';

        if (rol === 'tecnico') {
            url = '/tecnico/asignaciones';
        } else if (rol === 'coordinador' || rol === 'admin') {
            url = '/coordinador/solicitudes/historial';
        }

        const response = await api.get(url, { params });
        
        // Si la respuesta es paginada (tiene .data como array y metadatos de paginacion)
        if (response.data && Array.isArray(response.data.data)) {
            return {
                ...response.data,
                data: response.data.data.map(mapearAlFrontend).filter(Boolean)
            };
        }

        const data = response.data || [];
        return data.map(mapearAlFrontend).filter(Boolean);
    },

    async obtener(id) {
        const user = authService.getUser();
        const rol = user?.rol;
        let url = `/solicitante/solicitudes/${id}`;

        if (rol === 'tecnico' || rol === 'coordinador' || rol === 'admin') {
            url = `/solicitudes/${id}`;
        }

        const response = await api.get(url);
        return mapearAlFrontend(response.data.solicitud || response.data.asignacion || response.data);
    },

    async crear(datos) {
        const idUnidad = await obtenerIdUnidadPorDefecto();
        
        // El backend sólo acepta 'Servicios Generales' o 'Soporte Técnico'
        const serviciosGenerales = ['Plomería', 'Eléctrico', 'Aire Acondicionado', 'Limpieza'];
        const esServiciosGenerales = serviciosGenerales.includes(datos.titulo);
        const tipoServicioBackend = esServiciosGenerales ? 'Servicios Generales' : 'Soporte Técnico';

        // Concatenar la ubicación específica ingresada para que no se pierda
        const descripcionCompleta = datos.ubicacion 
            ? `[Ubicación: ${datos.ubicacion}] ${datos.subtitulo || datos.descripcion || ''}`
            : (datos.subtitulo || datos.descripcion || '');

        const payload = {
            titulo: datos.titulo,
            descripcion: descripcionCompleta,
            tipo_servicio: tipoServicioBackend,
            prioridad: datos.prioridad || 'Media',
            id_unidad: idUnidad
        };

        const response = await api.post('/solicitante/solicitudes', payload);

        // Subir evidencia si viene un archivo
        if (datos.archivo && response.data.solicitud) {
            try {
                const formData = new FormData();
                formData.append('archivo', datos.archivo);
                await api.post(`/solicitante/solicitudes/${response.data.solicitud.id_solicitud}/evidencias`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            } catch (e) {
                console.error("Error al subir archivo de evidencia:", e);
            }
        }

        return mapearAlFrontend(response.data.solicitud);
    },

    async actualizar(id, datos) {
        const user = authService.getUser();
        const rol = user?.rol;

        // Si es técnico y está actualizando el estado de la tarea
        if (rol === 'tecnico' && datos.estado) {
            let endpoint = `/tecnico/asignaciones/${id}/iniciar`;
            if (datos.estado === 'Completada') {
                endpoint = `/tecnico/asignaciones/${id}/finalizar`;
            }
            const response = await api.patch(endpoint, {
                codigo_verificacion: datos.codigo_verificacion
            });
            return mapearAlFrontend(response.data.asignacion || response.data.solicitud);
        }

        // Si es coordinador o administrador (flujos de asignación o reasignación)
        if (rol === 'coordinador' || rol === 'admin') {
            let resSolicitud = null;
            if (datos.prioridad) {
                const response = await api.patch(`/coordinador/solicitudes/${id}/clasificar`, {
                    prioridad: datos.prioridad
                });
                resSolicitud = response.data.solicitud;
            }
            if (datos.tecnico_id) {
                // Asignar o reasignar técnico (debe ser un ID válido)
                const response = await api.post(`/coordinador/solicitudes/${id}/asignar`, {
                    id_usuario_tecnico: datos.tecnico_id
                });
                resSolicitud = response.data.solicitud;
            }
            if (resSolicitud) {
                return mapearAlFrontend(resSolicitud);
            }
        }

        // Edición básica
        const response = await api.put(`/solicitante/solicitudes/${id}`, datos);
        return mapearAlFrontend(response.data.solicitud);
    },

    async eliminar(id) {
        await api.delete(`/solicitante/solicitudes/${id}`);
    }
};
