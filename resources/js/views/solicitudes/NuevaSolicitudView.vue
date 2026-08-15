<template>
  <div class="nueva-solicitud-page">
    <header class="page-header">
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
          <p class="header-subtitle">MANTENIMIENTO</p>
        </div>
      </div>

      <nav class="header-nav">
        <router-link to="/dashboard" class="nav-link">Inicio</router-link>
        <router-link to="/dashboard?tab=solicitudes" class="nav-link active">Solicitudes</router-link>
        <a href="#help" @click.prevent="router.push('/dashboard?tab=ayuda')" class="nav-link">Ayuda</a>
      </nav>

      <div class="header-user-wrapper">
        <div class="user-avatar" title="Perfil">{{ userInitial }}</div>
        <button class="logout-btn" @click="cerrarSesion" title="Cerrar sesión">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
          </svg>
          <span class="logout-text">Cerrar sesión</span>
        </button>
      </div>
    </header>

    <main class="page-main">
      <h2 class="page-title">Nueva Solicitud de Servicio</h2>
      <p class="page-subtitle">Complete los detalles para reportar una falla o requerimiento técnico.</p>

      <form class="solicitud-form" @submit.prevent="enviarSolicitud">
        <div class="field-group">
          <label>¿Qué tipo de servicio requiere?</label>
          <div class="select-wrapper">
            <span class="field-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.7 6.3a3 3 0 1 0-4.2 4.2L4 17v3h3l6.5-6.5a3 3 0 0 0 4.2-4.2l-2-2-1 1Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </span>
            <select v-model="form.tipoServicio" required>
              <option value="" disabled>Seleccione el tipo de servicio</option>
              <option v-for="tipo in tiposServicio" :key="tipo" :value="tipo">{{ tipo }}</option>
            </select>
          </div>
        </div>

        <div class="field-group">
          <label>¿Dónde se encuentra el problema?</label>
          <div class="input-wrapper">
            <span class="field-icon">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Z" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="2"/>
              </svg>
            </span>
            <input
              v-model="form.ubicacion"
              type="text"
              placeholder="Piso, ala, número de habitación o área"
              required
            />
          </div>
        </div>

        <div class="field-group">
          <label>Descripción detallada del problema</label>
          <textarea
            v-model="form.descripcion"
            rows="4"
            placeholder="Describa brevemente la falla detectada o el requerimiento específico..."
            required
          ></textarea>
        </div>

        <div class="field-group">
          <label>Adjuntar Evidencia Fotográfica (Opcional)</label>
          <div 
            class="upload-box" 
            :class="{ 'dragging': isDragging, 'has-file': form.archivo }" 
            @click="triggerFileInput"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
          >
            <input 
              type="file" 
              ref="fileInput" 
              class="hidden-file-input" 
              accept="image/png,image/jpeg" 
              @change="onFileChange" 
            />
            
            <span class="upload-icon" :class="{ 'text-emerald-500': form.archivo }">
              <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="6" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                <circle cx="8.5" cy="11.5" r="1.5" stroke="currentColor" stroke-width="2"/>
                <path d="M21 16l-4.5-4.5a2 2 0 0 0-2.8 0L7 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            
            <div v-if="!form.archivo">
              <p class="upload-text"><span class="upload-link">Subir un archivo</span> o arrastrar y soltar</p>
              <p class="upload-hint">PNG, JPG hasta 10MB</p>
            </div>
            <div v-else class="file-info-container">
              <p class="file-name-text">{{ form.archivo.name }}</p>
              <p class="file-size-text">({{ formatSize(form.archivo.size) }})</p>
              <button type="button" class="remove-file-btn" @click.stop="removeFile">Quitar foto</button>
            </div>
          </div>
        </div>

        <p v-if="error" class="form-error">{{ error }}</p>
        <p v-if="exito" class="form-success">{{ exito }}</p>

        <button type="submit" class="submit-btn" :disabled="enviando">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          {{ enviando ? 'Enviando...' : 'Enviar Solicitud' }}
        </button>
        <p class="submit-note">Esta solicitud será asignada automáticamente al equipo técnico de guardia.</p>
      </form>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';

const router = useRouter();
const authStore = useAuthStore();

async function cerrarSesion() {
  await authStore.logout();
  router.push('/login');
}

const tiposServicio = ['Eléctrico', 'Plomería', 'Aire Acondicionado', 'Equipo Médico', 'Limpieza', 'Otro'];

const form = ref({
  tipoServicio: '',
  ubicacion: '',
  descripcion: '',
  archivo: null,
});

const isDragging = ref(false);
const fileInput = ref(null);

const enviando = ref(false);
const error = ref('');
const exito = ref('');

const userInitial = computed(() => {
  const nombre = authStore.user?.name ?? 'U';
  return nombre.charAt(0).toUpperCase();
});

function triggerFileInput() {
  if (fileInput.value) {
    fileInput.value.click();
  }
}

function onFileChange(e) {
  const files = e.target.files;
  if (files && files.length > 0) {
    validarYAsignarArchivo(files[0]);
  }
}

function onDragOver() {
  isDragging.value = true;
}

function onDragLeave() {
  isDragging.value = false;
}

function onDrop(e) {
  isDragging.value = false;
  const files = e.dataTransfer.files;
  if (files && files.length > 0) {
    validarYAsignarArchivo(files[0]);
  }
}

function validarYAsignarArchivo(file) {
  error.value = '';
  // Validar formato (solo imágenes)
  if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
    error.value = 'El archivo de evidencia debe ser una imagen PNG o JPG.';
    return;
  }
  // Validar tamaño (máx 5MB)
  if (file.size > 5 * 1024 * 1024) {
    error.value = 'La imagen de evidencia supera el límite permitido de 5MB.';
    return;
  }
  form.value.archivo = file;
}

function removeFile() {
  form.value.archivo = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

function formatSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

async function enviarSolicitud() {
  // Validaciones de cliente antes de llamar al backend
  if (!form.value.tipoServicio) {
    error.value = 'Por favor seleccione el tipo de servicio requerido.';
    return;
  }
  if (!form.value.ubicacion || !form.value.ubicacion.trim()) {
    error.value = 'Por favor indique la ubicación del problema.';
    return;
  }
  if (!form.value.descripcion || !form.value.descripcion.trim()) {
    error.value = 'Por favor proporcione una descripción del problema.';
    return;
  }

  enviando.value = true;
  error.value = '';
  exito.value = '';

  try {
    await solicitudesService.crear({
      titulo: form.value.tipoServicio,
      subtitulo: form.value.descripcion,
      ubicacion: form.value.ubicacion,
      archivo: form.value.archivo,
      fecha: new Date().toISOString().slice(0, 10),
      prioridad: 'Media',
      estado: 'Pendiente',
    });

    exito.value = 'Solicitud enviada correctamente.';
    setTimeout(() => router.push('/dashboard'), 1200);
  } catch (e) {
    // Extraer mensaje descriptivo del backend si existe
    const backendMsg = e?.response?.data?.message || e?.response?.data?.mensaje;
    const backendErrors = e?.response?.data?.errors;
    if (backendErrors) {
      const firstError = Object.values(backendErrors)[0];
      error.value = Array.isArray(firstError) ? firstError[0] : String(firstError);
    } else if (backendMsg) {
      error.value = backendMsg;
    } else {
      error.value = 'No se pudo enviar la solicitud. Intenta de nuevo.';
    }
  } finally {
    enviando.value = false;
  }
}
</script>

<style scoped>
.nueva-solicitud-page {
  min-height: 100vh;
  background: #f4f6fa;
  font-family: system-ui, -apple-system, sans-serif;
  color: #1e293b;
}

.page-header {
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
.header-logo {
  width: 36px; height: 36px; border-radius: 9px; background: #2563eb;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.header-logo svg { width: 18px; height: 18px; }
.header-title { font-size: 0.95rem; font-weight: 700; margin: 0; }
.header-subtitle { font-size: 0.68rem; font-weight: 700; color: #2563eb; margin: 0; letter-spacing: 0.05em; }

.header-nav { display: flex; gap: 1.5rem; margin-left: auto; margin-right: auto; }
.nav-link { color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
.nav-link.active { color: #2563eb; }

.header-user-wrapper { display: flex; align-items: center; gap: 0.75rem; }
.logout-btn {
  display: flex; align-items: center; gap: 0.35rem; background: #fee2e2; color: #ef4444;
  border: none; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700;
  cursor: pointer; transition: all 0.2s ease;
}
.logout-btn:hover { background: #fecaca; color: #dc2626; }
.logout-btn svg { width: 14px; height: 14px; }

.user-avatar {
  width: 34px; height: 34px; border-radius: 50%; background: #2563eb;
  color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;
}

.page-main { max-width: 700px; margin: 0 auto; padding: 2.5rem 1.5rem; }
.page-title { font-size: 1.7rem; font-weight: 800; margin: 0 0 0.4rem; }
.page-subtitle { color: #64748b; margin: 0 0 1.5rem; }

.solicitud-form {
  background: #fff; border-radius: 14px; padding: 1.75rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 1.25rem;
}

.field-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; }

.input-wrapper, .select-wrapper { position: relative; display: flex; align-items: center; }
.field-icon { position: absolute; left: 0.85rem; color: #94a3b8; display: flex; }
.field-icon svg { width: 18px; height: 18px; }

.input-wrapper input, .select-wrapper select {
  width: 100%; padding: 0.75rem 0.9rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem;
  background: #f8fafc; box-sizing: border-box; appearance: none;
}
.select-wrapper select { cursor: pointer; }
.input-wrapper input:focus, .select-wrapper select:focus { outline: none; border-color: #2563eb; background: #fff; }

textarea {
  width: 100%; padding: 0.75rem 0.9rem; border: 1px solid #e2e8f0;
  border-radius: 10px; font-size: 0.95rem; background: #f8fafc;
  box-sizing: border-box; font-family: inherit; resize: vertical;
}
textarea:focus { outline: none; border-color: #2563eb; background: #fff; }

.upload-box {
  border: 2px dashed #cbd5e1; border-radius: 12px; padding: 1.75rem;
  text-align: center; background: #f8fafc; cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.upload-box:hover {
  border-color: #2563eb; background: #eff6ff/40;
}
.upload-box.dragging {
  border-color: #2563eb; background: #dbeafe;
}
.upload-box.has-file {
  border-color: #10b981; background: #f0fdf4;
}
.hidden-file-input {
  display: none;
}
.upload-icon { color: #94a3b8; display: inline-flex; margin-bottom: 0.5rem; }
.upload-icon svg { width: 28px; height: 28px; }
.upload-text { margin: 0; font-size: 0.9rem; color: #475569; }
.upload-link { color: #2563eb; font-weight: 600; }
.upload-hint { margin: 0.25rem 0 0; font-size: 0.78rem; color: #94a3b8; }

.file-info-container {
  display: flex; flex-direction: column; align-items: center; gap: 0.25rem;
}
.file-name-text {
  font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0;
}
.file-size-text {
  font-size: 0.75rem; color: #64748b; margin: 0;
}
.remove-file-btn {
  margin-top: 0.5rem; background: #fee2e2; color: #ef4444; border: none;
  font-size: 0.75rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 6px;
  cursor: pointer; transition: all 0.2s ease;
}
.remove-file-btn:hover {
  background: #fecaca; color: #dc2626;
}

.form-error { background: #fef2f2; color: #dc2626; font-size: 0.85rem; padding: 0.6rem 0.8rem; border-radius: 8px; margin: 0; }
.form-success { background: #f0fdf4; color: #16a34a; font-size: 0.85rem; padding: 0.6rem 0.8rem; border-radius: 8px; margin: 0; }

.submit-btn {
  display: flex; align-items: center; justify-content: center; gap: 0.5rem;
  background: #2563eb; color: #fff; border: none; padding: 0.85rem;
  border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer;
}
.submit-btn:hover { background: #1d4ed8; }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.submit-btn svg { width: 18px; height: 18px; }
.submit-note { text-align: center; font-size: 0.8rem; color: #94a3b8; margin: 0; }

@media (max-width: 768px) {
  .header-nav { display: none; }
}
</style>