/**
 * router/index.js
 * Definición de rutas y guard global de autenticación con control de acceso por roles.
 *
 * Convención para módulos nuevos:
 * - meta.requiresAuth = true  -> requiere sesión activa
 * - meta.requiresGuest = true -> solo accesible SIN sesión (ej. login)
 * - meta.roles = [...]        -> lista de roles con permisos de acceso a la ruta
 */

import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/auth/LoginView.vue';
import DashboardView from '../views/dashboard/DashboardView.vue';
import NuevaSolicitudView from '../views/solicitudes/NuevaSolicitudView.vue';
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
    meta: { 
      requiresAuth: true, 
      roles: ['Solicitante'] 
    },
  },
  {
    path: '/solicitudes/nueva',
    name: 'nueva-solicitud',
    component: NuevaSolicitudView,
    meta: { 
      requiresAuth: true, 
      roles: ['Solicitante'] 
    },
  },
  {
    path: '/solicitudes/:id',
    name: 'detalle-solicitud',
    component: DetalleSolicitudView,
    meta: { 
      requiresAuth: true, 
      roles: ['Administrador', 'Coordinador', 'Técnico', 'Solicitante'] 
    },
  },
  {
    path: '/coordinacion',
    redirect: '/admin',
  },
  {
    path: '/tareas',
    name: 'mis-tareas',
    component: MisTareasView,
    meta: { 
      requiresAuth: true, 
      roles: ['Técnico'] 
    },
  },
  {
    path: '/tareas/:id',
    name: 'detalle-tarea',
    component: DetalleTareaView,
    meta: { 
      requiresAuth: true, 
      roles: ['Técnico'] 
    },
  },
  {
    path: '/admin',
    name: 'admin',
    component: PanelAdminView,
    meta: { 
      requiresAuth: true, 
      roles: ['Administrador', 'Coordinador'] 
    },
  },
  {
    path: '/reportes',
    name: 'reportes',
    component: ReportesView,
    meta: { 
      requiresAuth: true, 
      roles: ['Administrador', 'Coordinador'] 
    },
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
function esAdmin(rol) {
  if (!rol) return false;
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  return r === 'administrador' || r === 'admin';
}

function rutaSegunRol(rol) {
  if (!rol) return 'dashboard';
  if (esAdmin(rol)) return 'admin';
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  if (r === 'coordinador') return 'admin';
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

  // VALIDACIÓN Y BLINDAJE DE SEGURIDAD BASADO EN ROLES
  if (isAuthenticated && to.meta.roles) {
    const user = authService.getUser();
    let userRolNormalizado = user?.rol 
      ? user.rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '') 
      : '';
    if (userRolNormalizado === 'admin') {
      userRolNormalizado = 'administrador';
    }
    
    const rolesPermitidosNormalizados = to.meta.roles.map(r => 
      r.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    );

    // Si el rol del usuario no está autorizado para la ruta solicitada, lo redirige
    if (!rolesPermitidosNormalizados.includes(userRolNormalizado)) {
      return { name: rutaSegunRol(user?.rol) };
    }
  }
});

export default router;