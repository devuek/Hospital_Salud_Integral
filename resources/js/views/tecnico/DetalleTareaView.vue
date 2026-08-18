<template>
  <div class="tarea-page">
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

      <div class="header-search">
        <span class="search-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1 0 6.5 6.5a7.5 7.5 0 0 0 10.65 10.65z"/>
          </svg>
        </span>
        <input type="text" placeholder="Buscar activos o tareas..." />
      </div>

      <nav class="header-nav">
        <router-link to="/tareas" class="nav-link active">Tareas</router-link>
      </nav>

      <div class="header-actions">
        <span class="icon-btn text-slate-500 hover:text-slate-700 transition-colors">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        </span>
        <span class="icon-btn text-slate-500 hover:text-slate-700 transition-colors">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" /></svg>
        </span>
        <div class="user-avatar">{{ userInitial }}</div>
      </div>
    </header>

    <main class="page-main">
      <p class="breadcrumbs">Inicio › Tareas Asignadas › #MT-2024-08</p>

      <div class="title-row">
        <h2 class="page-title">Tarea #MT-2024-08: Reparación de Monitor</h2>
        <button class="report-problem-btn" @click="abrirModalReporte">
          <svg xmlns="http://www.w3.org/2000/svg" class="inline-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          </svg>
          Reportar Problema
        </button>
      </div>

      <div class="tag-row">
        <span class="priority-badge" :class="'priority-' + (solicitud.original?.prioridad || 'media').toLowerCase()">
          PRIORIDAD: {{ (solicitud.original?.prioridad || 'MEDIA').toUpperCase() }}
        </span>
        <span class="status-badge" :class="'status-' + (solicitud.original?.estado_solicitud || 'proceso').toLowerCase().replace(' ', '-')">
          ESTADO: {{ (solicitud.original?.estado_solicitud || 'EN PROCESO').toUpperCase() }}
        </span>
      </div>

      <div class="content-grid">
        <div class="main-column">
          <section class="info-card">
            <h3 class="flex items-center gap-2">
              <span class="card-icon text-blue-500">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </span>
              Detalles de la Tarea
            </h3>
            <div class="info-grid">
              <div>
                <p class="info-label">UBICACIÓN</p>
                <p class="info-value">{{ tarea.ubicacion }}</p>
              </div>
              <div>
                <p class="info-label">ACTIVO</p>
                <p class="info-value">{{ tarea.activo }}</p>
              </div>
              <div>
                <p class="info-label">SOLICITANTE</p>
                <p class="info-value">{{ tarea.solicitante }}</p>
              </div>
              <div>
                <p class="info-label">TIPO DE MANTENIMIENTO</p>
                <p class="info-value">{{ tarea.tipo }}</p>
              </div>
            </div>
            <p class="info-label">DESCRIPCIÓN DEL PROBLEMA</p>
            <p class="info-description">{{ tarea.descripcion }}</p>
          </section>

          <section class="info-card">
            <h3 class="flex items-center gap-2">
              <span class="card-icon text-slate-500">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
              </span>
              Bitácora de Trabajo
            </h3>

            <div class="timeline">
              <div class="timeline-item" v-for="(entrada, i) in bitacora" :key="i">
                <span class="timeline-dot"></span>
                <div class="timeline-content">
                  <div class="timeline-head">
                    <strong>{{ entrada.titulo }}</strong>
                    <span class="timeline-time">{{ entrada.fecha }}</span>
                  </div>
                  <p>{{ entrada.texto }}</p>
                </div>
              </div>
            </div>

            <div class="nueva-nota">
              <p class="info-label">NUEVA ENTRADA DE BITÁCORA</p>
              <textarea
                v-model="nuevaNota"
                rows="3"
                placeholder="Escribe aquí los avances o hallazgos del trabajo..."
              ></textarea>
              <button class="add-note-btn" @click="agregarNota" :disabled="!nuevaNota.trim()">
                + Añadir Nota
              </button>
            </div>
          </section>
        </div>

        <div class="side-column">
          <section class="info-card">
            <h3>
              <span class="card-icon">
                <svg class="h-4 w-4 text-blue-600 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </span>
              Evidencia de Finalización
            </h3>

            <label class="upload-box" :class="{ 'upload-box-loading': subiendo }" v-if="!tareaTerminada">
              <input
                type="file"
                accept="image/jpeg,image/png,image/jpg"
                class="upload-input"
                :disabled="subiendo"
                @change="subirEvidencia"
              />
              <span class="upload-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
              </span>
              <p class="upload-text">{{ subiendo ? 'Subiendo...' : 'Subir Foto de Finalización' }}</p>
              <p class="upload-hint">JPG, JPEG o PNG, máx. 5MB</p>
            </label>

            <p v-if="errorEvidencia" class="upload-error">{{ errorEvidencia }}</p>

            <p v-if="cargandoEvidencias" class="state-msg-inline">Cargando evidencia...</p>

            <div class="evidence-preview" v-if="evidencias.length">
              <a
                v-for="ev in evidencias"
                :key="ev.id"
                :href="urlEvidencia(ev)"
                target="_blank"
                class="evidence-item"
              >
                <img v-if="esImagen(ev)" :src="urlEvidencia(ev)" :alt="ev.nombre_archivo" />
                <div v-else class="evidence-doc">
                  <svg class="h-6 w-6 text-slate-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                </div>
              </a>
            </div>
          </section>

          <section class="action-card" v-if="!tareaTerminada">
            <label class="checklist-item">
              <input type="checkbox" v-model="confirmado" />
              <span>Asegúrese de haber documentado todas las refacciones utilizadas antes de finalizar.</span>
            </label>

            <button class="finish-btn" :disabled="!confirmado" @click="finalizarTrabajo">
              <svg class="h-4 w-4 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              Finalizar Trabajo
            </button>
            <p class="finish-note">Al finalizar, se notificará al solicitante.</p>

            <button v-if="!tarea.is_pausada" class="pause-btn" @click="pausarTarea">
              <svg class="h-4 w-4 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Pausar Tarea (Esperando repuestos)
            </button>
            <button v-else class="pause-btn resume-btn" @click="reanudarTarea">
              <svg class="h-4 w-4 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Reanudar Tarea
            </button>
            <button class="cancel-btn" @click="abrirModalCancelar">
              <svg class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Cancelar Tarea
            </button>
          </section>

          <section class="action-card tarea-cerrada-card" v-else>
            <div class="cerrada-badge">
              <svg xmlns="http://www.w3.org/2000/svg" class="cerrada-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Tarea {{ solicitud.original?.estado_solicitud || 'Resuelta' }}</span>
            </div>
            <p class="cerrada-hint">Esta tarea ya fue completada. No se permiten más acciones.</p>
          </section>

          <section class="parts-card">
            <h3 class="flex items-center gap-2">
              <span class="card-icon text-slate-500">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>
              </span>
              Repuestos Utilizados
            </h3>
            <div class="part-item" v-for="(parte, i) in repuestos" :key="i">
              <span>{{ parte.nombre }}</span>
              <span class="part-qty">{{ parte.cantidad }}</span>
            </div>
            <button class="add-part-btn" @click="agregarInsumo">+ Agregar Insumo</button>
          </section>
        </div>
      </div>
    </main>

    <!-- TOAST NOTIFICACIÓN PREMIUM -->
    <transition name="fade">
      <div v-if="toast.show" class="fixed top-5 right-5 z-[100] flex w-full max-w-sm overflow-hidden rounded-2xl border bg-white/95 backdrop-blur-md shadow-2xl transition-all duration-300"
           :class="{
             'border-emerald-100 shadow-emerald-100/20': toast.type === 'success',
             'border-rose-100 shadow-rose-100/20': toast.type === 'error',
             'border-amber-100 shadow-amber-100/20': toast.type === 'warning'
           }">
        <div class="flex items-center gap-3 p-4 w-full">
          <div class="flex-shrink-0">
            <span v-if="toast.type === 'success'" class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            <span v-else-if="toast.type === 'error'" class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </span>
            <span v-else class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
          </div>
          <div class="flex-1 text-left">
            <h4 class="text-xs font-bold text-slate-800">
              {{ toast.type === 'success' ? 'Completado' : toast.type === 'error' ? 'Error' : 'Advertencia' }}
            </h4>
            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ toast.message }}</p>
          </div>
          <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none p-1">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </transition>

    <!-- MODAL PREMIUM: MOTIVO DE PAUSA -->
    <div v-if="showModalPausa" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center gap-2 mb-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
          <h3 class="text-lg font-bold text-slate-800">Pausar Tarea</h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Escriba el motivo de la pausa o las refacciones/insumos necesarios para continuar.</p>
        <div class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Motivo / Requerimiento (mín. 5 caracteres)</label>
            <textarea v-model="motivoPausaTexto" rows="3" required placeholder="Ej. Esperando que el proveedor envíe el conector de alimentación de repuesto..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
          </div>
          <div class="mt-2 flex justify-end gap-2">
            <button type="button" @click="showModalPausa = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="button" @click="confirmarPausarTarea" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">
              Pausar Tarea
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL PREMIUM: AGREGAR INSUMO -->
    <div v-if="showModalInsumo" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center gap-2 mb-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </span>
          <h3 class="text-lg font-bold text-slate-800">Agregar Insumo / Repuesto</h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Ingrese la descripción y cantidad del insumo o repuesto utilizado en la reparación.</p>
        <div class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Nombre del Insumo / Repuesto</label>
            <input type="text" v-model="insumoForm.nombre" placeholder="Ej. Soldadura de plata, Conector DC Hembra..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Cantidad / Medida</label>
            <input type="text" v-model="insumoForm.cantidad" placeholder="Ej. 1x, 5g, 2 pzas..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div class="mt-2 flex justify-end gap-2">
            <button type="button" @click="showModalInsumo = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="button" @click="confirmarAgregarInsumo" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">
              Guardar Repuesto
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL PREMIUM: FINALIZAR TRABAJO -->
    <div v-if="showModalFinalizar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center gap-2 mb-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </span>
          <h3 class="text-lg font-bold text-slate-800">Finalizar Trabajo</h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Para poder solicitar el cierre del ticket, debe ingresar el código físico de 6 caracteres proveído por el solicitante.</p>
        <div class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Código de Verificación Física (6 Caracteres)</label>
            <input type="text" v-model="codigoVerificacionTexto" placeholder="Ej. EFF300" maxlength="6" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-center text-base font-bold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-100 border-emerald-200 bg-emerald-50/20 text-emerald-800"/>
          </div>
          <div class="mt-2 flex justify-end gap-2">
            <button type="button" @click="showModalFinalizar = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="button" @click="confirmarFinalizarTrabajo" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">
              Finalizar y Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: REPORTAR PROBLEMA -->
    <div v-if="showModalReporte" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100">
        <div class="flex items-center gap-2 mb-2">
          <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </span>
          <h3 class="text-base font-bold text-slate-800">Reportar Problema</h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Complete el formulario. El reporte quedará registrado en la bitácora y será visible para el destinatario seleccionado.</p>
        <div class="flex flex-col gap-3">

          <!-- DESTINATARIO -->
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Dirigido a</label>
            <div class="destinatario-grid">
              <button
                type="button"
                v-for="op in destinatarioOpciones"
                :key="op.value"
                @click="reporteForm.destinatario = op.value"
                :class="['destinatario-btn', reporteForm.destinatario === op.value ? 'destinatario-btn--active' : '']"
              >
                <span class="destinatario-icon" v-html="op.icon"></span>
                <span>{{ op.label }}</span>
              </button>
            </div>
          </div>

          <!-- TIPO -->
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Tipo de Problema</label>
            <select v-model="reporteForm.tipo" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-100">
              <option value="">Seleccionar...</option>
              <option value="Falta de materiales">Falta de materiales</option>
              <option value="Equipo dañado adicionalmente">Equipo dañado adicionalmente</option>
              <option value="Problema de acceso al área">Problema de acceso al área</option>
              <option value="Solicitud incorrecta">Solicitud incorrecta</option>
              <option value="Requiere especialista externo">Requiere especialista externo</option>
              <option value="Otro">Otro</option>
            </select>
          </div>

          <!-- DESCRIPCIÓN -->
          <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Descripción del Problema (mín. 10 caracteres)</label>
            <textarea v-model="reporteForm.descripcion" rows="4" placeholder="Describa en detalle el problema encontrado..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-100"></textarea>
          </div>

          <div class="mt-1 flex justify-end gap-2">
            <button type="button" @click="showModalReporte = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50" :disabled="enviandoReporte">
              Cancelar
            </button>
            <button type="button" @click="enviarReporte" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition-colors disabled:opacity-50" :disabled="enviandoReporte">
              {{ enviandoReporte ? 'Enviando...' : 'Enviar Reporte' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: CANCELAR TAREA CON ALERTA DE NOTIFICACIÓN AL COORDINADOR -->
    <div v-if="showModalCancelar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100">
        <div class="flex items-center gap-3 mb-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </span>
          <div>
            <h3 class="text-base font-bold text-slate-800">¿Cancelar asignación de tarea?</h3>
            <p class="text-xs text-slate-400">Acción de cancelación para reasignación</p>
          </div>
        </div>

        <div class="rounded-xl bg-amber-50/80 border border-amber-200/70 p-3.5 mb-4 text-xs text-amber-900 leading-relaxed">
          <p class="font-bold mb-1 flex items-center gap-1 text-amber-900">
            <span>⚠️</span> Notificación al Coordinador:
          </p>
          Al cancelar esta tarea, se enviará automáticamente una <strong>notificación formal al coordinador</strong> indicando que la has cancelado para que pueda ser reasignada a otro técnico disponible.
        </div>

        <div class="mb-4">
          <label class="block text-xs font-bold text-slate-500 mb-1.5">Motivo de Cancelación (Opcional)</label>
          <textarea
            v-model="motivoCancelar"
            rows="3"
            placeholder="Especifique el motivo por el cual no podrá atender esta tarea..."
            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-200"
          ></textarea>
        </div>

        <div class="flex justify-end gap-2.5">
          <button
            type="button"
            @click="showModalCancelar = false"
            class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
            :disabled="cancelandoTarea"
          >
            No, volver
          </button>
          <button
            type="button"
            @click="confirmarCancelarTarea"
            class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition-colors shadow-sm disabled:opacity-50 flex items-center gap-1.5"
            :disabled="cancelandoTarea"
          >
            <span v-if="cancelandoTarea">Cancelando...</span>
            <span v-else>Aceptar y Notificar al Coordinador</span>
          </button>
        </div>
      </div>
    </div>

    <footer class="page-footer">
      © 2024 Hospital Salud Integral - Sistema de Gestión de Mantenimiento Bio-Médico
    </footer>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import evidenciasService from '../../services/evidenciasService';
import solicitudesService from '../../services/solicitudesService';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const userInitial = computed(() => (authStore.user?.name ?? 'U').charAt(0).toUpperCase());

const solicitud = ref({});
const idAsignacion = ref(null);
const cargando = ref(true);
const error = ref('');

const tarea = ref({
  ubicacion: 'Cargando...',
  activo: 'Cargando...',
  solicitante: 'Cargando...',
  tipo: 'Cargando...',
  descripcion: 'Cargando...',
  is_pausada: false,
  motivo_pausa: null,
});

const bitacora = ref([]);
const repuestos = ref([]);
const nuevaNota = ref('');
const confirmado = ref(false);

// Determina si la tarea ya fue terminada/cerrada (no se permiten mas acciones)
const tareaTerminada = computed(() => {
  const estado = solicitud.value.original?.estado_solicitud || '';
  return ['Resuelta', 'Cerrada', 'Validada', 'Completada'].includes(estado);
});

// Modal y estado para Cancelar Tarea con notificación al coordinador
const showModalCancelar = ref(false);
const motivoCancelar = ref('');
const cancelandoTarea = ref(false);

function abrirModalCancelar() {
  motivoCancelar.value = '';
  showModalCancelar.value = true;
}

async function confirmarCancelarTarea() {
  const targetId = idAsignacion.value || route.params.id;
  if (!targetId) {
    showToast('No se encontró el identificador de la asignación.', 'error');
    return;
  }

  cancelandoTarea.value = true;
  try {
    const res = await api.post(`/tecnico/asignaciones/${targetId}/cancelar`, {
      motivo: motivoCancelar.value.trim() || 'Cancelación solicitada por el técnico.'
    });

    showModalCancelar.value = false;
    showToast(res.data?.mensaje || 'Se ha notificado al coordinador y la tarea fue cancelada.', 'success');
    
    setTimeout(() => {
      router.push('/tareas');
    }, 1200);
  } catch (e) {
    console.error('Error al cancelar tarea:', e);
    const msg = e.response?.data?.mensaje || 'No se pudo procesar la cancelación de la tarea.';
    showToast(msg, 'error');
  } finally {
    cancelandoTarea.value = false;
  }
}

// Modal y estado para Reportar Problema
const showModalReporte = ref(false);
const enviandoReporte = ref(false);
const reporteForm = ref({ destinatario: 'Coordinador', tipo: '', descripcion: '' });

const destinatarioOpciones = [
  {
    value: 'Coordinador',
    label: 'Coordinador',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
  },
  {
    value: 'Administrador',
    label: 'Administrador',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'
  },
  {
    value: 'Ambos',
    label: 'Coordinador y Admin',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
  },
];

function abrirModalReporte() {
  reporteForm.value = { destinatario: 'Coordinador', tipo: '', descripcion: '' };
  showModalReporte.value = true;
}

async function enviarReporte() {
  if (!reporteForm.value.destinatario) {
    showToast('Seleccione a quién va dirigido el reporte.', 'warning');
    return;
  }
  if (!reporteForm.value.tipo) {
    showToast('Seleccione el tipo de problema.', 'warning');
    return;
  }
  if (reporteForm.value.descripcion.trim().length < 10) {
    showToast('La descripción debe tener al menos 10 caracteres.', 'warning');
    return;
  }
  if (!idAsignacion.value) {
    showToast('No se encontró la asignación activa.', 'error');
    return;
  }

  enviandoReporte.value = true;
  try {
    const mensajeReporte = `[REPORTE DE PROBLEMA] Para: ${reporteForm.value.destinatario} | Tipo: ${reporteForm.value.tipo} | Descripción: ${reporteForm.value.descripcion.trim()}`;
    await api.post(`/tecnico/asignaciones/${idAsignacion.value}/bitacoras`, {
      descripcion_trabajo: mensajeReporte,
      materiales: null
    });
    showModalReporte.value = false;
    showToast(`Reporte enviado a ${reporteForm.value.destinatario} correctamente.`, 'success');
    await cargarDatos();
  } catch (e) {
    console.error('[DetalleTareaView] Error al enviar reporte:', e);
    showToast('No se pudo enviar el reporte. Intente nuevamente.', 'error');
  } finally {
    enviandoReporte.value = false;
  }
}

// Modales reactivos y Toast
const toast = ref({ show: false, message: '', type: 'success' });
function showToast(message, type = 'success') {
  toast.value.message = message;
  toast.value.type = type;
  toast.value.show = true;
  setTimeout(() => { toast.value.show = false; }, 4000);
}

const showModalPausa = ref(false);
const motivoPausaTexto = ref('');

const showModalInsumo = ref(false);
const insumoForm = ref({ nombre: '', cantidad: '1x' });

const showModalFinalizar = ref(false);
const codigoVerificacionTexto = ref('');

// Evidencia (conectada al backend real)
const evidencias = ref([]);
const cargandoEvidencias = ref(true);
const subiendo = ref(false);
const errorEvidencia = ref('');

function urlEvidencia(ev) {
  return ev.url;
}

function esImagen(ev) {
  return (ev.type || '').startsWith('image/');
}

function formatFechaHora(fechaStr) {
  if (!fechaStr) return '';
  const date = new Date(fechaStr);
  return date.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
}

async function cargarDatos() {
  cargando.value = true;
  error.value = '';
  try {
    let sol = await solicitudesService.obtener(route.params.id);
    const estadoReal = sol.original?.estado_solicitud;
    
    if (['Asignada', 'Abierta', 'Devuelta'].includes(estadoReal)) {
      try {
        console.log('[DetalleTareaView.vue] Tarea con estado de atención pendiente. Iniciando de forma automática...');
        await api.patch(`/tecnico/asignaciones/${sol.id_asignacion}/iniciar`);
        // Recargar el ticket con el nuevo estado 'En Proceso'
        sol = await solicitudesService.obtener(route.params.id);
      } catch (iniciarError) {
        console.error('[DetalleTareaView.vue] Error al iniciar la atención automáticamente:', iniciarError);
      }
    }

    solicitud.value = sol;
    idAsignacion.value = sol.id_asignacion;

    tarea.value = {
      ubicacion: sol.ubicacion || 'No especificada',
      activo: sol.titulo || 'Mantenimiento de Equipo',
      solicitante: sol.original?.solicitante ? `${sol.original.solicitante.nombre} ${sol.original.solicitante.apellido}` : 'No especificado',
      tipo: sol.subtitulo || 'Soporte Técnico',
      descripcion: sol.descripcion || 'Sin descripción.',
      is_pausada: sol.original?.asignacion?.is_pausada || false,
      motivo_pausa: sol.original?.asignacion?.motivo_pausa || null,
    };

    // Cargar bitácoras reales
    if (sol.original?.asignacion?.bitacoras) {
      bitacora.value = sol.original.asignacion.bitacoras.map(b => ({
        titulo: b.descripcion_trabajo.startsWith('Tarea pausada') ? 'Pausado' : (b.descripcion_trabajo.startsWith('Tarea reanudada') ? 'Reanudado' : 'Entrada de Bitácora'),
        fecha: formatFechaHora(b.created_at),
        texto: b.descripcion_trabajo,
      }));

      // Cargar repuestos desde las bitácoras reales de la asignación
      const listaRepuestos = [];
      sol.original.asignacion.bitacoras.forEach(b => {
        if (b.materiales) {
          // El backend puede guardar una lista separada por comas, los mostramos
          const partes = b.materiales.split(', ');
          partes.forEach(p => {
            if (p.trim()) {
              const match = p.match(/(.*)\s\((.*)\)/);
              if (match) {
                listaRepuestos.push({ nombre: match[1].trim(), cantidad: match[2].trim() });
              } else {
                listaRepuestos.push({ nombre: p.trim(), cantidad: '' });
              }
            }
          });
        }
      });
      repuestos.value = listaRepuestos;
    } else {
      bitacora.value = [];
      repuestos.value = [];
    }

  } catch (e) {
    error.value = 'No se pudo cargar el detalle de la tarea.';
  } finally {
    cargando.value = false;
  }
}

async function cargarEvidencias() {
  cargandoEvidencias.value = true;
  try {
    evidencias.value = await evidenciasService.listar(route.params.id);
  } catch (e) {
    console.error('No se pudo cargar la evidencia', e);
  } finally {
    cargandoEvidencias.value = false;
  }
}

async function subirEvidencia(event) {
  const archivo = event.target.files[0];
  if (!archivo) return;

  // Validar formato (solo JPG, PNG, JPEG)
  const tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
  if (!tiposPermitidos.includes(archivo.type)) {
    errorEvidencia.value = 'Sólo se permiten imágenes en formato JPG, JPEG o PNG.';
    event.target.value = '';
    return;
  }

  // Validar tamaño (máx. 5MB)
  if (archivo.size > 5 * 1024 * 1024) {
    errorEvidencia.value = 'El archivo supera el tamaño máximo permitido de 5MB.';
    event.target.value = '';
    return;
  }

  subiendo.value = true;
  errorEvidencia.value = '';
  console.log('[DetalleTareaView.vue] Llamando a subirEvidencia:', {
    solicitudId: route.params.id,
    idAsignacion: idAsignacion.value,
    archivo: archivo.name
  });
  try {
    const nueva = await evidenciasService.subir(route.params.id, archivo, idAsignacion.value);
    evidencias.value.unshift(nueva);
    console.log('[DetalleTareaView.vue] Subida de evidencia completada con éxito.');
  } catch (e) {
    console.error('[DetalleTareaView.vue] Falló la subida de evidencia:', e);
    if (e.response) {
      console.error('[DetalleTareaView.vue] Datos de respuesta de error:', e.response.data);
    }
    errorEvidencia.value = 'No se pudo subir la imagen. Verifica el archivo.';
  } finally {
    subiendo.value = false;
    event.target.value = '';
  }
}

async function agregarNota() {
  if (!nuevaNota.value.trim()) return;
  if (!idAsignacion.value) {
    showToast('Asignación inválida.', 'error');
    return;
  }

  try {
    let materialesStr = null;
    if (repuestos.value.length > 0) {
      materialesStr = repuestos.value.map(r => `${r.nombre} (${r.cantidad})`).join(', ');
    }

    await api.post(`/tecnico/asignaciones/${idAsignacion.value}/bitacoras`, {
      descripcion_trabajo: nuevaNota.value.trim(),
      materiales: materialesStr
    });

    await cargarDatos();
    nuevaNota.value = '';
    showToast('Nota agregada a la bitácora con éxito.', 'success');
  } catch (e) {
    showToast('No se pudo guardar la nota de bitácora.', 'error');
  }
}

function agregarInsumo() {
  insumoForm.value.nombre = '';
  insumoForm.value.cantidad = '1x';
  showModalInsumo.value = true;
}

function confirmarAgregarInsumo() {
  const nombre = insumoForm.value.nombre.trim();
  const cantidad = insumoForm.value.cantidad.trim();
  if (!nombre) {
    showToast('Escriba el nombre del repuesto o insumo.', 'warning');
    return;
  }
  if (!cantidad) {
    showToast('Escriba la cantidad.', 'warning');
    return;
  }

  repuestos.value.push({ nombre, cantidad });
  showModalInsumo.value = false;
  showToast('Insumo agregado a la lista.', 'success');
}

function pausarTarea() {
  motivoPausaTexto.value = '';
  showModalPausa.value = true;
}

async function confirmarPausarTarea() {
  if (!idAsignacion.value) return;
  const motivo = motivoPausaTexto.value.trim();
  if (!motivo) {
    showToast('Debe escribir el motivo de la pausa.', 'warning');
    return;
  }
  if (motivo.length < 5) {
    showToast('El motivo debe tener al menos 5 caracteres.', 'warning');
    return;
  }

  try {
    await solicitudesService.pausarAsignacion(idAsignacion.value, motivo);
    showModalPausa.value = false;
    showToast('La tarea ha sido pausada y el coordinador ha sido notificado.', 'success');
    await cargarDatos();
  } catch (e) {
    showToast('No se pudo pausar la tarea.', 'error');
  }
}

async function reanudarTarea() {
  if (!idAsignacion.value) return;
  try {
    await solicitudesService.reanudarAsignacion(idAsignacion.value);
    showToast('La tarea ha sido reanudada exitosamente.', 'success');
    await cargarDatos();
  } catch (e) {
    showToast('No se pudo reanudar la tarea.', 'error');
  }
}

async function finalizarTrabajo() {
  if (!confirmado.value) return;
  if (!idAsignacion.value) return;

  if (evidencias.value.length === 0) {
    showToast('Debe subir al menos una foto de evidencia de finalización.', 'warning');
    return;
  }

  codigoVerificacionTexto.value = '';
  showModalFinalizar.value = true;
}

async function confirmarFinalizarTrabajo() {
  const codigo = codigoVerificacionTexto.value.trim();
  if (!codigo || codigo.length !== 6) {
    showToast('Debe ingresar un código válido de 6 caracteres.', 'warning');
    return;
  }

  try {
    const response = await api.patch(`/tecnico/asignaciones/${idAsignacion.value}/solicitar-cierre`, {
      codigo_verificacion: codigo.toUpperCase()
    });
    showModalFinalizar.value = false;
    showToast(response.data.mensaje || 'Trabajo finalizado con éxito.', 'success');
    setTimeout(() => {
      router.push('/tareas');
    }, 1500);
  } catch (e) {
    const msg = e.response?.data?.mensaje || 'No se pudo finalizar el trabajo. Verifica el código de verificación.';
    showToast(msg, 'error');
  }
}

onMounted(() => {
  cargarDatos();
  cargarEvidencias();
});
</script>

<style scoped>
.tarea-page {
  min-height: 100vh;
  background: #f4f6fa;
  font-family: system-ui, -apple-system, sans-serif;
  color: #1e293b;
}

.page-header {
  display: flex; align-items: center; gap: 1.5rem;
  background: #fff; padding: 0.85rem 1.5rem; border-bottom: 1px solid #e5e9f0;
  flex-wrap: wrap;
}
.header-brand { display: flex; align-items: center; gap: 0.6rem; }
.header-logo { width: 32px; height: 32px; border-radius: 8px; background: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.header-logo svg { width: 18px; height: 18px; }
.header-title { font-size: 0.95rem; font-weight: 700; margin: 0; }

.header-search { display: flex; align-items: center; gap: 0.4rem; background: #f1f5f9; border-radius: 8px; padding: 0.4rem 0.75rem; flex: 1; max-width: 320px; }
.header-search input { border: none; background: none; outline: none; font-size: 0.85rem; width: 100%; }

.header-nav { display: flex; gap: 1.25rem; margin-left: auto; }
.nav-link { color: #64748b; text-decoration: none; font-size: 0.88rem; font-weight: 600; }
.nav-link.active { color: #2563eb; }

.header-actions { display: flex; align-items: center; gap: 0.75rem; }
.icon-btn { cursor: pointer; color: #64748b; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; }

.page-main { max-width: 1100px; margin: 0 auto; padding: 1.5rem; }
.breadcrumbs { font-size: 0.8rem; color: #94a3b8; margin: 0 0 0.75rem; }

.title-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
.page-title { font-size: 1.4rem; font-weight: 800; margin: 0; }
.report-problem-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  padding: 0.5rem 0.9rem;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
  transition: background 0.15s;
}
.report-problem-btn:hover { background: #fee2e2; }
.inline-icon { width: 14px; height: 14px; flex-shrink: 0; }

.tarea-cerrada-card { text-align: center; padding: 1.5rem 1.25rem !important; }
.cerrada-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
  border-radius: 20px;
  padding: 0.4rem 1rem;
  font-weight: 700;
  font-size: 0.88rem;
  margin-bottom: 0.75rem;
}
.cerrada-icon { width: 18px; height: 18px; }
.cerrada-hint { font-size: 0.78rem; color: #94a3b8; margin: 0; }

/* Destinatario selector */
.destinatario-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
}
.destinatario-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.3rem;
  padding: 0.55rem 0.4rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  background: #f8fafc;
  cursor: pointer;
  transition: all 0.15s;
  text-align: center;
  line-height: 1.2;
}
.destinatario-btn:hover {
  border-color: #94a3b8;
  background: #f1f5f9;
  color: #334155;
}
.destinatario-btn--active {
  border-color: #2563eb !important;
  background: #eff6ff !important;
  color: #1d4ed8 !important;
}
.destinatario-icon { display: flex; align-items: center; justify-content: center; }

.tag-row { display: flex; gap: 0.6rem; margin: 0.75rem 0 1.5rem; }
.priority-badge, .status-badge { font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.7rem; border-radius: 14px; }
.priority-alta { background: #fef2f2; color: #dc2626; }
.status-proceso { background: #dbeafe; color: #1d4ed8; }

.content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 1.25rem; align-items: start; }
.main-column, .side-column { display: flex; flex-direction: column; gap: 1.25rem; }

.info-card, .action-card, .parts-card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.info-card h3, .parts-card h3 { font-size: 0.95rem; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.4rem; }
.card-icon { font-size: 0.9rem; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.info-label { font-size: 0.72rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.03em; margin: 0 0 0.2rem; }
.info-value { font-size: 0.9rem; font-weight: 600; margin: 0; }
.info-description { font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5; }

.timeline { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.25rem; }
.timeline-item { display: flex; gap: 0.75rem; }
.timeline-dot { width: 9px; height: 9px; border-radius: 50%; background: #2563eb; margin-top: 0.4rem; flex-shrink: 0; }
.timeline-content { flex: 1; }
.timeline-head { display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 0.15rem; }
.timeline-time { color: #94a3b8; font-size: 0.78rem; font-weight: 500; }
.timeline-content p { margin: 0; font-size: 0.85rem; color: #64748b; line-height: 1.4; }

.nueva-nota textarea {
  width: 100%; padding: 0.65rem; border: 1px solid #e2e8f0; border-radius: 8px;
  font-family: inherit; font-size: 0.88rem; resize: vertical; box-sizing: border-box; margin-bottom: 0.5rem;
}
.add-note-btn { background: none; border: none; color: #2563eb; font-weight: 700; font-size: 0.85rem; cursor: pointer; padding: 0; }
.add-note-btn:disabled { color: #cbd5e1; cursor: not-allowed; }

.upload-box { position: relative; display: block; border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1.5rem; text-align: center; background: #f8fafc; cursor: pointer; }
.upload-box-loading { opacity: 0.6; cursor: wait; }
.upload-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.upload-icon { font-size: 1.5rem; display: block; margin-bottom: 0.4rem; }
.upload-text { margin: 0; font-size: 0.85rem; font-weight: 600; color: #475569; }
.upload-hint { margin: 0.2rem 0 0; font-size: 0.75rem; color: #94a3b8; }
.upload-error { color: #dc2626; font-size: 0.8rem; margin: 0.5rem 0 0; }
.state-msg-inline { color: #94a3b8; font-size: 0.8rem; margin: 0.5rem 0 0; }

.evidence-preview { display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
.evidence-item { width: 70px; height: 70px; display: block; border-radius: 8px; overflow: hidden; }
.evidence-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
.evidence-doc { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #f1f5f9; }

.checklist-item { display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.83rem; color: #475569; margin-bottom: 1rem; cursor: pointer; }
.checklist-item input { margin-top: 0.2rem; }

.finish-btn {
  width: 100%; background: #2563eb; color: #fff; border: none; padding: 0.8rem;
  border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; margin-bottom: 0.4rem;
}
.finish-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
.finish-note { text-align: center; font-size: 0.75rem; color: #94a3b8; margin: 0 0 1rem; }

.pause-btn, .cancel-btn {
  width: 100%; background: #fff; border: 1px solid #e2e8f0; padding: 0.65rem;
  border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; margin-bottom: 0.5rem; color: #475569;
}
.resume-btn {
  background: #f0fdf4 !important;
  color: #16a34a !important;
  border-color: #bbf7d0 !important;
}
.cancel-btn { color: #dc2626; border-color: #fecaca; }

.parts-card { background: #1e293b; color: #fff; }
.parts-card h3 { color: #fff; }
.part-item { display: flex; justify-content: space-between; font-size: 0.85rem; padding: 0.5rem 0; border-bottom: 1px solid #334155; }
.part-qty { color: #94a3b8; }
.add-part-btn { width: 100%; background: #334155; color: #fff; border: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; margin-top: 0.75rem; }

.page-footer { text-align: center; color: #94a3b8; font-size: 0.8rem; padding: 2rem 0; }

@media (max-width: 900px) {
  .content-grid { grid-template-columns: 1fr; }
  .header-nav, .header-search { display: none; }
}
</style>