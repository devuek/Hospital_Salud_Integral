<template>
  <div class="tareas-page">
    <header class="page-header">
      <div class="header-brand">
        <div class="header-logo">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="3" y="7" width="18" height="13" rx="2" stroke="white" stroke-width="2"/>
            <path d="M12 11v4M10 13h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h1 class="header-title">Hospital Salud Integral</h1>
      </div>

      <nav class="header-nav">
        <router-link to="/tareas" class="nav-link active">Tareas</router-link>
      </nav>

      <div class="header-actions">
        <!-- Buscador en Mis Tareas -->
        <div id="tec-buscador-global" style="position: relative; margin-right: 0.5rem;">
          <div style="position: relative; display: flex; align-items: center;">
            <svg style="position: absolute; left: 0.65rem; width: 0.95rem; height: 0.95rem; color: #94a3b8; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input
              type="text"
              v-model="buscarGlobal"
              @focus="mostrarDropdownBuscar = true"
              @keyup.escape="mostrarDropdownBuscar = false"
              @keyup.enter="ejecutarBusqueda"
              placeholder="Buscar tarea..."
              style="width: 13rem; padding: 0.45rem 0.65rem 0.45rem 2rem; font-size: 0.8125rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background: #f8fafc; color: #334155; outline: none; transition: all 0.2s;"
            />
            <button v-if="buscarGlobal" @click="buscarGlobal = ''; mostrarDropdownBuscar = false" style="position: absolute; right: 0.5rem; border: none; background: transparent; color: #94a3b8; cursor: pointer; display: flex;">
              <svg style="width: 0.9rem; height: 0.9rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Dropdown resultados -->
          <div
            v-if="mostrarDropdownBuscar && buscarGlobal.trim().length >= 1"
            style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; z-index: 50; width: 19rem; border-radius: 0.75rem; border: 1px solid #f1f5f9; background: white; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden;"
          >
            <div v-if="resultadosBusqueda.length === 0" style="padding: 1.5rem 1rem; text-align: center;">
              <p style="font-size: 0.8125rem; color: #94a3b8; margin: 0;">Sin resultados para <strong>"{{ buscarGlobal }}"</strong></p>
            </div>
            <template v-else>
              <div style="border-bottom: 1px solid #f1f5f9; background: #f8fafc; padding: 0.4rem 1rem;">
                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin: 0;">{{ resultadosBusqueda.length }} tarea(s)</p>
              </div>
              <div style="max-height: 18rem; overflow-y: auto;">
                <button
                  v-for="sol in resultadosBusqueda"
                  :key="sol.id"
                  @click="irATarea(sol)"
                  style="display: flex; width: 100%; align-items: flex-start; gap: 0.75rem; padding: 0.65rem 1rem; border: none; background: transparent; text-align: left; cursor: pointer; border-bottom: 1px solid #f8fafc;"
                  onmouseover="this.style.background='#eff6ff'"
                  onmouseout="this.style.background='transparent'"
                >
                  <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase; border-radius: 9999px; padding: 0.15rem 0.5rem; background: #dbeafe; color: #1d4ed8;">
                    {{ sol.estado }}
                  </span>
                  <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 0.75rem; font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin: 0;">{{ sol.titulo || 'Sin título' }}</p>
                    <p style="font-size: 0.6875rem; color: #2563eb; font-family: monospace; font-weight: 600; margin: 0.15rem 0 0 0;">{{ sol._folio }} · <span style="color: #64748b; font-family: sans-serif;">{{ sol.ubicacion }}</span></p>
                  </div>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Campana de Notificaciones para Técnicos -->
        <div id="tec-notificaciones" class="relative" style="position: relative; margin-right: 0.5rem;">
          <button @click="toggleNotificaciones" class="relative rounded-full p-2 text-slate-500 hover:bg-slate-100 focus:outline-none" style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: 9999px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer;" title="Notificaciones de tareas">
            <svg class="h-5 w-5 text-amber-500" style="width: 1.15rem; height: 1.15rem; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <!-- Badge indicador si hay no leídas -->
            <span v-if="notificacionesNoLeidas.length > 0" style="position: absolute; top: 2px; right: 2px; display: flex; height: 0.6rem; width: 0.6rem;">
              <span class="animate-ping" style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background: #f87171; opacity: 0.75;"></span>
              <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.6rem; width: 0.6rem; background: #ef4444;"></span>
            </span>
          </button>

          <!-- Dropdown Notificaciones Técnico -->
          <div v-show="showNotificaciones" style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; z-index: 50; width: 22rem; border-radius: 1rem; border: 1px solid #f1f5f9; background: white; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding: 0.75rem 1rem;">
              <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #1e293b;">Notificaciones</span>
                <span v-if="notificacionesNoLeidas.length > 0" style="border-radius: 9999px; background: #fee2e2; padding: 0.1rem 0.45rem; font-size: 0.65rem; font-weight: 800; color: #b91c1c;">
                  {{ notificacionesNoLeidas.length }} nueva(s)
                </span>
              </div>
              <button v-if="notificacionesNoLeidas.length > 0" @click="marcarTodasLeidas" style="font-size: 0.7rem; font-weight: 600; color: #2563eb; background: none; border: none; cursor: pointer;">
                Marcar leídas
              </button>
            </div>

            <div style="max-height: 18rem; overflow-y: auto;">
              <div v-if="notificaciones.length === 0" style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                No tienes notificaciones pendientes.
              </div>
              <div
                v-for="n in notificaciones"
                :key="n.id"
                @click="clickNotificacion(n)"
                style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 1rem; cursor: pointer; border-bottom: 1px solid #f8fafc; transition: all 0.15s;"
                :style="{ background: !n.leida ? '#fef2f2' : '#ffffff' }"
              >
                <div style="flex-shrink: 0; margin-top: 0.15rem;">
                  <span v-if="n.tipo === 'rechazada'" class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                  </span>
                  <span v-else-if="n.tipo === 'reasignada'" class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                  <span v-else-if="n.tipo === 'alta'" class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                  </span>
                  <span v-else class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                  </span>
                </div>
                <div style="flex: 1; min-width: 0;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                    <p style="font-size: 0.75rem; margin: 0; line-height: 1.35;" :style="{ fontWeight: !n.leida ? '800' : '600', color: !n.leida ? '#7f1d1d' : '#334155' }">
                      {{ n.mensaje }}
                    </p>
                    <span v-if="!n.leida" style="width: 6px; height: 6px; border-radius: 9999px; background: #ef4444; flex-shrink: 0; margin-top: 4px;"></span>
                  </div>
                  <span style="font-size: 0.65rem; color: #94a3b8; display: block; margin-top: 0.25rem;">{{ n.tiempo }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="user-info-badge">
          <div class="user-avatar-circle" @click="menuAbierto = !menuAbierto">{{ userInitial }}</div>
          <div class="user-info-text">
            <span class="user-name-text">{{ userName }}</span>
            <span class="user-role-text">{{ userRol }}<template v-if="userArea"> · {{ userArea }}</template></span>
          </div>
          <div v-if="menuAbierto" class="dropdown-menu">
            <button class="dropdown-item" @click="cerrarSesion">Cerrar sesión</button>
          </div>
        </div>
      </div>
    </header>

    <main class="page-main">
      <div class="title-row">
        <div>
          <h2 class="page-title">Mis Solicitudes Asignadas</h2>
          <p class="page-subtitle">Tareas de mantenimiento asignadas a ti</p>
        </div>
        <div class="filter-tabs">
          <button
            v-for="f in filtros"
            :key="f.value"
            class="filter-tab"
            :class="{ active: filtroActivo === f.value }"
            @click="cambiarFiltro(f.value)"
          >
            {{ f.label }}
          </button>
        </div>
      </div>

      <!-- Banner global de Reasignaciones Rechazadas si las hay -->
      <div v-if="tareasConReasignacionRechazada.length > 0" style="margin-bottom: 1.25rem; border-radius: 12px; border: 1px solid #fca5a5; background: #fef2f2; padding: 1rem; color: #991b1b; display: flex; align-items: flex-start; gap: 0.75rem;">
        <span style="display: flex; width: 2rem; height: 2rem; border-radius: 8px; background: #fee2e2; color: #dc2626; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </span>
        <div style="font-size: 0.8125rem; line-height: 1.45;">
          <p style="font-weight: 800; margin: 0 0 0.25rem 0; font-size: 0.875rem; color: #7f1d1d;">
            El Coordinador ha rechazado tu solicitud de reasignación
          </p>
          Debes continuar con la ejecución del trabajo asignado. Si tienes algún inconveniente o no puedes realizarlo, por favor <strong>comunícate directamente con el Coordinador</strong>.
        </div>
      </div>

      <p v-if="loading" class="state-msg">Cargando tareas...</p>
      <p v-else-if="error" class="state-msg error">{{ error }}</p>
      <p v-else-if="tareas.length === 0" class="state-msg">No tienes tareas asignadas en este momento.</p>
      <p v-else-if="tareasFiltradas.length === 0" class="state-msg">No hay tareas con el filtro "{{ filtros.find(f => f.value === filtroActivo)?.label }}".</p>

      <div v-else class="tareas-grid">
        <div v-for="t in tareasFiltradas" :key="t.id" class="tarea-card" :class="{ 'card-alta': t.prioridad === 'Alta' }">
          <div class="card-top">
            <span class="cell-id">#SOL-{{ abreviarId(t.id) }}</span>
            <span class="priority-badge" :class="prioridadClase(t.prioridad)">{{ t.prioridad }}</span>
          </div>

          <h3 class="card-title">{{ t.titulo }}</h3>
          <p class="card-meta">{{ t.ubicacion }} <span v-if="t.departamento"> · {{ t.departamento }}</span></p>
          <p v-if="t.descripcion" class="card-descripcion">{{ t.descripcion }}</p>
          
          <!-- Alerta de Reasignación en la Tarjeta -->
          <div v-if="estadoReasignacionTarea(t) === 'Rechazada'" style="display: flex; align-items: center; gap: 0.4rem; margin: 0.5rem 0; padding: 0.4rem 0.6rem; border-radius: 6px; background: #fef2f2; border: 1px solid #fecaca; font-size: 0.72rem; color: #991b1b; font-weight: 700;">
            <svg class="h-3.5 w-3.5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Reasignación rechazada por Coordinación: habla con el Coordinador.</span>
          </div>
          <div v-else-if="estadoReasignacionTarea(t) === 'Reasignada'" style="display: flex; align-items: center; gap: 0.4rem; margin: 0.5rem 0; padding: 0.4rem 0.6rem; border-radius: 6px; background: #f0f9ff; border: 1px solid #bae6fd; font-size: 0.72rem; color: #0369a1; font-weight: 700;">
            <svg class="h-3.5 w-3.5 text-sky-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Reasignada por Coordinación. Si no estás de acuerdo, habla con el Coordinador.</span>
          </div>
          <div v-else-if="estadoReasignacionTarea(t) === 'Pendiente'" style="display: flex; align-items: center; gap: 0.4rem; margin: 0.5rem 0; padding: 0.4rem 0.6rem; border-radius: 6px; background: #eef2ff; border: 1px solid #c7d2fe; font-size: 0.72rem; color: #3730a3; font-weight: 700;">
            <svg class="h-3.5 w-3.5 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span>Reasignación solicitada en trámite con Coordinación.</span>
          </div>

          <p class="card-date">{{ formatFecha(t.fecha) }}</p>
          <div class="card-bottom">
            <span class="status-badge" :class="estadoClase(t.estado)">{{ t.estado }}</span>
            <div class="card-actions">
              <router-link :to="`/tareas/${t.id}`" class="detail-link">Ver detalle</router-link>
              <button
                v-if="siguienteEstado(t.estado)"
                class="status-btn"
                :disabled="cambiandoId === t.id"
                @click="avanzarEstado(t)"
              >
                {{ cambiandoId === t.id ? 'Guardando...' : `Marcar ${siguienteEstado(t.estado)}` }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Alerta Toast Flotante -->
    <div v-if="alertaToast.mostrar" class="toast-alerta" :class="alertaToast.tipo">
      <span class="toast-icon">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </span>
      <span class="toast-text">{{ alertaToast.mensaje }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';
import authService from '../../services/authService';

const authStore = useAuthStore();
const router = useRouter();

const currentUser = computed(() => authService.getUser());
const userInitial = computed(() => (currentUser.value?.nombre ?? 'U').charAt(0).toUpperCase());
const userName    = computed(() => currentUser.value?.nombre ?? 'Usuario');
const userRol     = computed(() => currentUser.value?.rol ?? 'Técnico');
const userArea    = computed(() => currentUser.value?.area_servicio ?? null);

const tareas = ref([]);
const loading = ref(true);
const error = ref('');
const filtroActivo = ref('todas');
const cambiandoId = ref(null);
const menuAbierto = ref(false);

const buscarGlobal = ref('');
const mostrarDropdownBuscar = ref(false);

const resultadosBusqueda = computed(() => {
  const raw = buscarGlobal.value.trim().toLowerCase();
  const q = raw.replace(/^#(sol|req)-/i, '').replace(/^#/i, '');
  if (!q) return [];

  return (tareas.value || []).filter(t => {
    const folio = abreviarId(t.id).toLowerCase();
    return (
      folio.includes(q) ||
      `sol-${folio}`.includes(q) ||
      (t.id && t.id.toString().toLowerCase().includes(q)) ||
      (t.titulo && t.titulo.toLowerCase().includes(q)) ||
      (t.departamento && t.departamento.toLowerCase().includes(q)) ||
      (t.ubicacion && t.ubicacion.toLowerCase().includes(q)) ||
      (t.estado && t.estado.toLowerCase().includes(q)) ||
      (t.prioridad && t.prioridad.toLowerCase().includes(q)) ||
      (t.descripcion && t.descripcion.toLowerCase().includes(q))
    );
  }).map(t => ({ ...t, _folio: `#SOL-${abreviarId(t.id)}` })).slice(0, 8);
});

function ejecutarBusqueda() {
  if (resultadosBusqueda.value.length === 1) {
    irATarea(resultadosBusqueda.value[0]);
  } else {
    mostrarDropdownBuscar.value = true;
  }
}

function irATarea(t) {
  mostrarDropdownBuscar.value = false;
  buscarGlobal.value = '';
  router.push(`/tareas/${t.id}`);
}

const alertaToast = ref({
  mostrar: false,
  mensaje: '',
  tipo: 'error'
});

const filtros = [
  { value: 'todas', label: 'Todas' },
  { value: 'Pendiente', label: 'Pendientes' },
  { value: 'En Proceso', label: 'En Proceso' },
  { value: 'Completada', label: 'Completadas' },
];

const miIdUsuario = computed(() => currentUser.value?.id_usuario || authStore.user?.id_usuario || null);

function estadoReasignacionTarea(t) {
  const reas = t.reasignaciones || t.original?.reasignaciones || (t.original?.solicitud?.reasignaciones) || [];
  const miId = miIdUsuario.value;

  const rechazada = reas.find(r => r.estado === 'Rechazada' && (r.id_usuario_tecnico_solicitante === miId || !r.id_usuario_tecnico_solicitante));
  if (rechazada) return 'Rechazada';

  const pendiente = reas.find(r => r.estado === 'Pendiente' && r.id_usuario_tecnico_solicitante === miId);
  if (pendiente) return 'Pendiente';

  const aprobada = reas.find(r => r.estado === 'Aprobada' && r.id_usuario_tecnico_solicitante !== miId);
  if (aprobada) return 'Reasignada';

  // Verificar en historial
  const hist = t.historial || t.original?.historial || (t.original?.solicitud?.historial) || [];
  const eventoRech = hist.find(h => h.campo_modificado === 'reasignacion_rechazada');
  if (eventoRech) return 'Rechazada';

  const eventoAprob = hist.find(h => h.campo_modificado === 'reasignacion_aprobada');
  if (eventoAprob) return 'Reasignada';

  return null;
}

const tareasConReasignacionRechazada = computed(() => {
  return (tareas.value || []).filter(t => estadoReasignacionTarea(t) === 'Rechazada');
});

const tareasFiltradas = computed(() => {
  if (filtroActivo.value === 'todas') return tareas.value || [];
  return (tareas.value || []).filter(t => t.estado === filtroActivo.value);
});

function abreviarId(uuid) {
  if (!uuid) return '';
  const clean = uuid.replace(/-/g, '');
  let hash1 = 0;
  for (let i = 0; i < 16; i++) {
    hash1 = ((hash1 << 5) - hash1) + clean.charCodeAt(i);
    hash1 |= 0;
  }
  let hash2 = 0;
  for (let i = 16; i < clean.length; i++) {
    hash2 = ((hash2 << 5) - hash2) + clean.charCodeAt(i);
    hash2 |= 0;
  }
  const h1 = Math.abs(hash1).toString(36).toUpperCase().padStart(4, '0');
  const h2 = Math.abs(hash2).toString(36).toUpperCase().padStart(5, '0');
  return `${h1}${h2}`.substring(0, 9);
}

function formatFecha(fecha) {
  if (!fecha) return '';
  const date = new Date(fecha);
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function prioridadClase(prioridad) {
  if (prioridad === 'Alta') return 'priority-alta';
  if (prioridad === 'Media') return 'priority-media';
  return 'priority-baja';
}

function estadoClase(estado) {
  if (estado === 'Pendiente') return 'status-pendiente';
  if (estado === 'En Proceso') return 'status-proceso';
  if (estado === 'Completada') return 'status-completada';
  return '';
}

function siguienteEstado(estadoActual) {
  if (estadoActual === 'Pendiente') return 'En Proceso';
  if (estadoActual === 'En Proceso') return 'Completada';
  return null;
}

const notificaciones = ref([]);
const showNotificaciones = ref(false);

const notificacionesNoLeidas = computed(() => notificaciones.value.filter(n => !n.leida));

function toggleNotificaciones() {
  showNotificaciones.value = !showNotificaciones.value;
}

function marcarTodasLeidas() {
  const leidas = JSON.parse(localStorage.getItem('notif_tecnico_leidas') || '[]');
  notificaciones.value.forEach(n => {
    n.leida = true;
    if (!leidas.includes(n.id)) leidas.push(n.id);
  });
  localStorage.setItem('notif_tecnico_leidas', JSON.stringify(leidas));
}

function clickNotificacion(n) {
  n.leida = true;
  const leidas = JSON.parse(localStorage.getItem('notif_tecnico_leidas') || '[]');
  if (!leidas.includes(n.id)) {
    leidas.push(n.id);
    localStorage.setItem('notif_tecnico_leidas', JSON.stringify(leidas));
  }
  showNotificaciones.value = false;
  if (n.tareaId) {
    router.push(`/tareas/${n.tareaId}`);
  }
}

function construirNotificacionesTecnico() {
  const leidas = new Set(JSON.parse(localStorage.getItem('notif_tecnico_leidas') || '[]'));
  const lista = [];
  const miId = miIdUsuario.value;

  (tareas.value || []).forEach(t => {
    const reas = t.reasignaciones || t.original?.reasignaciones || (t.original?.solicitud?.reasignaciones) || [];
    
    // 1. Reasignaciones Rechazadas
    const rech = reas.find(r => r.estado === 'Rechazada');
    if (rech) {
      const idNotif = `notif-tec-rech-${rech.id_solicitud_reasignacion || t.id}`;
      const motivo = rech.comentarios_coordinador ? `: "${rech.comentarios_coordinador}"` : '';
      lista.unshift({
        id: idNotif,
        tareaId: t.id,
        tipo: 'rechazada',
        mensaje: `El Coordinador rechazó la reasignación de #${abreviarId(t.id)}${motivo}. Debes continuar el trabajo.`,
        tiempo: rech.updated_at ? formatFecha(rech.updated_at) : 'Rechazada',
        leida: leidas.has(idNotif)
      });
    }

    // 2. Reasignaciones Aprobadas (el técnico es el receptor)
    const aprob = reas.find(r => r.estado === 'Aprobada' && r.id_usuario_tecnico_solicitante !== miId);
    if (aprob) {
      const idNotif = `notif-tec-aprob-${aprob.id_solicitud_reasignacion || t.id}`;
      lista.push({
        id: idNotif,
        tareaId: t.id,
        tipo: 'reasignada',
        mensaje: `Se te ha reasignado el Ticket #${abreviarId(t.id)} (${t.titulo}). Habla con el coordinador si no estás de acuerdo.`,
        tiempo: aprob.updated_at ? formatFecha(aprob.updated_at) : 'Reasignada',
        leida: leidas.has(idNotif)
      });
    }

    // 3. Tareas en Prioridad Alta
    if (t.prioridad === 'Alta' && t.estado !== 'Completada') {
      const idNotif = `notif-tec-alta-${t.id}`;
      lista.push({
        id: idNotif,
        tareaId: t.id,
        tipo: 'alta',
        mensaje: `Tarea de Prioridad Alta #${abreviarId(t.id)}: ${t.titulo} en ${t.ubicacion}.`,
        tiempo: 'Urgente',
        leida: leidas.has(idNotif)
      });
    }
  });

  notificaciones.value = lista;
}

async function cargarTareas() {
  loading.value = true;
  error.value = '';
  try {
    const data = await solicitudesService.listar();
    tareas.value = Array.isArray(data) ? data : (data.data ?? []);
    construirNotificacionesTecnico();
  } catch (e) {
    error.value = 'No se pudieron cargar tus tareas.';
  } finally {
    loading.value = false;
  }
}

function cambiarFiltro(valor) {
  filtroActivo.value = valor;
}

async function avanzarEstado(tarea) {
  const nuevoEstado = siguienteEstado(tarea.estado);
  if (!nuevoEstado) return;

  if (nuevoEstado === 'Completada') {
    alertaToast.value = {
      mostrar: true,
      mensaje: 'No se pudo completar la tarea, verifica los datos.',
      tipo: 'error'
    };
    setTimeout(() => {
      alertaToast.value.mostrar = false;
    }, 4000);
    return;
  }

  cambiandoId.value = tarea.id;
  try {
    await solicitudesService.actualizar(tarea.id, { estado: nuevoEstado });
    tarea.estado = nuevoEstado;
  } catch (e) {
    error.value = 'No se pudo actualizar el estado de la tarea.';
  } finally {
    cambiandoId.value = null;
  }
}

onMounted(async () => {
  await cargarTareas();
  document.addEventListener('click', (e) => {
    const el = document.getElementById('tec-notificaciones');
    if (el && !el.contains(e.target)) {
      showNotificaciones.value = false;
    }
  });
});

async function cerrarSesion() {
  menuAbierto.value = false;
  await authStore.logout();
  router.push({ name: 'login' });
}

onMounted(() => {
  cargarTareas();
});
</script>

<style scoped>
.tareas-page { min-height: 100vh; background: #f4f6fa; font-family: system-ui, -apple-system, sans-serif; color: #1e293b; }
.page-header { display: flex; align-items: center; gap: 1.5rem; background: #fff; padding: 0.85rem 1.5rem; border-bottom: 1px solid #e5e9f0; flex-wrap: wrap; }
.header-brand { display: flex; align-items: center; gap: 0.6rem; }
.header-logo { width: 32px; height: 32px; border-radius: 8px; background: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.header-logo svg { width: 18px; height: 18px; }
.header-title { font-size: 0.95rem; font-weight: 700; margin: 0; }
.header-nav { display: flex; gap: 1.25rem; margin-left: auto; }
.nav-link { color: #64748b; text-decoration: none; font-size: 0.88rem; font-weight: 600; }
.nav-link.active { color: #2563eb; }
.header-actions { display: flex; align-items: center; }

.user-menu { position: relative; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; }

/* Badge de usuario con nombre, rol y área */
.user-info-badge { position: relative; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
.user-avatar-circle { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(37,99,235,0.35); }
.user-info-text { display: flex; flex-direction: column; line-height: 1.2; }
.user-name-text { font-size: 0.8rem; font-weight: 700; color: #1e293b; }
.user-role-text { font-size: 0.7rem; color: #64748b; font-weight: 500; }
.dropdown-menu {
  position: absolute;
  right: 0;
  top: 42px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  min-width: 140px;
  z-index: 20;
  overflow: hidden;
}
.dropdown-item {
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 0.6rem 0.9rem;
  font-size: 0.85rem;
  color: #dc2626;
  cursor: pointer;
}
.dropdown-item:hover { background: #f8fafc; }

.page-main { max-width: 1100px; margin: 0 auto; padding: 1.5rem; }
.title-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.page-title { font-size: 1.3rem; font-weight: 800; margin: 0; }
.page-subtitle { margin: 0.2rem 0 0; font-size: 0.85rem; color: #64748b; }

.filter-tabs { display: flex; gap: 0.4rem; }
.filter-tab { background: #fff; border: 1px solid #e2e8f0; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; color: #475569; }
.filter-tab.active { background: #2563eb; color: #fff; border-color: #2563eb; }

.state-msg { padding: 2rem 0; text-align: center; color: #64748b; }
.state-msg.error { color: #dc2626; }

.tareas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
.tarea-card { background: #fff; border-radius: 12px; padding: 1.1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 0.5rem; }
.card-alta { border-left: 3px solid #dc2626; }
.card-top { display: flex; justify-content: space-between; align-items: center; }
.cell-id { font-weight: 700; font-size: 0.85rem; color: #94a3b8; }
.card-title { font-size: 1rem; font-weight: 700; margin: 0; }
.card-meta { font-size: 0.85rem; color: #475569; margin: 0; }
.card-date { font-size: 0.78rem; color: #94a3b8; margin: 0; }
.card-descripcion { font-size: 0.82rem; color: #64748b; margin: 0.35rem 0; line-height: 1.35; }
.card-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; flex-wrap: wrap; gap: 0.5rem; }
.card-actions { display: flex; align-items: center; gap: 0.6rem; }
.detail-link { font-size: 0.8rem; font-weight: 600; color: #2563eb; text-decoration: none; }
.status-btn { background: #2563eb; color: #fff; border: none; padding: 0.4rem 0.7rem; border-radius: 7px; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.status-btn:disabled { background: #cbd5e1; cursor: not-allowed; }

.priority-badge, .status-badge { font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 14px; }
.priority-alta { background: #fef2f2; color: #dc2626; }
.priority-media { background: #fff7ed; color: #d97706; }
.priority-baja { background: #f0fdf4; color: #16a34a; }
.status-pendiente { background: #fef3c7; color: #b45309; }
.status-proceso { background: #dbeafe; color: #1d4ed8; }
.status-completada { background: #dcfce7; color: #15803d; }


/* Estilos del Toast flotante */
.toast-alerta {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  background: #fff;
  border-left: 4px solid #dc2626;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
  border-radius: 8px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  z-index: 9999;
  animation: slideInRight 0.3s ease-out;
}
.toast-alerta.error {
  border-left-color: #dc2626;
}
.toast-icon {
  font-size: 1.1rem;
}
.toast-text {
  font-size: 0.85rem;
  font-weight: 600;
  color: #1e293b;
}
@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@media (max-width: 700px) {
  .header-nav { display: none; }
}
</style>