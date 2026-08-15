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

    async subir(solicitudId, archivo) {
        const user = authService.getUser();
        const rol = user?.rol;
        let url = `/solicitante/solicitudes/${solicitudId}/evidencias`;

        if (rol === 'tecnico') {
            url = `/tecnico/asignaciones/${solicitudId}/evidencias`;
        }

        const formData = new FormData();
        formData.append('archivo', archivo); // El backend valida el parámetro con nombre 'archivo'

        const response = await api.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        const e = response.data.evidencia;
        return {
            id: e.id_evidencia,
            url: e.url_recurso,
            name: e.url_recurso.split('/').pop() || 'archivo_evidencia',
            type: e.tipo_archivo
        };
    }
};