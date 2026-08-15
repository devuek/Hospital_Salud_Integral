/**
 * resources/js/services/tecnicosService.js
 * Servicio para gestionar la información de técnicos de mantenimiento.
 */

import api from './api';

export default {
    async listar() {
        // En el backend local, los técnicos se listan con su carga de trabajo bajo el prefix coordinador
        const response = await api.get('/coordinador/tecnicos/carga-trabajo');
        
        // Mapear { id_usuario, nombre_completo, tickets_activos } a { id, name }
        return (response.data || []).map(t => ({
            id: t.id_usuario,
            name: `${t.nombre_completo} (${t.tickets_activos} tickets activos)`
        }));
    }
};