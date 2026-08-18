<template>
  <div class="detalle-page">
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
      <div class="header-actions">
        <router-link :to="esCoordinadorOAdmin ? '/admin' : '/dashboard'" class="back-link">← Volver al Panel</router-link>
      </div>
    </header>

    <main class="page-main">
      <p v-if="loading" class="state-msg">Cargando solicitud...</p>
      <p v-else-if="error" class="state-msg state-error">{{ error }}</p>

      <template v-else>
        <p class="breadcrumbs">Inicio › {{ esCoordinadorOAdmin ? 'Coordinación' : 'Mis Solicitudes' }} › Solicitud #SOL-{{ abreviarId(solicitud.id) }}</p>

        <div class="title-row">
          <h2 class="page-title">{{ solicitud.titulo }}</h2>
        </div>

        <div class="tag-row">
          <span class="priority-badge" :class="prioridadClase(solicitud.prioridad)">PRIORIDAD: {{ (solicitud.prioridad || '').toUpperCase() }}</span>
          <span class="status-badge" :class="estadoClase(solicitud.estado)">ESTADO: {{ (solicitud.estado || '').toUpperCase() }}</span>
        </div>

        <div class="content-grid">
          <div class="main-column">
            <!-- Código de Validación Física (OTP) -->
            <section v-if="solicitud.codigo_verificacion" class="info-card otp-card" style="border: 1px solid #dcfce7; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);">
              <h3 class="flex items-center gap-2 text-emerald-600 font-bold" style="margin: 0 0 0.5rem 0; color: #059669; font-size: 0.95rem;">
                <span class="card-icon text-emerald-500" style="display: inline-flex; align-items: center;">
                  <svg class="h-5 w-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </span>
                Código de Validación Física (OTP)
              </h3>
              <p class="info-description" style="margin-bottom: 1rem; color: #047857; font-size: 0.85rem;">
                Proporcione este código de 6 caracteres al técnico únicamente cuando el trabajo haya sido finalizado a su entera conformidad.
              </p>
              <div style="display: flex; items-center: center; gap: 1rem; flex-wrap: wrap; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.85rem 1.25rem; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                  <div v-for="(char, idx) in (solicitud.codigo_verificacion || '').split('')" :key="idx"
                    style="display: flex; width: 2.25rem; height: 2.75rem; align-items: center; justify-content: center; border-radius: 8px; background: #ecfdf5; font-size: 1.15rem; font-weight: 800; color: #065f46; border: 1px solid #a7f3d0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-transform: uppercase;">
                    {{ char }}
                  </div>
                </div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                  Estado: <span :style="{ color: solicitud.estado === 'Completada' ? '#059669' : '#d97706', fontWeight: '800' }">{{ solicitud.estado === 'Completada' ? 'Validado y Cerrado' : 'Pendiente de Validación' }}</span>
                </div>
              </div>
            </section>

            <section class="info-card">
              <div class="card-header-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 class="flex items-center gap-2" style="margin: 0;">
                  <span class="card-icon text-blue-500">
                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </span>
                  Descripción de la Solicitud
                </h3>
                <button 
                  v-if="!esCoordinadorOAdmin && solicitud.estado === 'Pendiente'" 
                  class="edit-solicitud-btn" 
                  @click="abrirModalEdicion"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                  Editar Solicitud
                </button>
              </div>
              <div class="info-grid">
                <div>
                  <p class="info-label">UBICACIÓN</p>
                  <p class="info-value">{{ solicitud.ubicacion || '—' }}</p>
                </div>
                <div>
                  <p class="info-label">DEPARTAMENTO</p>
                  <p class="info-value">{{ solicitud.departamento || '—' }}</p>
                </div>
              </div>
              <p class="info-label">DESCRIPCIÓN</p>
              <p class="info-description">
                {{ solicitud.descripcion || 'Esta solicitud no tiene una descripción registrada.' }}
              </p>
            </section>

            <section v-if="esCoordinadorOAdmin" class="info-card">
              <h3 class="flex items-center gap-2">
                <span class="card-icon text-slate-500">
                  <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>
                </span>
                Reasignar Técnico
              </h3>
              <p class="info-description">
                Cambia el técnico responsable de esta solicitud. El cambio queda registrado
                automáticamente en el historial, indicando quién lo hizo y cuándo.
              </p>

              <div class="reasignar-row">
                <select v-model="tecnicoSeleccionado" class="tecnico-select-lg">
                  <option :value="null">Sin asignar</option>
                  <option v-for="t in tecnicos" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
                <button
                  class="guardar-tecnico-btn"
                  :disabled="!cambioTecnicoPendiente || guardandoTecnico"
                  @click="guardarTecnico"
                >
                  {{ guardandoTecnico ? 'Guardando...' : 'Guardar' }}
                </button>
              </div>
              <p v-if="errorTecnico" class="upload-error">{{ errorTecnico }}</p>
              <p v-if="exitoTecnico" class="exito-msg">Técnico reasignado correctamente.</p>
            </section>

            <section class="info-card">
              <h3 class="flex items-center gap-2">
                <span class="card-icon text-slate-500">
                  <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </span>
                Historial de la Solicitud
              </h3>

              <p v-if="cargandoHistorial" class="state-msg-inline">Cargando historial...</p>
              <p v-else-if="errorHistorial" class="state-msg-inline state-error">{{ errorHistorial }}</p>
              <p v-else-if="historial.length === 0" class="state-msg-inline">
                Todavía no hay cambios registrados para esta solicitud.
              </p>

              <div class="timeline" v-else>
                <div class="timeline-item" v-for="item in historial" :key="item.id">
                  <span class="timeline-dot"></span>
                  <div class="timeline-content">
                    <div class="timeline-head">
                      <strong>{{ descripcionCambio(item) }}</strong>
                      <span class="timeline-time">{{ formatFechaHora(item.created_at) }}</span>
                    </div>
                    <p>{{ item.user ? item.user.name : 'Sistema' }}</p>
                  </div>
                </div>
              </div>
            </section>

            <section class="info-card">
              <h3 class="flex items-center gap-2">
                <span class="card-icon text-slate-500">
                  <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><circle cx="12" cy="13" r="3" stroke="currentColor" stroke-width="2" /></svg>
                </span>
                Evidencia de la Solicitud
              </h3>

              <p v-if="cargandoEvidencias" class="state-msg-inline">Cargando evidencia...</p>
              <p v-else-if="errorEvidencias" class="state-msg-inline state-error">{{ errorEvidencias }}</p>
              <p v-else-if="evidencias.length === 0" class="state-msg-inline">
                Todavía no se ha subido evidencia para esta solicitud.
              </p>

              <div class="evidencia-grid" v-else>
                <a
                  v-for="ev in evidencias"
                  :key="ev.id"
                  :href="urlEvidencia(ev)"
                  target="_blank"
                  class="evidencia-item"
                >
                  <img v-if="esImagen(ev)" :src="urlEvidencia(ev)" :alt="ev.nombre_archivo" class="evidencia-thumb" />
                  <div v-else class="evidencia-doc" style="display: flex; align-items: center; justify-content: center; background-color: #f1f5f9; color: #64748b; width: 3rem; height: 3rem; border-radius: 0.5rem;">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div class="evidencia-info">
                    <p class="evidencia-nombre">{{ ev.nombre_archivo }}</p>
                    <p class="evidencia-meta">{{ ev.user ? ev.user.name : 'Sistema' }} · {{ formatFechaHora(ev.created_at) }}</p>
                  </div>
                </a>
              </div>
            </section>
          </div>
        </div>
      </template>
    </main>

    <!-- Modal de Edición de Solicitud (Exclusivo Solicitante) -->
    <div v-if="mostrandoModalEdicion" class="modal-overlay">
      <div class="modal-container">
        <div class="modal-header">
          <h3>Editar Solicitud</h3>
          <button class="close-btn" @click="mostrandoModalEdicion = false">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Título / Servicio</label>
            <input type="text" v-model="editForm.titulo" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea v-model="editForm.descripcion" class="form-textarea" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Prioridad</label>
            <select v-model="editForm.prioridad" class="form-select">
              <option value="Baja">Baja</option>
              <option value="Media">Media</option>
              <option value="Alta">Alta</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Evidencias Adjuntas</label>
            <div v-if="evidencias.length > 0" class="mini-evidencia-list">
              <div v-for="ev in evidencias" :key="ev.id" class="mini-evidencia-item">
                <img v-if="esImagen(ev)" :src="urlEvidencia(ev)" class="mini-evidencia-thumb" />
                <span v-else class="mini-evidencia-icon">
                  <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                </span>
                <span class="mini-evidencia-name">{{ ev.nombre_archivo }}</span>
              </div>
            </div>
            <p v-else class="text-xs text-slate-400">No hay evidencias previas subidas.</p>
          </div>

          <div class="form-group">
            <label class="form-label">Adjuntar Nueva Imagen / Documento</label>
            <input type="file" ref="fileInputEdicion" @change="onArchivoEdicionSeleccionado" accept="image/*,application/pdf" class="form-file-input" />
            <p class="form-help-text">Puedes subir imágenes o archivos PDF adicionales (Máx. 5MB).</p>
            <div v-if="previewUrlEdicion" class="image-preview-container">
              <img :src="previewUrlEdicion" class="image-preview" />
              <button type="button" @click="removerArchivoSeleccionado" class="remove-preview-btn">&times;</button>
            </div>
          </div>
          <p v-if="errorEdicion" class="error-msg">{{ errorEdicion }}</p>
        </div>
        <div class="modal-footer">
          <button class="btn-secundario" @click="mostrandoModalEdicion = false">Cancelar</button>
          <button class="btn-primario" :disabled="guardandoEdicion" @click="guardarEdicion">
            {{ guardandoEdicion ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import solicitudesService from '../../services/solicitudesService';
import tecnicosService from '../../services/tecnicosService';
import evidenciasService from '../../services/evidenciasService';
import authService from '../../services/authService';

const route = useRoute();
const router = useRouter();

const solicitud = ref({});
const loading = ref(true);
const error = ref('');

const esCoordinadorOAdmin = computed(() => {
  const user = authService.getUser();
  if (!user) return false;
  const r = user.rol ? user.rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '') : '';
  return r === 'coordinador' || r === 'administrador' || r === 'admin';
});

const mostrandoModalEdicion = ref(false);
const guardandoEdicion = ref(false);
const errorEdicion = ref('');
const editForm = ref({
  titulo: '',
  descripcion: '',
  prioridad: ''
});

const nuevoArchivoEdicion = ref(null);
const previewUrlEdicion = ref(null);
const fileInputEdicion = ref(null);

function onArchivoEdicionSeleccionado(event) {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 5 * 1024 * 1024) {
    errorEdicion.value = 'El archivo supera el tamaño máximo permitido de 5MB.';
    nuevoArchivoEdicion.value = null;
    previewUrlEdicion.value = null;
    if (fileInputEdicion.value) fileInputEdicion.value.value = '';
    return;
  }

  nuevoArchivoEdicion.value = file;
  errorEdicion.value = '';

  if (file.type.startsWith('image/')) {
    previewUrlEdicion.value = URL.createObjectURL(file);
  } else {
    previewUrlEdicion.value = null;
  }
}

function removerArchivoSeleccionado() {
  nuevoArchivoEdicion.value = null;
  previewUrlEdicion.value = null;
  if (fileInputEdicion.value) fileInputEdicion.value.value = '';
}

function abrirModalEdicion() {
  editForm.value = {
    titulo: solicitud.value.titulo,
    descripcion: solicitud.value.descripcion || '',
    prioridad: solicitud.value.prioridad || 'Media'
  };
  errorEdicion.value = '';
  nuevoArchivoEdicion.value = null;
  previewUrlEdicion.value = null;
  if (fileInputEdicion.value) fileInputEdicion.value.value = '';
  mostrandoModalEdicion.value = true;
}

async function guardarEdicion() {
  guardandoEdicion.value = true;
  errorEdicion.value = '';
  try {
    const res = await solicitudesService.actualizar(solicitud.value.id, {
      titulo: editForm.value.titulo,
      descripcion: editForm.value.descripcion,
      prioridad: editForm.value.prioridad
    });
    solicitud.value = res;

    // Si seleccionó un archivo extra para adjuntar
    if (nuevoArchivoEdicion.value) {
      await solicitudesService.subirEvidencia(solicitud.value.id, nuevoArchivoEdicion.value);
      await cargarEvidencias();
    }

    mostrandoModalEdicion.value = false;
    await cargarHistorial();
  } catch (e) {
    errorEdicion.value = 'No se pudo actualizar la solicitud. Intenta de nuevo.';
  } finally {
    guardandoEdicion.value = false;
  }
}

const historial = ref([]);
const cargandoHistorial = ref(true);
const errorHistorial = ref('');
const tecnicos = ref([]);

const evidencias = ref([]);
const cargandoEvidencias = ref(true);
const errorEvidencias = ref('');

const tecnicoSeleccionado = ref(null);
const guardandoTecnico = ref(false);
const errorTecnico = ref('');
const exitoTecnico = ref(false);

const cambioTecnicoPendiente = computed(() => {
  return tecnicoSeleccionado.value !== (solicitud.value.tecnico_id ?? null);
});

const NOMBRES_CAMPO = {
  estado: 'Estado',
  prioridad: 'Prioridad',
  tecnico_id: 'Técnico asignado',
};

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

function formatFechaHora(fecha) {
  if (!fecha) return '';
  const date = new Date(fecha);
  return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) +
    ' ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

function nombreTecnico(id) {
  if (!id) return 'Sin asignar';
  const tecnico = tecnicos.value.find((t) => String(t.id) === String(id));
  return tecnico ? tecnico.name : `Técnico #${id}`;
}

function valorLegible(campo, valor) {
  if (campo === 'tecnico_id') return nombreTecnico(valor);
  return valor || '—';
}

function descripcionCambio(item) {
  const nombreCampo = NOMBRES_CAMPO[item.campo] || item.campo;
  const anterior = valorLegible(item.campo, item.valor_anterior);
  const nuevo = valorLegible(item.campo, item.valor_nuevo);
  return `${nombreCampo}: ${anterior} → ${nuevo}`;
}

function urlEvidencia(ev) {
  return `/storage/${ev.ruta}`;
}

function esImagen(ev) {
  return (ev.tipo || '').startsWith('image/');
}

async function cargarSolicitud() {
  loading.value = true;
  error.value = '';
  try {
    solicitud.value = await solicitudesService.obtener(route.params.id);
    tecnicoSeleccionado.value = solicitud.value.tecnico_id ?? null;
  } catch (e) {
    error.value = 'No se pudo cargar la solicitud.';
  } finally {
    loading.value = false;
  }
}

async function cargarHistorial() {
  cargandoHistorial.value = true;
  errorHistorial.value = '';
  try {
    historial.value = await solicitudesService.historial(route.params.id);
  } catch (e) {
    errorHistorial.value = 'No se pudo cargar el historial.';
  } finally {
    cargandoHistorial.value = false;
  }
}

async function cargarEvidencias() {
  cargandoEvidencias.value = true;
  errorEvidencias.value = '';
  try {
    evidencias.value = await evidenciasService.listar(route.params.id);
  } catch (e) {
    errorEvidencias.value = 'No se pudo cargar la evidencia.';
  } finally {
    cargandoEvidencias.value = false;
  }
}

async function cargarTecnicos() {
  try {
    tecnicos.value = await tecnicosService.listar();
  } catch (e) {
    console.error('No se pudieron cargar los técnicos', e);
  }
}

async function guardarTecnico() {
  guardandoTecnico.value = true;
  errorTecnico.value = '';
  exitoTecnico.value = false;
  try {
    await solicitudesService.actualizar(route.params.id, {
      tecnico_id: tecnicoSeleccionado.value,
    });
    exitoTecnico.value = true;
    await cargarSolicitud();
    await cargarHistorial();
    setTimeout(() => { exitoTecnico.value = false; }, 3000);
  } catch (e) {
    console.error('Error al guardar técnico:', e);
    errorTecnico.value = 'No se pudo guardar el cambio de técnico.';
  } finally {
    guardandoTecnico.value = false;
  }
}

onMounted(() => {
  cargarSolicitud();
  cargarHistorial();
  cargarEvidencias();
  cargarTecnicos();
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
</script>

<style scoped>
.detalle-page {
  min-height: 100vh;
  background: #f4f6fa;
  font-family: system-ui, -apple-system, sans-serif;
  color: #1e293b;
}

.page-header {
  display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
  background: #fff; padding: 0.85rem 1.5rem; border-bottom: 1px solid #e5e9f0;
  flex-wrap: wrap;
}
.header-brand { display: flex; align-items: center; gap: 0.6rem; }
.header-logo { width: 32px; height: 32px; border-radius: 8px; background: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.header-logo svg { width: 18px; height: 18px; }
.header-title { font-size: 0.95rem; font-weight: 700; margin: 0; }
.back-link { color: #2563eb; text-decoration: none; font-size: 0.85rem; font-weight: 600; }

.page-main { max-width: 1100px; margin: 0 auto; padding: 1.5rem; }
.state-msg { color: #64748b; padding: 2rem 0; }
.state-error { color: #dc2626; }
.state-msg-inline { color: #94a3b8; font-size: 0.85rem; margin: 0; }

.breadcrumbs { font-size: 0.8rem; color: #94a3b8; margin: 0 0 0.75rem; }

.title-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.4rem; font-weight: 800; margin: 0; }

.tag-row { display: flex; gap: 0.6rem; margin: 0.75rem 0 1.5rem; }
.priority-badge, .status-badge { font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 14px; }
.priority-alta { background: #fef2f2; color: #dc2626; }
.priority-media { background: #fff7ed; color: #d97706; }
.priority-baja { background: #f0fdf4; color: #16a34a; }
.status-pendiente { background: #fef3c7; color: #b45309; }
.status-proceso { background: #dbeafe; color: #1d4ed8; }
.status-completada { background: #dcfce7; color: #15803d; }

.content-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; align-items: start; }
.main-column { display: flex; flex-direction: column; gap: 1.25rem; }

.info-card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.info-card h3 { font-size: 0.95rem; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.4rem; }
.card-icon { font-size: 0.9rem; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.info-label { font-size: 0.72rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.03em; margin: 0 0 0.2rem; }
.info-value { font-size: 0.9rem; font-weight: 600; margin: 0; }
.info-description { font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5; }

.reasignar-row { display: flex; gap: 0.6rem; margin-top: 1rem; flex-wrap: wrap; }
.tecnico-select-lg { flex: 1; min-width: 180px; padding: 0.55rem 0.7rem; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.88rem; }
.guardar-tecnico-btn { background: #2563eb; color: #fff; border: none; padding: 0.55rem 1.1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
.guardar-tecnico-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
.upload-error { color: #dc2626; font-size: 0.8rem; margin: 0.6rem 0 0; }
.exito-msg { color: #16a34a; font-size: 0.8rem; margin: 0.6rem 0 0; font-weight: 600; }

.timeline { display: flex; flex-direction: column; gap: 1rem; }
.timeline-item { display: flex; gap: 0.75rem; }
.timeline-dot { width: 9px; height: 9px; border-radius: 50%; background: #2563eb; margin-top: 0.4rem; flex-shrink: 0; }
.timeline-content { flex: 1; }
.timeline-head { display: flex; justify-content: space-between; gap: 0.75rem; font-size: 0.88rem; margin-bottom: 0.15rem; flex-wrap: wrap; }
.timeline-time { color: #94a3b8; font-size: 0.78rem; font-weight: 500; white-space: nowrap; }
.timeline-content p { margin: 0; font-size: 0.83rem; color: #64748b; line-height: 1.4; }

.evidencia-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.75rem; }
.evidencia-item { display: flex; flex-direction: column; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; text-decoration: none; color: inherit; background: #fff; }
.evidencia-thumb { width: 100%; height: 100px; object-fit: cover; display: block; }
.evidencia-doc { width: 100%; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2rem; background: #f1f5f9; }
.evidencia-info { padding: 0.5rem 0.6rem; }
.evidencia-nombre { font-size: 0.78rem; font-weight: 600; margin: 0 0 0.15rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.evidencia-meta { font-size: 0.7rem; color: #94a3b8; margin: 0; }

/* Estilos de Botón y Modal de Edición */
.edit-solicitud-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  background-color: #f1f5f9;
  color: #2563eb;
  border: 1px solid #e2e8f0;
  padding: 0.4rem 0.8rem;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}
.edit-solicitud-btn:hover {
  background-color: #e2e8f0;
  border-color: #cbd5e1;
}
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}
.modal-container {
  background-color: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  animation: modalScale 0.2s ease-out;
}
@keyframes modalScale {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  border-radius: 16px 16px 0 0;
}
.modal-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  font-weight: 300;
  cursor: pointer;
  color: #64748b;
}
.modal-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  overflow-y: auto;
  max-height: calc(90vh - 120px);
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.form-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.form-input, .form-textarea, .form-select {
  padding: 0.6rem 0.8rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  font-size: 0.88rem;
  outline: none;
  width: 100%;
}
.form-input:focus, .form-textarea:focus, .form-select:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.error-msg {
  color: #dc2626;
  font-size: 0.8rem;
  margin: 0;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  background-color: #f8fafc;
  border-top: 1px solid #f1f5f9;
  border-radius: 0 0 16px 16px;
}
.btn-primario, .btn-secundario {
  padding: 0.55rem 1.1rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
}
.btn-primario {
  background-color: #2563eb;
  color: #fff;
  border: none;
}
.btn-primario:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
}
.btn-secundario {
  background-color: #fff;
  color: #475569;
  border: 1px solid #e2e8f0;
}
.btn-secundario:hover {
  background-color: #f8fafc;
}

/* Estilos de mini evidencias y previews de imagenes en modal */
.mini-evidencia-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.25rem;
}
.mini-evidencia-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
  padding: 0.3rem 0.5rem;
  border-radius: 6px;
  max-width: 100%;
}
.mini-evidencia-thumb {
  width: 20px;
  height: 20px;
  object-fit: cover;
  border-radius: 4px;
}
.mini-evidencia-icon {
  font-size: 0.85rem;
}
.mini-evidencia-name {
  font-size: 0.75rem;
  color: #475569;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 120px;
}
.form-file-input {
  border: 1px dashed #cbd5e1;
  padding: 0.75rem;
  border-radius: 8px;
  cursor: pointer;
  background-color: #fafbfc;
  width: 100%;
}
.form-help-text {
  font-size: 0.75rem;
  color: #94a3b8;
  margin: 0.2rem 0 0;
}
.image-preview-container {
  position: relative;
  margin-top: 0.75rem;
  width: fit-content;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}
.image-preview {
  max-width: 100%;
  max-height: 120px;
  display: block;
  object-fit: contain;
}
.remove-preview-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: rgba(15, 23, 42, 0.6);
  color: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 700;
  line-height: 1;
}
.remove-preview-btn:hover {
  background-color: rgba(15, 23, 42, 0.85);
}
</style>