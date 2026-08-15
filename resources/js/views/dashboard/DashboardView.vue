<template>
  <div class="dashboard-page">
    <header class="dashboard-header">
      <div class="header-brand">
        <div class="header-logo">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="3" y="7" width="18" height="13" rx="2" stroke="white" stroke-width="2"/>
            <path d="M12 11v4M10 13h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <h1 class="header-title">Hospital Salud Integral</h1>
          <p class="header-subtitle">EL SALVADOR</p>
        </div>
      </div>

      <nav class="header-nav">
        <a href="#panel" @click.prevent="irAPanel" :class="['nav-link', activeTab === 'panel' ? 'active' : '']">Panel</a>
        <a href="#solicitudes" @click.prevent="irASolicitudes" :class="['nav-link', activeTab === 'solicitudes' ? 'active' : '']">Mis Solicitudes</a>
        <a href="#notificaciones" @click.prevent="toggleNotificaciones" :class="['nav-link', activeTab === 'notificaciones' ? 'active' : '']">Notificaciones</a>
        <a href="#ayuda" @click.prevent="irAAyuda" :class="['nav-link', activeTab === 'ayuda' ? 'active' : '']">Ayuda</a>
      </nav>

      <div class="header-user">
        <span class="bell-icon" @click="toggleNotificaciones" style="cursor: pointer;">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 8a6 6 0 1 1 12 0c0 4 1.5 5.5 1.5 5.5H4.5S6 12 6 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.5 17a2.5 2.5 0 0 0 5 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
        <div class="user-info">
          <p class="user-name">{{ authStore.user?.name ?? 'Usuario' }}</p>
          <p class="user-role">{{ authStore.user?.rol ?? 'Solicitante' }}</p>
        </div>
        <div class="user-avatar" title="Perfil">{{ userInitial }}</div>
        <button class="logout-btn" @click="cerrarSesion" title="Cerrar sesión">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
          </svg>
          <span class="logout-text">Cerrar sesión</span>
        </button>
      </div>
    </header>

    <main class="dashboard-main">
      <div class="welcome-row">
        <div>
          <h2 class="welcome-title">Bienvenido, {{ firstName }}</h2>
          <p class="welcome-subtitle">Gestión de mantenimientos y servicios técnicos hospitalarios.</p>
        </div>
        <router-link to="/solicitudes/nueva" class="new-request-btn">
          <span class="plus-icon">+</span>
          Nueva Solicitud
        </router-link>
      </div>

      <section class="metrics-row">
        <div class="metric-card">
          <div class="metric-top">
            <span class="metric-icon icon-amber">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M12 10v4l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <span class="metric-badge badge-amber">+2 hoy</span>
          </div>
          <p class="metric-label">PENDIENTES</p>
          <p class="metric-value">{{ resumen.pendientes }}</p>
        </div>

        <div class="metric-card">
          <div class="metric-top">
            <span class="metric-icon icon-blue">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="2"/>
                <path d="M3 19c0-3 2.7-5 6-5s6 2 6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M16 9a2.5 2.5 0 1 0 0-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M18 19c0-2.2-1.3-4-3-4.7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <span class="metric-badge badge-blue">Activo</span>
          </div>
          <p class="metric-label">EN PROCESO</p>
          <p class="metric-value">{{ resumen.en_proceso }}</p>
        </div>

        <div class="metric-card">
          <div class="metric-top">
            <span class="metric-icon icon-green">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                <path d="M8.5 12.5l2.2 2.2L16 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span class="metric-badge badge-green">Completados</span>
          </div>
          <p class="metric-label">COMPLETADAS</p>
          <p class="metric-value">{{ resumen.completadas }}</p>
        </div>
      </section>

      <section class="table-card">
        <div class="table-header">
          <h3>Solicitudes Recientes</h3>
          <a href="#" class="view-all-link">Ver todo el historial</a>
        </div>

        <p v-if="loading" class="table-loading">Cargando solicitudes...</p>
        <p v-else-if="error" class="table-error">{{ error }}</p>

        <table v-else class="solicitudes-table">
          <thead>
            <tr>
              <th>ID SOLICITUD</th>
              <th>SERVICIO / EQUIPO</th>
              <th>UBICACIÓN</th>
              <th>FECHA</th>
              <th>ESTADO</th>
              <th>ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="solicitud in solicitudes" :key="solicitud.id">
              <td class="cell-id">#SOL-{{ solicitud.id }}</td>
             <td>
    <p class="cell-titulo">{{ solicitud.titulo }}</p>
    <p class="cell-subtitulo">{{ solicitud.subtitulo }}</p>
    <p v-if="solicitud.descripcion" class="cell-descripcion">{{ solicitud.descripcion }}</p>
</td>              <td class="cell-ubicacion">{{ solicitud.ubicacion }}</td>
              <td>{{ formatFecha(solicitud.fecha) }}</td>
              <td>
                <span class="status-badge" :class="estadoClase(solicitud.estado)">
                  {{ solicitud.estado }}
                </span>
              </td>
              <td>
                <a href="#" class="details-link">Detalles ›</a>
              </td>
            </tr>
            <tr v-if="solicitudes.length === 0">
              <td colspan="6" class="cell-empty">No hay solicitudes registradas todavía.</td>
            </tr>
          </tbody>
        </table>

        <div class="table-footer">
          <span>Mostrando {{ solicitudes.length }} de un total de {{ totalSolicitudes }}</span>
        </div>
      </section>

      <div class="help-banner">
        <div class="help-text">
          <span class="help-icon">?</span>
          <div>
            <p class="help-title">¿Necesitas ayuda urgente?</p>
            <p class="help-subtitle">Si es una emergencia técnica, llama a la extensión 100 de Mantenimiento Crítico.</p>
          </div>
        </div>
        <button class="contact-btn">Contactar Soporte</button>
      </div>
    </main>

    <!-- Modal de Notificaciones -->
    <div v-if="showNotiModal" class="noti-modal-overlay" @click.self="showNotiModal = false">
      <div class="noti-modal-content">
        <div class="noti-header">
          <h3>Centro de Notificaciones</h3>
          <button @click="showNotiModal = false">&times;</button>
        </div>
        <div class="noti-body">
          <div class="noti-item active">
            <p class="noti-title">Nueva respuesta en ticket #SOL-1</p>
            <p class="noti-desc">El técnico asignado ha comenzado la reparación de aire acondicionado.</p>
            <span class="noti-time">Hace 5 minutos</span>
          </div>
          <div class="noti-item">
            <p class="noti-title">Sistema de Mantenimiento Integrado</p>
            <p class="noti-desc">Bienvenido al nuevo panel de solicitudes del Hospital Salud Integral.</p>
            <span class="noti-time">Hace 1 día</span>
          </div>
        </div>
      </div>
    </div>

    <footer class="dashboard-footer">
      © 2024 Hospital Salud Integral - Sistema de Gestión de Servicios Técnicos.
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';

const authStore = useAuthStore();
const router = useRouter();

const resumen = ref({ pendientes: 0, en_proceso: 0, completadas: 0, total: 0 });
const solicitudes = ref([]);
const totalSolicitudes = ref(0);
const loading = ref(true);
const error = ref('');

const activeTab = ref('panel');
const showNotiModal = ref(false);

function irAPanel() {
  activeTab.value = 'panel';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function irASolicitudes() {
  activeTab.value = 'solicitudes';
  setTimeout(() => {
    const el = document.querySelector('.table-card');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }, 50);
}

function toggleNotificaciones() {
  showNotiModal.value = !showNotiModal.value;
}

function irAAyuda() {
  activeTab.value = 'ayuda';
  setTimeout(() => {
    const el = document.querySelector('.help-banner');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }, 50);
}

const firstName = computed(() => {
  const nombre = authStore.user?.name ?? 'Usuario';
  return nombre.split(' ')[0];
});

const userInitial = computed(() => {
  const nombre = authStore.user?.name ?? 'U';
  return nombre.charAt(0).toUpperCase();
});

function formatFecha(fecha) {
  if (!fecha) return '';
  const date = new Date(fecha);
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
}

function estadoClase(estado) {
  if (estado === 'Pendiente') return 'status-pendiente';
  if (estado === 'En Proceso') return 'status-proceso';
  if (estado === 'Completada') return 'status-completada';
  return '';
}

async function cerrarSesion() {
  await authStore.logout();
  router.push('/login');
}

import { useRoute } from 'vue-router';
const route = useRoute();

async function cargarDatos() {
  loading.value = true;
  error.value = '';
  try {
    const [resumenData, listaData] = await Promise.all([
      solicitudesService.resumen(),
      solicitudesService.listar(),
    ]);
    resumen.value = resumenData;
    solicitudes.value = listaData.data ?? [];
    totalSolicitudes.value = listaData.total ?? solicitudes.value.length;
  } catch (e) {
    error.value = 'No se pudieron cargar las solicitudes. Intenta de nuevo más tarde.';
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await cargarDatos();
  const tab = route.query.tab;
  if (tab === 'solicitudes') {
    irASolicitudes();
  } else if (tab === 'ayuda') {
    irAAyuda();
  }
});
</script>

<style scoped>
.dashboard-page {
  min-height: 100vh;
  background: #f4f6fa;
  font-family: system-ui, -apple-system, sans-serif;
  color: #1e293b;
}
.dashboard-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  padding: 1rem 2rem;
  border-bottom: 1px solid #e5e9f0;
  flex-wrap: wrap;
  gap: 1rem;
}
.header-brand { display: flex; align-items: center; gap: 0.75rem; }
.header-logo { width: 38px; height: 38px; border-radius: 10px; background: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.header-logo svg { width: 20px; height: 20px; }
.header-title { font-size: 1rem; font-weight: 700; margin: 0; line-height: 1.2; }
.header-subtitle { font-size: 0.7rem; font-weight: 700; color: #2563eb; margin: 0; letter-spacing: 0.05em; }
.header-nav { display: flex; gap: 1.5rem; margin-left: auto; margin-right: auto; }
.nav-link { color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; padding-bottom: 0.25rem; }
.nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
.header-user { display: flex; align-items: center; gap: 0.75rem; }
.logout-btn {
  display: flex; align-items: center; gap: 0.35rem; background: #fee2e2; color: #ef4444;
  border: none; padding: 0.45rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700;
  cursor: pointer; transition: all 0.2s ease;
}
.logout-btn:hover { background: #fecaca; color: #dc2626; }
.logout-btn svg { width: 14px; height: 14px; }
.bell-icon { color: #94a3b8; display: flex; }
.bell-icon svg { width: 20px; height: 20px; }
.user-info { text-align: right; }
.user-name { font-size: 0.85rem; font-weight: 700; margin: 0; }
.user-role { font-size: 0.75rem; color: #64748b; margin: 0; }
.user-avatar { width: 36px; height: 36px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; }
.user-avatar:hover { background: #1d4ed8; }

/* Modal de Notificaciones */
.noti-modal-overlay {
  position: fixed; inset: 0; z-index: 50; display: flex; align-items: flex-start; justify-content: flex-end;
  background: rgba(15, 23, 42, 0.3); backdrop-filter: blur(4px); padding: 1.5rem;
}
.noti-modal-content {
  width: 100%; max-width: 380px; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden; margin-top: 4.5rem;
}
.noti-header {
  display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9;
}
.noti-header h3 { margin: 0; font-size: 0.95rem; font-weight: 700; color: #0f172a; }
.noti-header button { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; line-height: 1; }
.noti-body { display: flex; flex-direction: column; max-height: 400px; overflow-y: auto; }
.noti-item { padding: 1rem 1.25rem; border-bottom: 1px solid #f8fafc; transition: background 0.2s ease; cursor: pointer; }
.noti-item:hover { background: #f8fafc; }
.noti-item.active { background: #eff6ff; border-left: 3px solid #2563eb; }
.noti-title { font-size: 0.85rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem; }
.noti-desc { font-size: 0.8rem; color: #64748b; margin: 0 0 0.5rem; line-height: 1.4; }
.noti-time { font-size: 0.72rem; color: #94a3b8; font-weight: 500; }

.dashboard-main { max-width: 1100px; margin: 0 auto; padding: 2rem; }
.welcome-row { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
.welcome-title { font-size: 1.8rem; font-weight: 800; margin: 0 0 0.4rem; }
.welcome-subtitle { color: #64748b; margin: 0; }
.new-request-btn { display: flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #fff; border: none; padding: 0.75rem 1.25rem; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.95rem; text-decoration: none; }
.new-request-btn:hover { background: #1d4ed8; }
.plus-icon { font-size: 1.1rem; }
.metrics-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem; }
.metric-card { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.metric-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.metric-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.metric-icon svg { width: 20px; height: 20px; }
.icon-amber { background: #fef3c7; color: #d97706; }
.icon-blue { background: #dbeafe; color: #2563eb; }
.icon-green { background: #dcfce7; color: #16a34a; }
.metric-badge { font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 20px; }
.badge-amber { background: #fef3c7; color: #b45309; }
.badge-blue { background: #dbeafe; color: #1d4ed8; }
.badge-green { background: #dcfce7; color: #15803d; }
.metric-label { font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 0.04em; margin: 0 0 0.3rem; }
.metric-value { font-size: 2rem; font-weight: 800; margin: 0; }
.table-card { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem; }
.table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.table-header h3 { margin: 0; font-size: 1.1rem; }
.view-all-link { color: #2563eb; font-weight: 600; font-size: 0.9rem; text-decoration: none; }
.table-loading, .table-error { padding: 1.5rem 0; color: #64748b; }
.table-error { color: #dc2626; }
.solicitudes-table { width: 100%; border-collapse: collapse; }
.solicitudes-table th { text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0; }
.solicitudes-table td { padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
.cell-id { font-weight: 700; }
.cell-titulo { font-weight: 600; margin: 0; }
.cell-subtitulo { font-size: 0.8rem; color: #94a3b8; margin: 0.1rem 0 0; }
.cell-ubicacion { font-style: italic; color: #475569; }
.cell-empty { text-align: center; color: #94a3b8; padding: 2rem 0; }
.status-badge { font-size: 0.78rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 20px; }
.status-pendiente { background: #fef3c7; color: #b45309; }
.status-proceso { background: #dbeafe; color: #1d4ed8; }
.status-completada { background: #dcfce7; color: #15803d; }
.details-link { color: #2563eb; font-weight: 600; font-size: 0.9rem; text-decoration: none; }
.table-footer { margin-top: 1rem; font-size: 0.85rem; color: #94a3b8; }
.help-banner { background: linear-gradient(90deg, #2563eb, #1d4ed8); border-radius: 14px; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; color: #fff; }
.help-text { display: flex; align-items: flex-start; gap: 1rem; }
.help-icon { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
.help-title { font-weight: 700; margin: 0 0 0.25rem; }
.help-subtitle { font-size: 0.9rem; margin: 0; opacity: 0.9; }
.contact-btn { background: #fff; color: #2563eb; border: none; font-weight: 700; padding: 0.7rem 1.3rem; border-radius: 10px; cursor: pointer; }
.dashboard-footer { text-align: center; color: #94a3b8; font-size: 0.8rem; padding: 2rem 0; }
@media (max-width: 768px) {
  .header-nav { display: none; }
  .metrics-row { grid-template-columns: 1fr; }
  .solicitudes-table { font-size: 0.85rem; }
}
</style>