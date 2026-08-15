/**
 * resources/js/services/authService.js
 * Lógica de negocio de autenticación. Usa el cliente Axios
 * centralizado de api.js.
 */

import api from './api';

function normalizarUsuario(user) {
    if (user && user.rol) {
        // Normalizar rol a minúsculas y sin acentos (ej. 'Técnico' -> 'tecnico')
        user.rol = user.rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        if (user.role) {
            user.role = user.rol;
        }
        if (user.rol === 'administrador') {
            user.rol = 'admin';
        }
    }
    return user;
}

export default {
    api, // Compatibilidad con vistas que usan authService.api
    async login(credentials) {
        // Adaptar campos: el frontend envia 'email' pero el backend espera 'correo'
        const payload = {
            correo: credentials.email || credentials.correo,
            password: credentials.password
        };
        const response = await api.post('/login', payload);
        
        // El backend devuelve { token, usuario: { id, nombre, correo, rol } }
        const token = response.data.token;
        const usuario = normalizarUsuario(response.data.usuario);

        localStorage.setItem('auth_token', token);
        localStorage.setItem('auth_user', JSON.stringify(usuario));
        return usuario;
    },

    async logout() {
        try {
            await api.post('/logout');
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            localStorage.removeItem('auth_last_activity');
        }
    },

    async fetchCurrentUser() {
        const response = await api.get('/me');
        const usuario = normalizarUsuario(response.data);
        localStorage.setItem('auth_user', JSON.stringify(usuario));
        return usuario;
    },

    getUser() {
        const userStr = localStorage.getItem('auth_user');
        if (!userStr) return null;
        try {
            return normalizarUsuario(JSON.parse(userStr));
        } catch (e) {
            return null;
        }
    },

    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    }
};
