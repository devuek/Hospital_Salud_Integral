/**
 * resources/js/services/api.js
 * Cliente Axios centralizado para toda la app.
 * Configura baseURL y agrega el Bearer token automáticamente.
 * Si el backend responde 401, limpia la sesión local y dispara
 * el evento 'auth:logout' para que los stores se sincronicen.
 */

import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            localStorage.removeItem('auth_last_activity');
            window.dispatchEvent(new Event('auth:logout'));
            // Redirigir al login si no estamos ya en la página de login
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login?expired=1';
            }
        }
        return Promise.reject(error);
    }
);

export default api;