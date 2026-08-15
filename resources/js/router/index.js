/**
 * router/index.js
 * Definición de rutas y guard global de autenticación.
 *
 * Convención para módulos nuevos:
 * - meta.requiresAuth = true  -> requiere sesión activa
 * - meta.requiresGuest = true -> solo accesible SIN sesión (ej. login)
 */

import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/auth/LoginView.vue';
import DashboardView from '../views/dashboard/DashboardView.vue';
import NuevaSolicitudView from '../views/solicitudes/NuevaSolicitudView.vue';
import PanelCoordinacionView from '../views/coordinacion/PanelCoordinacionView.vue';
import DetalleSolicitudView from '../views/coordinacion/DetalleSolicitudView.vue';
import MisTareasView from '../views/tecnico/MisTareasView.vue';
import DetalleTareaView from '../views/tecnico/DetalleTareaView.vue';
import PanelAdminView from '../views/admin/PanelAdminView.vue';
import ReportesView from '../views/reportes/ReportesView.vue';
import authService from '../services/authService';

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: '/solicitudes/nueva',
    name: 'nueva-solicitud',
    component: NuevaSolicitudView,
    meta: { requiresAuth: true },
  },
  {
    path: '/solicitudes/:id',
    name: 'detalle-solicitud',
    component: DetalleSolicitudView,
    meta: { requiresAuth: true },
  },
  {
    path: '/coordinacion',
    name: 'coordinacion',
    component: PanelCoordinacionView,
    meta: { requiresAuth: true },
  },
  {
    path: '/tareas',
    name: 'mis-tareas',
    component: MisTareasView,
    meta: { requiresAuth: true },
  },
  {
    path: '/tareas/:id',
    name: 'detalle-tarea',
    component: DetalleTareaView,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'admin',
    component: PanelAdminView,
    meta: { requiresAuth: true },
  },
  {
    path: '/reportes',
    name: 'reportes',
    component: ReportesView,
    meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { requiresGuest: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Devuelve el nombre de ruta correcto según el rol del usuario.
// Maneja tanto los valores actuales ('Administrador') como valores
// cacheados en localStorage de versiones anteriores ('admin').
function esAdmin(rol) {
  if (!rol) return false;
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  return r === 'administrador' || r === 'admin';
}

function rutaSegunRol(rol) {
  if (!rol) return 'dashboard';
  if (esAdmin(rol)) return 'admin';
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  if (r === 'coordinador') return 'coordinacion';
  if (r === 'tecnico') return 'mis-tareas';
  return 'dashboard'; // Solicitante u otros
}

router.beforeEach((to) => {
  const isAuthenticated = authService.isAuthenticated();

  // Sin sesión → al login
  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: 'login' };
  }

  // Usuario autenticado intentando entrar a página de invitado (login) → su panel
  if (to.meta.requiresGuest && isAuthenticated) {
    const user = authService.getUser();
    return { name: rutaSegunRol(user?.rol) };
  }

  // PROTECCIÓN TOTAL: el admin NUNCA puede llegar al /dashboard de solicitantes.
  // Si está autenticado y navega a /dashboard siendo admin → forzar a /admin.
  if (isAuthenticated && to.name === 'dashboard') {
    const user = authService.getUser();
    if (esAdmin(user?.rol)) {
      return { name: 'admin' };
    }
  }
});

export default router;