/**
 * resources/js/services/evidenciasService.js
 * Servicio para gestionar las evidencias fotográficas de los tickets de mantenimiento.
 */

import api from './api';
import authService from './authService';
import solicitudesService from './solicitudesService';

export default {
    async listar(solicitudId) {
        // En lugar de hacer una petición extra a un endpoint GET inexistente en el backend local,
        // obtenemos el detalle de la solicitud (que ya carga la relación 'evidencias')
        // y retornamos sus evidencias formateadas
        try {
            const solicitud = await solicitudesService.obtener(solicitudId);
            const evidencias = solicitud?.original?.evidencias || [];
            
            return evidencias.map(e => ({
                id: e.id_evidencia,
                url: e.url_recurso,
                name: e.url_recurso.split('/').pop() || 'archivo_evidencia',
                type: e.tipo_archivo
            }));
        } catch (e) {
            console.error('Error al listar evidencias:', e);
            return [];
        }
    },

    async subir(solicitudId, archivo, idAsignacion = null) {
        const user = authService.getUser();
        const rol = user?.rol;
        
        console.log('[evidenciasService.subir] Iniciando subida:', {
            solicitudId,
            idAsignacion,
            userRol: rol,
            userId: user?.id_usuario,
            archivoName: archivo?.name,
            archivoType: archivo?.type
        });

        let url = `/solicitante/solicitudes/${solicitudId}/evidencias`;

        if (idAsignacion || rol === 'tecnico') {
            const targetId = idAsignacion || solicitudId;
            url = `/tecnico/asignaciones/${targetId}/evidencias`;
        }

        console.log('[evidenciasService.subir] Endpoint seleccionado:', url);

        const formData = new FormData();
        formData.append('archivo', archivo); // El backend valida el parámetro con nombre 'archivo'

        try {
            const response = await api.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            console.log('[evidenciasService.subir] Respuesta exitosa:', response.data);
            const e = response.data.evidencia;
            return {
                id: e.id_evidencia,
                url: e.url_recurso,
                name: e.url_recurso.split('/').pop() || 'archivo_evidencia',
                type: e.tipo_archivo
            };
        } catch (error) {
            console.error('[evidenciasService.subir] Error en la petición:', {
                status: error.response?.status,
                data: error.response?.data,
                message: error.message
            });
            throw error;
        }
    }
};