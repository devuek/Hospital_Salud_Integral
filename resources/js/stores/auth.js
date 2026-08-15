/**
 * stores/auth.js
 * Store de Pinia que envuelve authService para exponer estado reactivo
 * de autenticación al resto de la app (componentes, guards de router, etc).
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import authService from '../services/authService';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(authService.getUser());
  const loading = ref(false);
  const error = ref(null);

  const isAuthenticated = computed(() => !!user.value && authService.isAuthenticated());

  // Tiempos límite en milisegundos
  const INACTIVIDAD_MAXIMA = 2 * 60 * 60 * 1000;   // 2 horas
  const EXPIRED_TIEMPO_MAXIMO = 5 * 60 * 60 * 1000; // 5 horas

  function actualizarUltimaActividad() {
    if (authService.isAuthenticated()) {
      localStorage.setItem('auth_last_activity', Date.now().toString());
    }
  }

  function forzarLogout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    localStorage.removeItem('auth_last_activity');
    user.value = null;
    window.location.href = '/login';
  }

  function verificarExpiracion() {
    if (!authService.isAuthenticated()) return;

    const lastActivity = parseInt(localStorage.getItem('auth_last_activity') || '0');
    if (!lastActivity) {
      actualizarUltimaActividad();
      return;
    }

    const tiempoTranscurrido = Date.now() - lastActivity;

    // Cierre de sesión por inactividad de 2 horas en tiempo real
    if (tiempoTranscurrido >= INACTIVIDAD_MAXIMA) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      localStorage.removeItem('auth_last_activity');
      user.value = null;
      window.location.href = '/login?expired=1';
    }
  }

  // Verificación al inicializar (si se cerró la ventana por más de 5 horas)
  const lastActivityAlIniciar = parseInt(localStorage.getItem('auth_last_activity') || '0');
  if (authService.isAuthenticated() && lastActivityAlIniciar) {
    const tiempoTranscurridoAlIniciar = Date.now() - lastActivityAlIniciar;
    if (tiempoTranscurridoAlIniciar >= EXPIRED_TIEMPO_MAXIMO) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      localStorage.removeItem('auth_last_activity');
      user.value = null;
    } else {
      actualizarUltimaActividad();
    }
  }

  // Registrar listeners de interacción del usuario
  if (typeof window !== 'undefined') {
    const eventos = ['mousedown', 'click', 'keydown', 'scroll', 'touchstart'];
    eventos.forEach(evento => {
      window.addEventListener(evento, actualizarUltimaActividad);
    });

    // Validar inactividad en segundo plano cada 30 segundos
    setInterval(verificarExpiracion, 30000);
  }

  async function login(credentials) {
    loading.value = true;
    error.value = null;
    try {
      user.value = await authService.login(credentials);
      actualizarUltimaActividad();
      return user.value;
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Error al iniciar sesión';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    loading.value = true;
    try {
      await authService.logout();
    } finally {
      localStorage.removeItem('auth_last_activity');
      user.value = null;
      loading.value = false;
    }
  }

  async function fetchUser() {
    try {
      user.value = await authService.fetchCurrentUser();
    } catch (err) {
      user.value = null;
    }
    return user.value;
  }

  window.addEventListener('auth:logout', () => {
    user.value = null;
  });

  return {
    user,
    loading,
    error,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  };
});