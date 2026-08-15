<template>
  <div class="coord-layout">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="brand-logo">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="3" y="7" width="18" height="13" rx="2" stroke="white" stroke-width="2"/>
            <path d="M12 11v4M10 13h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="brand-name">Salud Integral</span>
      </div>

      <nav class="sidebar-nav">
        <p class="nav-section">MENÚ PRINCIPAL</p>
        <button
          @click="seccionActiva = 'solicitudes'"
          class="nav-item"
          :class="{ active: seccionActiva === 'solicitudes' }"
          style="width: 100%; border: none; text-align: left; cursor: pointer; display: flex;"
        >
          <span class="nav-icon">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
          </span>
          Tablero
        </button>
        <button
          @click="seccionActiva = 'solicitudes'"
          class="nav-item"
          :class="{ active: seccionActiva === 'solicitudes' }"
          style="width: 100%; border: none; text-align: left; cursor: pointer; display: flex;"
        >
          <span class="nav-icon">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          </span>
          Solicitudes
        </button>
        <button
          @click="seccionActiva = 'tecnicos'"
          class="nav-item"
          :class="{ active: seccionActiva === 'tecnicos' }"
          style="width: 100%; border: none; text-align: left; cursor: pointer; display: flex;"
        >
          <span class="nav-icon">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>
          </span>
          Técnicos
        </button>
        <button
          @click="seccionActiva = 'reportes'"
          class="nav-item"
          :class="{ active: seccionActiva === 'reportes' }"
          style="width: 100%; border: none; text-align: left; cursor: pointer; display: flex;"
        >
          <span class="nav-icon">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
          </span>
          Reportes
        </button>
        <p class="nav-section">CONFIGURACIÓN</p>
        <button
          @click="seccionActiva = 'ajustes'"
          class="nav-item"
          :class="{ active: seccionActiva === 'ajustes' }"
          style="width: 100%; border: none; text-align: left; cursor: pointer; display: flex;"
        >
          <span class="nav-icon">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" /></svg>
          </span>
          Ajustes
        </button>
      </nav>

      <div class="sidebar-user">
        <div class="user-avatar" title="Cerrar sesión" @click="cerrarSesion">{{ userInitial }}</div>
        <div>
          <p class="user-name">{{ displayName }}</p>
          <p class="user-role">Coordinador General</p>
        </div>
      </div>
    </aside>

    <div class="coord-main">
      <header class="coord-header">
        <div class="header-title-row">
          <h1>Panel de Coordinación</h1>
          <span class="header-tag">Mantenimiento</span>
        </div>
        <div class="header-actions">
          <div class="search-box">
            <span class="search-icon">
              <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input type="text" v-model="busqueda" placeholder="Buscar solicitud..." @input="buscar" />
          </div>
          <!-- Campana de Notificaciones Premium -->
          <div class="relative" style="position: relative;">
            <button @click="toggleNotificaciones" class="icon-btn text-slate-500 hover:text-slate-700 transition-colors relative" style="background: none; border: none; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; justify-content: center; position: relative;">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <!-- Indicador de alerta roja si hay notificaciones pendientes -->
              <span v-if="notificaciones.length > 0" class="absolute top-1 right-1 flex h-2 w-2" style="position: absolute; top: 0.25rem; right: 0.25rem; display: flex; height: 0.5rem; width: 0.5rem;">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75" style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #f87171; opacity: 0.75;"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500" style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.5rem; width: 0.5rem; background-color: #ef4444;"></span>
              </span>
            </button>
            
            <!-- Dropdown de Notificaciones -->
            <div v-show="showNotificaciones" class="absolute right-0 mt-2 z-50 w-80 rounded-2xl border border-slate-100 bg-white py-2 shadow-xl" style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; z-index: 50; width: 20rem; border-radius: 1rem; border: 1px solid #f1f5f9; background-color: #ffffff; padding: 0.5rem 0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
              <div class="flex items-center justify-between border-b border-slate-100 px-4 pb-2 pt-1" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; border-bottom: 1px solid #f1f5f9;">
                <span class="text-xs font-bold text-slate-800" style="font-size: 0.75rem; font-weight: 700; color: #1e293b;">Notificaciones</span>
                <button @click="limpiarNotificaciones" class="text-[10px] font-semibold text-blue-600 hover:underline" style="font-size: 0.65rem; font-weight: 600; color: #2563eb; background: none; border: none; cursor: pointer;">Marcar leídas</button>
              </div>
              <div class="max-h-64 overflow-y-auto" style="max-height: 16rem; overflow-y: auto;">
                <div v-for="n in notificaciones" :key="n.id" @click="clickNotificacion(n)" class="flex gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50/50 last:border-0 transition-colors" style="display: flex; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background-color 0.2s;">
                  <div class="flex-shrink-0 mt-0.5" style="flex-shrink: 0; margin-top: 0.125rem;">
                    <span v-if="n.tipo === 'pendiente'" class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600" style="display: flex; width: 2rem; height: 2rem; align-items: center; justify-content: center; background-color: #eff6ff; color: #2563eb; border-radius: 0.5rem;">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1.125rem; height: 1.125rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                    </span>
                    <span v-else class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600" style="display: flex; width: 2rem; height: 2rem; align-items: center; justify-content: center; background-color: #f0fdf4; color: #16a34a; border-radius: 0.5rem;">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1.125rem; height: 1.125rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </span>
                  </div>
                  <div class="flex flex-col gap-0.5" style="display: flex; flex-direction: column; gap: 0.125rem; text-align: left;">
                    <p class="text-xs text-slate-700 font-medium leading-normal" style="font-size: 0.75rem; color: #334155; margin: 0; font-weight: 500;">{{ n.mensaje }}</p>
                    <span class="text-[10px] text-slate-400 font-medium" style="font-size: 0.625rem; color: #94a3b8;">{{ n.tiempo }}</span>
                  </div>
                </div>
                <div v-if="notificaciones.length === 0" class="py-8 text-center text-xs text-slate-400" style="padding: 2rem 0; text-align: center; font-size: 0.75rem; color: #94a3b8;">
                  No tienes notificaciones pendientes.
                </div>
              </div>
            </div>
          </div>
          <button
            @click="seccionActiva = 'ajustes'"
            class="icon-btn text-slate-500 hover:text-slate-700 transition-colors"
            style="background: none; border: none; cursor: pointer; padding: 0; display: inline-flex;"
            title="Ajustes de Perfil"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
          </button>
        </div>
      </header>

      <main class="coord-content">
        <!-- SECCIÓN DE SOLICITUDES / TABLERO -->
        <template v-if="seccionActiva === 'solicitudes'">
          <section class="metrics-row">
            <div class="metric-card">
              <div class="metric-top">
                <span class="metric-icon icon-amber">
                  <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </span>
                <span class="metric-delta" :class="resumen.delta_pendientes >= 0 ? 'delta-up' : 'delta-down'">
                  {{ resumen.delta_pendientes >= 0 ? '+' : '' }}{{ resumen.delta_pendientes }}% hoy
                </span>
              </div>
              <p class="metric-label">Pendientes Hoy</p>
              <p class="metric-value">{{ resumen.pendientes }}</p>
            </div>

            <div class="metric-card">
              <div class="metric-top">
                <span class="metric-icon icon-blue">
                  <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>
                </span>
                <span class="metric-delta delta-down">{{ resumen.delta_en_proceso }}% hoy</span>
              </div>
              <p class="metric-label">En Curso</p>
              <p class="metric-value">{{ resumen.en_proceso }}</p>
            </div>

            <div class="metric-card">
              <div class="metric-top">
                <span class="metric-icon icon-green">
                  <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </span>
                <span class="metric-delta delta-neutral">{{ resumen.delta_personal }}% hoy</span>
              </div>
              <p class="metric-label">Personal Disponible</p>
              <p class="metric-value">{{ resumen.personal_disponible }}</p>
            </div>
          </section>

          <section class="table-card">
            <div class="table-card-header">
              <div>
                <h3>Solicitudes Pendientes</h3>
                <p class="table-subtitle">Gestión de tareas de mantenimiento entrantes</p>
              </div>
              <div class="table-actions" style="display: flex; gap: 0.6rem; align-items: center;">
                <!-- Guardado masivo para solucionar guardar varias cosas -->
                <button
                  v-if="hasChanges"
                  @click="guardarTodosLosCambios"
                  class="save-all-btn"
                  style="background: #16a34a; color: #fff; border: none; padding: 0.5rem 0.9rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.375rem; transition: background-color 0.2s;"
                  :disabled="guardandoTodos"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1rem; height: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2F9a2 2 0 00-2-2h-3m-1 4H8m0 0l3-3m-3 3l3 3" />
                  </svg>
                  {{ guardandoTodos ? 'Guardando...' : `Guardar Cambios (${Object.keys(cambiosPendientes).length})` }}
                </button>

                <router-link to="/solicitudes/nueva" class="new-btn"> Nueva Solicitud</router-link>
              </div>
            </div>

            <p v-if="loading" class="table-loading">Cargando solicitudes...</p>
            <p v-else-if="error" class="table-error">{{ error }}</p>

            <table v-else class="coord-table">
              <thead>
                <tr>
                  <th>ID SOLICITUD</th>
                  <th>PRIORIDAD</th>
                  <th>DEPARTAMENTO</th>
                  <th>FECHA SOLICITUD</th>
                  <th>ESTADO</th>
                  <th>ACCIÓN</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="s in solicitudes" :key="s.id" :class="{ 'row-alta': s.prioridad === 'Alta' }">
                  <td class="cell-id">
                    <router-link :to="`/solicitudes/${s.id}`" class="id-link">#SOL-{{ s.id }}</router-link>
                  </td>

                  <td>
                    <select
                      v-model="s.prioridad"
                      @change="registrarCambio(s, 'prioridad', s.prioridad)"
                      :class="prioridadClase(s.prioridad)"
                      class="priority-select"
                    >
                      <option value="Alta">Alta</option>
                      <option value="Media">Media</option>
                      <option value="Baja">Baja</option>
                    </select>
                  </td>

                  <td>{{ s.departamento ?? '—' }}</td>
                  <td>{{ formatFechaHora(s.fecha) }}</td>

                  <td>
                    <span class="status-badge" :class="estadoClase(s.estado)">{{ s.estado }}</span>
                  </td>

                  <td class="accion-cell">
                    <select
                      v-model="s.tecnico_id"
                      @change="registrarCambio(s, 'tecnico_id', s.tecnico_id)"
                      class="tecnico-select"
                    >
                      <option :value="null">Sin asignar</option>
                      <option v-for="t in tecnicos" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                    <button
                      class="assign-btn"
                      :disabled="!cambiosPendientes[s.id]"
                      @click="guardarCambios(s)"
                    >
                      Guardar
                    </button>
                  </td>
                </tr>
                <tr v-if="solicitudes.length === 0">
                  <td colspan="6" class="cell-empty">No hay solicitudes registradas.</td>
                </tr>
              </tbody>
            </table>

            <div class="table-footer">
              <span>Mostrando {{ solicitudes.length }} de {{ totalSolicitudes }} solicitudes</span>
              <div class="pagination">
                <button v-for="p in totalPaginas" :key="p" class="page-btn" :class="{ active: p === paginaActual }" @click="cambiarPagina(p)">{{ p }}</button>
                <button class="page-btn" :disabled="paginaActual >= totalPaginas" @click="cambiarPagina(paginaActual + 1)">›</button>
              </div>
            </div>
          </section>
        </template>

        <!-- SECCIÓN DE TÉCNICOS Y SU CARGA DE TRABAJO -->
        <template v-else-if="seccionActiva === 'tecnicos'">
          <section class="table-card" style="text-align: left;">
            <div class="table-card-header" style="margin-bottom: 2rem;">
              <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Equipo de Técnicos</h3>
                <p class="table-subtitle" style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Carga de trabajo y tickets activos asignados</p>
              </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
              <div v-for="t in tecnicosCarga" :key="t.id_usuario" class="p-5 rounded-2xl border border-slate-100 bg-white shadow-sm hover:shadow-md transition-all duration-200" style="background: white; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: left; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                  <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <!-- Avatar circle matching coordinator style -->
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background-color: #eff6ff; color: #2563eb; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                      {{ t.nombre_completo.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">{{ t.nombre_completo }}</h4>
                      <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Técnico de Soporte</p>
                    </div>
                  </div>
                </div>

                <div style="border-top: 1px solid #f8fafc; padding-top: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                  <div>
                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Tickets Activos</span>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0;">{{ t.tickets_activos }}</p>
                  </div>
                  
                  <!-- Workload Badge -->
                  <span :class="{
                    'bg-emerald-50 text-emerald-700 border-emerald-100': t.tickets_activos <= 2,
                    'bg-amber-50 text-amber-700 border-amber-100': t.tickets_activos > 2 && t.tickets_activos <= 4,
                    'bg-rose-50 text-rose-700 border-rose-100': t.tickets_activos > 4
                  }" style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.625rem; border-radius: 9999px; border: 1px solid; display: inline-block;">
                    {{ t.tickets_activos <= 2 ? 'Carga Ligera' : t.tickets_activos <= 4 ? 'Carga Moderada' : 'Carga Pesada' }}
                  </span>
                </div>
              </div>
            </div>
          </section>
        </template>

        <!-- SECCIÓN AJUSTES -->
        <template v-else-if="seccionActiva === 'ajustes'">
          <section class="table-card" style="text-align: left;">
            <div class="table-card-header" style="margin-bottom: 2rem;">
              <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Ajustes de Perfil</h3>
                <p class="table-subtitle" style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Administración de perfil y configuraciones del coordinador</p>
              </div>
            </div>

            <div style="max-width: 32rem; background: white; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
              <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem;">
                <div style="width: 4.5rem; height: 4.5rem; border-radius: 9999px; background-color: #eff6ff; color: #2563eb; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                  {{ userInitial }}
                </div>
                <div>
                  <h4 style="font-size: 1.2rem; font-weight: 700; color: #1e293b; margin: 0;">{{ displayName }}</h4>
                  <p style="font-size: 0.85rem; color: #94a3b8; margin: 0.25rem 0 0 0;">Rol: Coordinador de Mantenimiento</p>
                </div>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                  <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Nombre de Usuario</span>
                  <p style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0.25rem 0 0 0;">{{ authStore.user?.nombre }}</p>
                </div>
                <div>
                  <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Apellido de Usuario</span>
                  <p style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0.25rem 0 0 0;">{{ authStore.user?.apellido || 'García' }}</p>
                </div>
              </div>

              <div style="margin-bottom: 2rem;">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Correo de Contacto</span>
                <p style="font-size: 0.9rem; font-weight: 600; color: #334155; margin: 0.25rem 0 0 0;">{{ authStore.user?.correo }}</p>
              </div>

              <button @click="cerrarSesion" style="background: #dc2626; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-size: 0.875rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: background-color 0.2s;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1rem; height: 1rem;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Cerrar Sesión Activa
              </button>
            </div>
          </section>
        </template>

        <!-- SECCIÓN DE REPORTES -->
        <template v-else-if="seccionActiva === 'reportes'">
          <section class="table-card" style="text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
              <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Reportes de Mantenimiento</h3>
                <p class="table-subtitle" style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Resumen general de solicitudes del sistema.</p>
              </div>
              <button
                @click="exportarPDF"
                :disabled="reporteLoading || !!reporteError"
                style="background: #2563eb; color: #fff; border: none; padding: 0.6rem 1.1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap;"
              >
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 1rem; height: 1rem;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
              </button>
            </div>

            <p v-if="reporteLoading" style="color: #64748b; padding: 2rem 0;">Cargando reporte...</p>
            <p v-else-if="reporteError" style="color: #dc2626; padding: 2rem 0;">{{ reporteError }}</p>

            <template v-else>
              <!-- Métricas -->
              <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
                <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                  <p style="font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 0.04em; margin: 0 0 0.3rem;">PENDIENTES</p>
                  <p style="font-size: 2rem; font-weight: 800; margin: 0; color: #b45309;">{{ reporteResumen.pendientes }}</p>
                </div>
                <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                  <p style="font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 0.04em; margin: 0 0 0.3rem;">EN PROCESO</p>
                  <p style="font-size: 2rem; font-weight: 800; margin: 0; color: #2563eb;">{{ reporteResumen.en_proceso }}</p>
                </div>
                <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                  <p style="font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 0.04em; margin: 0 0 0.3rem;">COMPLETADAS</p>
                  <p style="font-size: 2rem; font-weight: 800; margin: 0; color: #16a34a;">{{ reporteResumen.completadas }}</p>
                </div>
                <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                  <p style="font-size: 0.8rem; color: #64748b; font-weight: 700; letter-spacing: 0.04em; margin: 0 0 0.3rem;">TOTAL</p>
                  <p style="font-size: 2rem; font-weight: 800; margin: 0; color: #1e293b;">{{ reporteResumen.total }}</p>
                </div>
              </div>

              <!-- Barras de distribución -->
              <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem;">
                <h4 style="margin: 0 0 1.25rem; font-size: 1.05rem; font-weight: 700; color: #1e293b;">Distribución por Estado</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                  <div v-for="item in reporteBarras" :key="item.label" style="display: flex; align-items: center; gap: 1rem;">
                    <span style="width: 110px; font-size: 0.85rem; font-weight: 600; color: #475569; flex-shrink: 0;">{{ item.label }}</span>
                    <div style="flex: 1; background: #f1f5f9; border-radius: 8px; height: 22px; overflow: hidden;">
                      <div :style="{ width: item.porcentaje + '%', background: item.color, height: '100%', borderRadius: '8px', transition: 'width 0.4s ease' }"></div>
                    </div>
                    <span style="width: 30px; text-align: right; font-weight: 700; flex-shrink: 0;">{{ item.valor }}</span>
                  </div>
                </div>
              </div>

              <!-- Tabla de detalle -->
              <div style="background: #fff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h4 style="margin: 0 0 1.25rem; font-size: 1.05rem; font-weight: 700; color: #1e293b;">Detalle de Solicitudes (últimas 10)</h4>
                <table style="width: 100%; border-collapse: collapse;">
                  <thead>
                    <tr>
                      <th style="text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0;">ID</th>
                      <th style="text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0;">SERVICIO</th>
                      <th style="text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0;">UBICACIÓN</th>
                      <th style="text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0;">FECHA</th>
                      <th style="text-align: left; font-size: 0.75rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.04em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0;">ESTADO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="s in reporteSolicitudes" :key="s.id">
                      <td style="padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9; font-weight: 700;">#SOL-{{ s.id }}</td>
                      <td style="padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9;">{{ s.titulo }}</td>
                      <td style="padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9; font-style: italic; color: #475569;">{{ s.ubicacion }}</td>
                      <td style="padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9;">{{ formatFechaHora(s.fecha) }}</td>
                      <td style="padding: 0.9rem 0.5rem; border-bottom: 1px solid #f1f5f9;">
                        <span class="status-badge" :class="estadoClase(s.estado)">{{ s.estado }}</span>
                      </td>
                    </tr>
                    <tr v-if="reporteSolicitudes.length === 0">
                      <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem 0;">No hay solicitudes registradas.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>
          </section>
        </template>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';
import tecnicosService from '../../services/tecnicosService';
import api from '../../services/api';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const authStore = useAuthStore();
const router = useRouter();

const solicitudes = ref([]);
const tecnicos = ref([]);
const tecnicosCarga = ref([]);
const cambiosPendientes = ref({});
const totalSolicitudes = ref(0);
const paginaActual = ref(1);
const totalPaginas = ref(1);
const busqueda = ref('');
const loading = ref(true);
const error = ref('');
const seccionActiva = ref('solicitudes');
const guardandoTodos = ref(false);

// --- REPORTES ---
const reporteResumen = ref({ pendientes: 0, en_proceso: 0, completadas: 0, total: 0 });
const reporteSolicitudes = ref([]);
const reporteLoading = ref(false);
const reporteError = ref('');

const reporteBarras = computed(() => {
  const total = reporteResumen.value.total || 1;
  return [
    { label: 'Pendientes',   valor: reporteResumen.value.pendientes,  color: '#d97706', porcentaje: (reporteResumen.value.pendientes  / total) * 100 },
    { label: 'En Proceso',   valor: reporteResumen.value.en_proceso,  color: '#2563eb', porcentaje: (reporteResumen.value.en_proceso  / total) * 100 },
    { label: 'Completadas',  valor: reporteResumen.value.completadas, color: '#16a34a', porcentaje: (reporteResumen.value.completadas / total) * 100 },
  ];
});

async function cargarReporte() {
  reporteLoading.value = true;
  reporteError.value = '';
  try {
    const [resumenData, listaData] = await Promise.all([
      solicitudesService.resumen(),
      solicitudesService.listar({ per_page: 10 }),
    ]);
    reporteResumen.value = resumenData;
    reporteSolicitudes.value = listaData.data ?? [];
  } catch (e) {
    reporteError.value = 'No se pudo cargar el reporte.';
  } finally {
    reporteLoading.value = false;
  }
}

function exportarPDF() {
  const doc = new jsPDF();
  const fechaGenerado = new Date().toLocaleDateString('es-ES');

  doc.setFontSize(16);
  doc.text('Hospital Salud Integral', 14, 18);
  doc.setFontSize(11);
  doc.text('Reporte de Solicitudes de Mantenimiento', 14, 25);
  doc.setFontSize(9);
  doc.setTextColor(100);
  doc.text(`Generado: ${fechaGenerado}`, 14, 31);
  doc.setTextColor(0);

  autoTable(doc, {
    startY: 38,
    head: [['Pendientes', 'En Proceso', 'Completadas', 'Total']],
    body: [[
      reporteResumen.value.pendientes,
      reporteResumen.value.en_proceso,
      reporteResumen.value.completadas,
      reporteResumen.value.total,
    ]],
    theme: 'grid',
    headStyles: { fillColor: [37, 99, 235] },
  });

  const finalY = doc.lastAutoTable.finalY + 10;
  doc.setFontSize(12);
  doc.text('Detalle de Solicitudes', 14, finalY);

  autoTable(doc, {
    startY: finalY + 5,
    head: [['ID', 'Servicio', 'Ubicación', 'Fecha', 'Estado']],
    body: reporteSolicitudes.value.map((s) => [
      `#SOL-${s.id}`,
      s.titulo,
      s.ubicacion,
      formatFechaHora(s.fecha),
      s.estado,
    ]),
    theme: 'striped',
    headStyles: { fillColor: [37, 99, 235] },
    styles: { fontSize: 9 },
  });

  const pdfData = doc.output('datauristring');
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/api/pdf/download';

  const inputPdf = document.createElement('input');
  inputPdf.type = 'hidden';
  inputPdf.name = 'pdf';
  inputPdf.value = pdfData;
  form.appendChild(inputPdf);

  const inputName = document.createElement('input');
  inputName.type = 'hidden';
  inputName.name = 'filename';
  inputName.value = `reporte-solicitudes-${fechaGenerado.replaceAll('/', '-')}.pdf`;
  form.appendChild(inputName);

  document.body.appendChild(form);
  form.submit();
  setTimeout(() => document.body.removeChild(form), 1000);
}

// Cargar reportes solo cuando el usuario navega a esa sección
watch(seccionActiva, (val) => {
  if (val === 'reportes' && reporteSolicitudes.value.length === 0) {
    cargarReporte();
  }
});

const resumen = ref({
  pendientes: 0,
  en_proceso: 0,
  personal_disponible: 8,
  delta_pendientes: 5,
  delta_en_proceso: -2,
  delta_personal: 0,
});

const displayName = computed(() => {
  if (authStore.user) {
    return `${authStore.user.nombre || ''} ${authStore.user.apellido || ''}`.trim() || 'Coordinador';
  }
  return 'Coordinador';
});

const userInitial = computed(() => displayName.value.charAt(0).toUpperCase());

const hasChanges = computed(() => Object.keys(cambiosPendientes.value).length > 0);

async function guardarTodosLosCambios() {
  guardandoTodos.value = true;
  error.value = '';
  try {
    const promesas = [];
    for (const [id, cambios] of Object.entries(cambiosPendientes.value)) {
      promesas.push(solicitudesService.actualizar(id, cambios));
    }
    await Promise.all(promesas);
    cambiosPendientes.value = {};
    await cargarSolicitudes(paginaActual.value);
  } catch (e) {
    error.value = 'Ocurrió un error al guardar algunos cambios.';
  } finally {
    guardandoTodos.value = false;
  }
}

async function cerrarSesion() {
  await authStore.logout();
  router.push('/login');
}

function formatFechaHora(fecha) {
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

const showNotificaciones = ref(false);
const notificaciones = ref([]);

function toggleNotificaciones() {
  showNotificaciones.value = !showNotificaciones.value;
}

function limpiarNotificaciones() {
  notificaciones.value = [];
  showNotificaciones.value = false;
}

function clickNotificacion(n) {
  showNotificaciones.value = false;
  router.push(`/solicitudes/${n.solicitudId}`);
}

async function cargarSolicitudes(pagina = 1) {
  loading.value = true;
  error.value = '';
  try {
    const params = { page: pagina };
    if (busqueda.value) params.buscar = busqueda.value;
    const [resumenData, listaData] = await Promise.all([
      solicitudesService.resumen(),
      solicitudesService.listar(params),
    ]);
    resumen.value.pendientes = resumenData.pendientes;
    resumen.value.en_proceso = resumenData.en_proceso;
    
    // Preservar selecciones locales pendientes tras recargar la tabla
    const rawList = listaData.data ?? [];
    solicitudes.value = rawList.map(s => {
      const pending = cambiosPendientes.value[s.id];
      if (pending) {
        return { ...s, ...pending };
      }
      return s;
    });

    totalSolicitudes.value = listaData.total ?? solicitudes.value.length;
    totalPaginas.value = listaData.last_page ?? 1;
    paginaActual.value = listaData.current_page ?? 1;

    // Generar notificaciones dinámicas a partir de solicitudes Pendientes
    const arraySolicitudes = listaData.data ?? [];
    const pendientes = arraySolicitudes.filter(s => s.estado === 'Pendiente');
    const nuevasNotif = [];
    pendientes.forEach((s, idx) => {
      nuevasNotif.push({
        id: `notif-${s.id}-${idx}`,
        solicitudId: s.id,
        tipo: 'pendiente',
        mensaje: `Nueva solicitud #${s.id.substring(0,8)}... requiere asignación.`,
        tiempo: 'Hace unos minutos'
      });
    });

    if (nuevasNotif.length === 0 && arraySolicitudes.length > 0) {
      nuevasNotif.push({
        id: 'notif-default',
        solicitudId: arraySolicitudes[0].id,
        tipo: 'preventivo',
        mensaje: 'Mantenimiento preventivo completado en la sede principal.',
        tiempo: 'Hace 1 hora'
      });
    }

    notificaciones.value = nuevasNotif;
  } catch (e) {
    error.value = 'No se pudieron cargar las solicitudes.';
  } finally {
    loading.value = false;
  }
}

async function cargarTecnicos() {
  try {
    const res = await api.get('/coordinador/tecnicos/carga-trabajo');
    tecnicosCarga.value = res.data || [];
    
    // Mapear al formato del selector simple
    tecnicos.value = tecnicosCarga.value.map(t => ({
      id: t.id_usuario,
      name: `${t.nombre_completo} (${t.tickets_activos} tickets activos)`
    }));
  } catch (e) {
    console.error('No se pudieron cargar los técnicos', e);
  }
}

function registrarCambio(solicitud, campo, valor) {
  if (!cambiosPendientes.value[solicitud.id]) {
    cambiosPendientes.value[solicitud.id] = {};
  }
  cambiosPendientes.value[solicitud.id][campo] = valor;
}

async function guardarCambios(solicitud) {
  const cambios = cambiosPendientes.value[solicitud.id];
  if (!cambios) return;

  try {
    await solicitudesService.actualizar(solicitud.id, cambios);
    delete cambiosPendientes.value[solicitud.id];
    await cargarSolicitudes(paginaActual.value);
  } catch (e) {
    error.value = 'No se pudo guardar el cambio.';
  }
}

function cambiarPagina(pagina) {
  if (pagina < 1 || pagina > totalPaginas.value) return;
  cargarSolicitudes(pagina);
}

let buscarTimeout = null;
function buscar() {
  clearTimeout(buscarTimeout);
  buscarTimeout = setTimeout(() => cargarSolicitudes(1), 400);
}

onMounted(() => {
  cargarSolicitudes(1);
  cargarTecnicos();
});
</script>

<style scoped>
.coord-layout { display: flex; height: 100vh; overflow: hidden; background: #f4f6fa; font-family: system-ui, -apple-system, sans-serif; color: #1e293b; }
.sidebar { width: 220px; background: #fff; border-right: 1px solid #e5e9f0; display: flex; flex-direction: column; padding: 1.5rem 1rem; flex-shrink: 0; height: 100%; }
.sidebar-brand { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem; padding: 0 0.5rem; }
.brand-logo svg { width: 18px; height: 18px; }
.brand-name { font-weight: 700; font-size: 0.95rem; }
.sidebar-nav { flex: 1; }
.nav-section { font-size: 0.7rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.05em; margin: 1rem 0.5rem 0.5rem; }
.nav-item { display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 0.5rem; border-radius: 8px; color: #475569; text-decoration: none; font-size: 0.9rem; font-weight: 600; background: transparent; transition: background-color 0.2s; }
.nav-item:hover { background: #f1f5f9; }
.nav-item.active { background: #eff6ff; color: #2563eb; }
.nav-icon { font-size: 1rem; width: 20px; text-align: center; }
.sidebar-user { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 0.5rem; border-top: 1px solid #e5e9f0; margin-top: auto; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0; cursor: pointer; }
.user-avatar:hover { background: #1d4ed8; }
.user-name { font-weight: 700; margin: 0; font-size: 0.85rem; }
.user-role { font-size: 0.75rem; color: #94a3b8; margin: 0; }
.coord-main { flex: 1; display: flex; flex-direction: column; min-width: 0; height: 100%; overflow: hidden; }
.coord-header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e9f0; flex-wrap: wrap; gap: 1rem; }
.header-title-row { display: flex; align-items: center; gap: 0.6rem; }
.header-title-row h1 { font-size: 1.2rem; margin: 0; }
.header-tag { background: #f1f5f9; color: #64748b; font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 6px; }
.header-actions { display: flex; align-items: center; gap: 0.75rem; }
.search-box { display: flex; align-items: center; gap: 0.4rem; background: #f1f5f9; border-radius: 8px; padding: 0.45rem 0.75rem; }
.search-box input { border: none; outline: none; background: none; font-size: 0.88rem; width: 180px; }
.icon-btn { cursor: pointer; font-size: 1.1rem; }
.coord-content { padding: 1.5rem; flex: 1; }
.metrics-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
.metric-card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.metric-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.metric-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.icon-amber { background: #fef3c7; }
.icon-blue { background: #dbeafe; }
.icon-green { background: #dcfce7; }
.metric-delta { font-size: 0.75rem; font-weight: 700; }
.delta-up { color: #16a34a; }
.delta-down { color: #dc2626; }
.delta-neutral { color: #94a3b8; margin: 0 0 0.25rem; }
.metric-label { font-size: 0.85rem; color: #64748b; margin: 0 0 0.25rem; }
.metric-value { font-size: 1.8rem; font-weight: 800; margin: 0; }
.table-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.table-card-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
.table-card-header h3 { margin: 0 0 0.2rem; font-size: 1.05rem; }
.table-subtitle { margin: 0; font-size: 0.85rem; color: #94a3b8; }
.table-actions { display: flex; gap: 0.6rem; }
.filter-btn { background: #fff; border: 1px solid #e2e8f0; padding: 0.5rem 0.9rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: #475569; }
.new-btn { background: #2563eb; color: #fff; border: none; padding: 0.5rem 0.9rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; cursor: pointer; }
.table-loading, .table-error { padding: 1.5rem 0; color: #64748b; }
.table-error { color: #dc2626; }
.coord-table { width: 100%; border-collapse: collapse; }
.coord-table th { text-align: left; font-size: 0.72rem; color: #94a3b8; font-weight: 700; letter-spacing: 0.03em; padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e9f0; }
.coord-table td { padding: 0.85rem 0.5rem; border-bottom: 1px solid #f1f5f9; font-size: 0.88rem; }
.row-alta td:first-child { border-left: 3px solid #dc2626; }
.cell-id { font-weight: 700; }
.id-link { color: #2563eb; text-decoration: none; font-weight: 700; }
.id-link:hover { text-decoration: underline; }
.cell-empty { text-align: center; color: #94a3b8; padding: 2rem 0; }
.priority-badge, .status-badge { display: inline-block; width: 90px; text-align: center; font-size: 0.74rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 14px; }
.priority-alta { background: #fef2f2; color: #dc2626; }
.priority-media { background: #fff7ed; color: #d97706; }
.priority-baja { background: #f0fdf4; color: #16a34a; }
.status-pendiente { background: #fef3c7; color: #b45309; }
.status-proceso { background: #dbeafe; color: #1d4ed8; }
.status-completada { background: #dcfce7; color: #15803d; }
.priority-select {
  font-size: 0.8rem;
  font-weight: 700;
  padding: 0.3rem 0.5rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  cursor: pointer;
}
.tecnico-select {
  font-size: 0.82rem;
  padding: 0.4rem 0.5rem;
  border-radius: 7px;
  border: 1px solid #e2e8f0;
  margin-right: 0.4rem;
  width: 130px;
}
.accion-cell { display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }
.assign-btn { background: #2563eb; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 7px; font-size: 0.8rem; font-weight: 700; cursor: pointer; }
.assign-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
.table-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; font-size: 0.85rem; color: #94a3b8; flex-wrap: wrap; gap: 0.75rem; }
.pagination { display: flex; gap: 0.3rem; }
.page-btn { width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; cursor: pointer; font-size: 0.8rem; }
.page-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }
@media (max-width: 900px) {
  .sidebar { display: none; }
  .metrics-row { grid-template-columns: 1fr; }
}
</style>