<template>
  <div class="flex h-screen w-full bg-slate-50 font-sans">
    <!-- Sidebar Lateral -->
    <aside :class="['shrink-0 flex-col border-r border-slate-200 bg-white px-4 py-6 transition-all duration-300 flex',
      sidebarColapsado ? 'w-20' : 'w-64']">
      
      <!-- Logo y Título -->
      <div class="mb-8 flex items-center gap-2 px-2 overflow-hidden">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div v-show="!sidebarColapsado" class="transition-opacity duration-300">
          <p class="text-[15px] font-semibold leading-tight text-slate-800 whitespace-nowrap">Salud Integral</p>
          <p class="text-xs leading-tight text-slate-400 whitespace-nowrap">Panel de Control</p>
        </div>
      </div>

      <!-- Navegación Principal -->
      <nav class="mb-8 flex flex-col gap-1">
        <button v-for="item in navItems" :key="item.label"
          @click="navegarA(item)"
          :class="['flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors w-full text-left',
            item.active ? 'bg-blue-50 font-medium text-blue-600' : 'text-slate-500 hover:bg-slate-50']">
          <span class="text-lg" v-html="item.iconSvg"></span>
          <span v-show="!sidebarColapsado" class="transition-opacity duration-300">{{ item.label }}</span>
        </button>
      </nav>


      <!-- Perfil de Usuario (solo visual, sin acción de logout) -->
      <div class="mt-auto flex flex-col gap-2 pt-4 border-t border-slate-100">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2 overflow-hidden">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-600">
            {{ userInitial }}
          </div>
          <div v-show="!sidebarColapsado" class="transition-opacity duration-300 min-w-0">
            <p class="text-sm font-medium text-slate-700 whitespace-nowrap truncate">{{ authStore.user?.nombre ?? 'Administrador' }}</p>
            <p class="text-xs text-slate-400 whitespace-nowrap">{{ rolMostrar }}</p>
          </div>
        </div>
        <!-- Cerrar Sesión al fondo, visible y discreto -->
        <button @click="cerrarSesion" v-show="!sidebarColapsado"
          class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors w-full text-left">
          <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Cerrar sesión
        </button>
      </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="flex flex-1 flex-col overflow-y-auto">
      
      <!-- Cabecera Superior -->
      <header class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
        <div class="flex items-center gap-3">
          <!-- Botón de Hamburguesa -->
          <button @click="toggleSidebar" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h1 class="text-lg font-semibold text-slate-800">{{ tituloEncabezado }}</h1>
        </div>

        <div class="flex items-center gap-4">
          <!-- Buscador Global con Dropdown de Resultados -->
          <div id="buscador-global" class="relative">
            <div class="relative hidden sm:flex items-center">
              <svg class="pointer-events-none absolute left-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
              </svg>
              <input
                type="text"
                v-model="buscarGlobal"
                @focus="mostrarDropdownBuscar = true"
                @keyup.escape="cerrarBuscador"
                @keyup.enter="ejecutarBusqueda"
                placeholder="Buscar ticket, área, estado..."
                class="w-64 rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-600 placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
              />
              <button v-if="buscarGlobal" @click="buscarGlobal = ''; mostrarDropdownBuscar = false" class="absolute right-2 text-slate-400 hover:text-slate-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Dropdown resultados -->
            <div
              v-if="mostrarDropdownBuscar && buscarGlobal.trim().length >= 1"
              class="absolute right-0 top-full mt-2 z-50 w-80 rounded-xl border border-slate-100 bg-white shadow-2xl overflow-hidden"
            >
              <!-- Sin resultados -->
              <div v-if="resultadosBusqueda.length === 0" class="px-4 py-6 text-center">
                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <p class="text-sm text-slate-400">Sin resultados para <span class="font-semibold text-slate-600">"{{ buscarGlobal }}"</span></p>
              </div>

              <!-- Resultados encontrados -->
              <template v-else>
                <div class="border-b border-slate-100 bg-slate-50 px-4 py-2">
                  <p class="text-xs font-semibold text-slate-500">{{ resultadosBusqueda.length }} resultado{{ resultadosBusqueda.length !== 1 ? 's' : '' }} encontrado{{ resultadosBusqueda.length !== 1 ? 's' : '' }}</p>
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                  <template v-for="item in resultadosBusqueda" :key="item._tipo === 'persona' ? 'u-'+item.id : 't-'+item.id">
                    <!-- TICKET -->
                    <button
                      v-if="item._tipo === 'ticket'"
                      @click="irATicket(item)"
                      class="flex w-full items-start gap-3 px-4 py-3 hover:bg-blue-50 transition-colors text-left"
                    >
                      <span :class="badgeEstadoSearch(item.estado)" class="mt-0.5 flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide">
                        {{ item.estado }}
                      </span>
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ item.titulo || 'Sin título' }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                          <span class="text-[10px] text-blue-600 font-mono font-semibold">{{ item._folio }}</span>
                          <span class="text-[10px] text-slate-300">·</span>
                          <span class="text-[10px] text-slate-500">{{ item.departamento || 'Sin área' }}</span>
                          <template v-if="item.prioridad">
                            <span class="text-[10px] text-slate-300">·</span>
                            <span :class="item.prioridad === 'Alta' ? 'text-rose-500' : item.prioridad === 'Media' ? 'text-amber-500' : 'text-slate-400'" class="text-[10px] font-medium">{{ item.prioridad }}</span>
                          </template>
                        </div>
                      </div>
                      <svg class="flex-shrink-0 h-4 w-4 text-slate-300 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <!-- PERSONA -->
                    <button
                      v-else-if="item._tipo === 'persona'"
                      @click="irAUsuario(item)"
                      class="flex w-full items-center gap-3 px-4 py-3 hover:bg-violet-50 transition-colors text-left"
                    >
                      <span class="flex-shrink-0 flex h-7 w-7 items-center justify-center rounded-full bg-violet-100 text-violet-700 text-[11px] font-bold uppercase">
                        {{ (item.nombre || '?')[0] }}{{ (item.apellido || '')[0] }}
                      </span>
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ item.nombre }} {{ item.apellido }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                          <span class="text-[10px] text-violet-600 font-medium capitalize">{{ item.rol || 'Usuario' }}</span>
                          <span class="text-[10px] text-slate-300">·</span>
                          <span class="text-[10px] text-slate-500 truncate">{{ item.correo }}</span>
                        </div>
                      </div>
                      <svg class="flex-shrink-0 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                  </template>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-4 py-2">
                  <p class="text-[10px] text-slate-400">Presiona <kbd class="rounded bg-slate-200 px-1 py-0.5 text-[9px] font-mono">Esc</kbd> para cerrar</p>
                </div>
              </template>
            </div>
          </div>
          
          <!-- Campana de Notificaciones Premium -->
          <div class="relative">
            <button @click="toggleNotificaciones" class="relative rounded-full p-2 text-slate-500 hover:bg-slate-50 focus:outline-none" title="Centro de notificaciones">
              <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <!-- Indicador de alerta roja si hay notificaciones sin leer -->
              <span v-if="notificacionesNoLeidas.length > 0" class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
              </span>
            </button>
            
            <!-- Dropdown de Notificaciones Persistente -->
            <div v-show="showNotificaciones" class="absolute right-0 mt-2 z-50 w-84 rounded-2xl border border-slate-100 bg-white py-2 shadow-xl" style="width: 22rem;">
              <div class="flex items-center justify-between border-b border-slate-100 px-4 pb-2 pt-1">
                <div>
                  <span class="text-xs font-bold text-slate-800">Notificaciones</span>
                  <span v-if="notificacionesNoLeidas.length > 0" class="ml-1.5 rounded-full bg-blue-100 px-1.5 py-0.2 text-[10px] font-bold text-blue-700">
                    {{ notificacionesNoLeidas.length }} nueva(s)
                  </span>
                </div>
                <button
                  v-if="notificacionesNoLeidas.length > 0"
                  @click="limpiarNotificaciones"
                  class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition-colors"
                >
                  Marcar todas leídas
                </button>
              </div>
              <div class="max-h-72 overflow-y-auto">
                <div
                  v-for="n in notificaciones"
                  :key="n.id"
                  @click="clickNotificacion(n)"
                  class="flex items-start gap-3 px-4 py-3 cursor-pointer border-b border-slate-50 last:border-0 transition-colors"
                  :class="!n.leida ? 'bg-blue-50/40 hover:bg-blue-50/70' : 'bg-white hover:bg-slate-50 opacity-80'"
                >
                  <div class="flex-shrink-0 mt-0.5">
                    <span v-if="n.tipo === 'cancelacion'" class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-200">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                    </span>
                    <span v-else-if="n.tipo === 'reporte'" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 border border-red-200">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <span v-else-if="n.tipo === 'pendiente'" class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                    </span>
                    <span v-else class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                      <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </span>
                  </div>
                  <div class="flex-1 flex flex-col gap-0.5 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                      <p class="text-xs font-medium leading-normal" :class="!n.leida ? 'text-slate-900 font-bold' : 'text-slate-600'">
                        {{ n.mensaje }}
                      </p>
                      <span v-if="!n.leida" class="h-2 w-2 rounded-full bg-blue-600 flex-shrink-0" title="No leída"></span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">{{ n.tiempo }}</span>
                  </div>
                </div>
                <div v-if="notificaciones.length === 0" class="py-8 text-center text-xs text-slate-400">
                  No hay notificaciones registradas.
                </div>
              </div>
            </div>
          </div>

          <button v-if="esAdmin" @click="crearNuevaSolicitud" class="rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            + Nueva Solicitud
          </button>
        </div>
      </header>

      <!-- Contenedor del Dashboard Dinámico -->
      <main class="flex flex-col gap-6 p-6">
        <!-- Alertas de Solicitudes Pausadas -->
        <div v-if="solicitudes.some(s => s.is_pausada)" class="flex flex-col gap-2">
          <div v-for="s in solicitudes.filter(s => s.is_pausada)" :key="s.id" 
            class="flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 shadow-sm">
            <div class="flex items-center gap-2">
              <span class="flex h-5 w-5 items-center justify-center text-amber-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </span>
              <span>
                El técnico <strong>{{ s.tecnico_nombre || 'asignado' }}</strong> pausó la tarea <strong>#SOL-{{ abreviarId(s.id) }}</strong>. 
                Motivo: <em>"{{ s.motivo_pausa || 'No especificado' }}"</em>
              </span>
            </div>
            <button @click="verDetalle(s.id)" class="text-xs font-bold text-amber-900 underline hover:no-underline whitespace-nowrap">Ver Ticket</button>
          </div>
        </div>
        
        <!-- SECCIÓN 1: DASHBOARD DE REPORTES -->
        <div v-if="seccionActiva === 'dashboard'" class="flex flex-col gap-6">
          <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold text-slate-800">{{ tituloDashboard }}</h1>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-sm text-slate-400">{{ subtituloDashboard }}</p>
              <div class="relative flex items-center gap-2">
                
                <!-- Selector de Fechas -->
                <div class="relative">
                  <button @click="showRangoMenu = !showRangoMenu" 
                    class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ rangoTexto }}</span>
                  </button>
                  <div v-show="showRangoMenu" class="absolute right-0 mt-1 z-10 w-44 rounded-lg border border-slate-100 bg-white py-1 shadow-lg">
                    <button v-for="r in rangosDisponibles" :key="r.dias" 
                      @click="seleccionarRango(r.dias)"
                      class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                      {{ r.texto }}
                    </button>
                  </div>
                </div>

                <!-- Botón Exportar PDF -->
                <button @click="exportarPDF" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                  <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  <span>Exportar</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Tarjetas Estadísticas -->
          <div class="flex flex-col gap-4 sm:flex-row">
            <div v-for="s in stats" :key="s.label" class="flex-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-1 flex items-start justify-between">
                <p class="text-[11px] font-medium tracking-wide text-slate-400">{{ s.label }}</p>
                <div :class="['flex h-9 w-9 items-center justify-center rounded-xl', s.iconBg]" v-html="s.iconSvg"></div>
              </div>
              <p class="mb-4 text-xs text-slate-400">{{ s.description }}</p>
              <p class="mb-2 text-3xl font-semibold text-slate-800">{{ s.value }}</p>
              <div :class="['flex items-center gap-1 text-xs font-medium', s.trendColor]">
                <span>{{ s.trend }}</span>
                <span class="font-normal text-slate-400">{{ s.trendLabel }}</span>
              </div>
            </div>
          </div>

          <!-- Gráficos -->
          <div class="flex flex-col gap-6 lg:flex-row">
            <!-- Solicitudes por Mes -->
            <div class="flex-1 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <div class="mb-6 flex items-start justify-between">
                <div>
                  <h2 class="text-base font-semibold text-slate-800">Solicitudes por Mes</h2>
                  <p class="mt-0.5 text-sm text-slate-400">Histórico de volumen de trabajo</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-500">
                  <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-blue-600"></span> Mantenimiento</span>
                  <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-slate-300"></span> Reparaciones</span>
                </div>
              </div>
              <div style="height: 220px">
                <Bar :data="barData" :options="barOptions" />
              </div>
            </div>

            <!-- Eficiencia (Rosquilla) -->
            <div class="w-full max-w-xs shrink-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <h2 class="text-base font-semibold text-slate-800">Eficiencia del Equipo</h2>
              <p class="mt-0.5 mb-4 text-sm text-slate-400">Distribución de estados hoy</p>
              <div class="relative mx-auto h-40 w-40">
                <Doughnut :data="doughnutData" :options="doughnutOptions" />
                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-2xl font-semibold text-slate-800">{{ tasaEficienciaGeneral }}%</span>
                  <span class="text-[10px] tracking-wide text-slate-400">GENERAL</span>
                </div>
              </div>
              <div class="mt-5 flex flex-col gap-2.5">
                <div v-for="e in efficiencyData" :key="e.name" class="flex items-center justify-between text-sm">
                  <span class="flex items-center gap-2 text-slate-500">
                    <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: e.color }"></span>{{ e.name }}
                  </span>
                  <span class="font-medium text-slate-700">{{ e.value }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actividad Reciente -->
          <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between px-6 pt-5 pb-4">
              <h2 class="text-base font-semibold text-slate-800">Actividad Reciente</h2>
              <button @click="verTodasLasSolicitudes" class="text-sm font-medium text-blue-600 hover:underline">Ver todas →</button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left text-sm">
                <thead>
                  <tr class="border-y border-slate-100 bg-slate-50/60 text-xs font-medium tracking-wide text-slate-400">
                    <th class="px-6 py-3">ID</th>
                    <th class="px-3 py-3">DEPARTAMENTO</th>
                    <th class="px-3 py-3">SERVICIO</th>
                    <th class="px-3 py-3">ESTADO</th>
                    <th class="px-3 py-3">PRIORIDAD</th>
                    <th class="px-6 py-3">FECHA</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="a in actividadReciente" :key="a.id" @click="verDetalle(a.id)" class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 cursor-pointer transition-colors">
                    <td class="px-6 py-4 font-semibold text-blue-600 hover:text-blue-700 transition-colors">#REQ-{{ abreviarId(a.id) }}</td>
                    <td class="px-3 py-4 text-slate-600 font-medium">{{ a.departamento }}</td>
                    <td class="px-3 py-4 text-slate-600 font-medium">{{ a.titulo }}</td>
                    <td class="px-3 py-4">
                      <span :class="['rounded-full px-2.5 py-1 text-xs font-semibold', statusStyles[a.estado] || statusStyles['Pendiente']]">
                        {{ a.estado }}
                      </span>
                    </td>
                    <td class="px-3 py-4">
                      <span class="flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span :class="['h-2 w-2 rounded-full', priorityDot[a.prioridad] || priorityDot['Media']]"></span>
                        {{ a.prioridad }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-slate-400">{{ a.fecha }}</td>
                  </tr>
                  <tr v-if="actividadReciente.length === 0">
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                      No se encontraron solicitudes con el filtro seleccionado.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 2: GESTIÓN DE USUARIOS -->
        <div v-if="seccionActiva === 'usuarios' && esAdmin" class="flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-800">Gestión de Usuarios</h1>
              <p class="text-sm text-slate-400">Administración de credenciales, roles y accesos al sistema.</p>
            </div>
            <button @click="abrirModalCrearUsuario" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              + Nuevo Usuario
            </button>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40">
              <input type="text" v-model="buscarUsuarioQuery" placeholder="Filtrar por nombre o correo..." 
                class="w-full max-w-md rounded-lg border border-slate-200 py-1.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left text-sm">
                <thead>
                  <tr class="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold text-slate-500">
                    <th class="px-6 py-3">NOMBRE</th>
                    <th class="px-6 py-3">CORREO INSTITUCIONAL</th>
                    <th class="px-6 py-3">ROL</th>
                    <th class="px-6 py-3">ESTADO</th>
                    <th class="px-6 py-3 text-right">ACCIONES</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="u in usuariosFiltrados" :key="u.id ?? u.id_usuario" class="border-b border-slate-100 hover:bg-slate-50/40">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ u.nombre_solo ?? u.nombre }} {{ u.apellido }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ u.correo }}</td>
                    <td class="px-6 py-4">
                      <span class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600 font-medium">
                        {{ u.rol ?? u.rol?.nombre_rol ?? 'Sin rol' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="['rounded-full px-2 py-0.5 text-xs font-semibold', 
                        u.estado === 'Activo' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600']">
                        {{ u.estado }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center justify-end gap-1">
                        <!-- Editar usuario -->
                        <button
                          @click="abrirModalEditarUsuario(u)"
                          title="Editar usuario"
                          class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                        >
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                          </svg>
                        </button>
                        <!-- Deshabilitar / Habilitar -->
                        <button
                          @click="confirmarCambiarEstado(u)"
                          :title="u.estado === 'Activo' ? 'Deshabilitar usuario' : 'Habilitar usuario'"
                          :class="u.estado === 'Activo'
                            ? 'text-amber-500 hover:bg-amber-50 hover:text-amber-600'
                            : 'text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600'"
                          class="flex h-8 w-8 items-center justify-center rounded-lg transition-colors"
                        >
                          <svg v-if="u.estado === 'Activo'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                          </svg>
                          <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                        </button>
                        <!-- Eliminar usuario -->
                        <button
                          @click="confirmarEliminarUsuario(u)"
                          title="Eliminar usuario"
                          class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                        >
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 3: GESTIÓN DE DEPARTAMENTOS -->
        <div v-if="seccionActiva === 'departamentos' && esAdmin" class="flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-800">Sedes y Unidades Médicas</h1>
              <p class="text-sm text-slate-400">Gestión de ubicaciones físicas y áreas del hospital.</p>
            </div>
            <div class="flex gap-2">
              <button @click="abrirModalCrearSede" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                + Nueva Sede
              </button>
              <button @click="abrirModalCrearUnidad" class="rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Nueva Unidad
              </button>
            </div>
          </div>

          <div class="flex flex-col gap-6">
            <div v-for="sede in sedes" :key="sede.id_sede" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                  <h2 class="text-lg font-bold text-slate-800">{{ sede.nombre_sede }}</h2>
                  <p class="text-xs text-slate-400">{{ sede.direccion }}</p>
                </div>
                <div class="flex items-center gap-1">
                  <button @click="abrirEditarSede(sede)" title="Editar sede"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  <button @click="confirmarEliminarSede(sede)" title="Eliminar sede"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>
              <div class="flex flex-wrap gap-2">
                <div v-for="un in unitsBySede[sede.id_sede]" :key="un.id_unidad"
                  class="group flex items-center gap-1.5 rounded-lg border border-slate-150 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600">
                  <span>{{ un.nombre_unidad }}</span>
                  <div class="hidden group-hover:flex items-center gap-0.5 ml-1">
                    <button @click="abrirEditarUnidad(un)" title="Editar unidad"
                      class="text-slate-400 hover:text-blue-600 transition-colors">
                      <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button @click="confirmarEliminarUnidad(un)" title="Eliminar unidad"
                      class="text-slate-400 hover:text-rose-600 transition-colors">
                      <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                </div>
                <span v-if="!unitsBySede[sede.id_sede] || unitsBySede[sede.id_sede].length === 0" class="text-xs text-slate-400 italic">
                  Sin unidades registradas.
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 4: PANEL DE REPORTES -->
        <div v-if="seccionActiva === 'reportes'" class="flex flex-col gap-4">
          <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm max-w-xl mx-auto my-8">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800">Generación de Reportes Ejecutivos</h2>
            <p class="mt-2 text-sm text-slate-500">
              Para generar e imprimir el reporte ejecutivo administrativo de mantenimiento del hospital, por favor utilice el botón de <b>Exportar</b> en la esquina superior del Dashboard Principal.
            </p>
          </div>
        </div>

        <!-- SECCIÓN 5: CONFIGURACIÓN -->
        <div v-if="seccionActiva === 'configuracion'" class="flex flex-col gap-4">
          <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm max-w-xl">
            <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mb-2">Configuración del Sistema</h1>
            <p class="text-sm text-slate-500 mb-6">Información de la cuenta y parámetros del sistema de mantenimiento.</p>
            <div class="flex flex-col gap-3">
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Versión del Sistema</span>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">MVP v1.0.0</span>
              </div>
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Usuario Activo</span>
                <span class="text-sm text-slate-500">{{ authStore.user?.nombre ?? '---' }}</span>
              </div>
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Rol del Usuario</span>
                <span class="text-sm text-slate-500">{{ rolMostrar }}</span>
              </div>
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Correo Institucional</span>
                <span class="text-sm text-slate-500">{{ authStore.user?.correo ?? '---' }}</span>
              </div>
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Hospital</span>
                <span class="text-sm text-slate-500">RCH Hospital, S.V.</span>
              </div>
              <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm font-medium text-slate-700">Soporte Técnico</span>
                <span class="text-sm text-slate-500">soporte@rchhospital.sv</span>
              </div>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 6: TABLERO DE COORDINACIÓN (Administrador y Coordinador) -->
        <div v-if="seccionActiva === 'coordinacion'" class="flex flex-col gap-6 text-left">
          <section class="flex flex-col gap-4 sm:flex-row">
            <div class="flex-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-1 flex items-start justify-between">
                <p class="text-[11px] font-medium tracking-wide text-slate-400">PENDIENTES HOY</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
              </div>
              <p class="mb-2 text-3xl font-semibold text-slate-800">{{ resumenCoord.pendientes }}</p>
            </div>
            
            <div class="flex-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-1 flex items-start justify-between">
                <p class="text-[11px] font-medium tracking-wide text-slate-400">EN CURSO</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>
                </div>
              </div>
              <p class="mb-2 text-3xl font-semibold text-slate-800">{{ resumenCoord.en_proceso }}</p>
            </div>

            <div class="flex-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-1 flex items-start justify-between">
                <p class="text-[11px] font-medium tracking-wide text-slate-400">PERSONAL DISPONIBLE</p>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
              </div>
              <p class="mb-2 text-3xl font-semibold text-slate-800">{{ resumenCoord.personal_disponible }}</p>
            </div>
          </section>

          <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden p-6">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
              <div>
                <h3 class="text-base font-semibold text-slate-800">Solicitudes Pendientes</h3>
                <p class="text-xs text-slate-400">Gestión de tareas de mantenimiento entrantes</p>
              </div>
              <div class="flex items-center gap-3">
                <input type="text" v-model="busquedaCoord" placeholder="Buscar..." @input="buscarCoord"
                  class="rounded-lg border border-slate-200 py-1.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 w-56 bg-slate-50"/>
                <button
                  v-if="hasChangesCoord"
                  @click="guardarTodosLosCambiosCoord"
                  class="rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors disabled:opacity-50 flex items-center gap-1.5"
                  :disabled="guardandoTodosCoord"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2F9a2 2 0 00-2-2h-3m-1 4H8m0 0l3-3m-3 3l3 3" />
                  </svg>
                  Guardar Todos ({{ Object.keys(cambiosPendientes).length }})
                </button>
              </div>
            </div>

            <p v-if="loadingCoord" class="py-8 text-center text-slate-400">Cargando solicitudes...</p>
            <p v-else-if="errorCoord" class="py-8 text-center text-rose-500 font-medium">{{ errorCoord }}</p>

            <div v-else class="overflow-x-auto">
              <table class="w-full border-collapse text-left text-sm">
                <thead>
                  <tr class="border-y border-slate-100 bg-slate-50/60 text-xs font-semibold text-slate-400">
                    <th class="px-6 py-3">ID SOLICITUD</th>
                    <th class="px-3 py-3">PRIORIDAD</th>
                    <th class="px-3 py-3">DEPARTAMENTO</th>
                    <th class="px-3 py-3">FECHA SOLICITUD</th>
                    <th class="px-3 py-3">ESTADO</th>
                    <th class="px-6 py-3 text-right">ACCIONES</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="s in solicitudesCoordFiltradas" :key="s.id" :class="{'bg-rose-50/30': s.prioridad === 'Alta'}" class="border-b border-slate-100 last:border-0 hover:bg-slate-50/40">
                    <td class="px-6 py-4 font-semibold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer" @click="verDetalle(s.id)" title="Ver detalle de la solicitud">
                      #SOL-{{ abreviarId(s.id) }}
                    </td>
                    <td class="px-3 py-4">
                      <select v-model="s.prioridad" @change="registrarCambioCoord(s, 'prioridad', s.prioridad)"
                        class="rounded-lg border border-slate-200 bg-white py-1 px-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer"
                        :class="{
                          'text-rose-600 bg-rose-50/50 border-rose-200': s.prioridad === 'Alta',
                          'text-amber-600 bg-amber-50/50 border-amber-200': s.prioridad === 'Media',
                          'text-emerald-600 bg-emerald-50/50 border-emerald-200': s.prioridad === 'Baja'
                        }">
                        <option value="Alta" class="text-rose-600">Alta</option>
                        <option value="Media" class="text-amber-600">Media</option>
                        <option value="Baja" class="text-emerald-600">Baja</option>
                      </select>
                    </td>
                    <td class="px-3 py-4 text-slate-600 font-medium">{{ s.departamento ?? '—' }}</td>
                    <td class="px-3 py-4 text-slate-400 font-medium">{{ formatFecha(s.fecha) }}</td>
                    <td class="px-3 py-4">
                      <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                        :class="{
                          'bg-amber-50 text-amber-600': s.estado === 'Pendiente',
                          'bg-blue-50 text-blue-600': s.estado === 'En Proceso',
                          'bg-emerald-50 text-emerald-600': s.estado === 'Completada'
                        }">
                        {{ s.estado }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <div class="relative">
                          <select
                            v-model="s.tecnico_id"
                            @change="registrarCambioCoord(s, 'tecnico_id', s.tecnico_id)"
                            class="rounded-lg border border-slate-200 bg-white py-1.5 pl-2.5 pr-7 text-xs font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 max-w-[160px] shadow-sm transition-all"
                            :disabled="['Resuelta','Cerrada','Validada','Completada'].includes(s.estado)"
                          >
                            <option :value="null">Sin asignar</option>
                            <option v-for="t in tecnicos" :key="t.id" :value="t.id">{{ t.name }}</option>
                          </select>
                        </div>
                        <button
                          v-if="!['Resuelta','Cerrada','Validada','Completada'].includes(s.estado)"
                          @click="guardarCambiosCoord(s)"
                          :disabled="!cambiosPendientes[s.id]"
                          :class="cambiosPendientes[s.id]
                            ? 'bg-blue-600 text-white shadow-sm hover:bg-blue-700 font-semibold'
                            : 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed opacity-40'"
                          class="flex items-center gap-1 rounded-lg border px-3 py-1.5 text-xs transition-all"
                          title="Guardar cambios de asignación"
                        >
                          <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                          Guardar
                        </button>
                        <!-- Boton Reabrir para tickets terminados -->
                        <button
                          v-if="['Resuelta','Cerrada','Validada','Completada'].includes(s.estado)"
                          @click="abrirModalReabrir(s)"
                          class="flex items-center gap-1 rounded-lg bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition-colors shadow-sm"
                        >
                          <span class="text-xs">↺</span> Reabrir
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="solicitudesCoord.length === 0">
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">No hay solicitudes registradas.</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex justify-between items-center mt-6 flex-wrap gap-4 text-xs text-slate-400 font-medium">
              <span>Mostrando {{ solicitudesCoord.length }} de {{ totalSolicitudesCoord }} solicitudes</span>
              <div class="flex gap-1">
                <button v-for="p in totalPaginasCoord" :key="p" @click="cambiarPaginaCoord(p)"
                  :class="['h-7 w-7 rounded-lg border text-xs font-bold transition-all', p === paginaActualCoord ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50']">
                  {{ p }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 7: EQUIPO DE TÉCNICOS (Administrador y Coordinador) -->
        <div v-if="seccionActiva === 'tecnicos'" class="flex flex-col gap-4 text-left">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-800">Equipo de Técnicos</h1>
              <p class="text-sm text-slate-400">Carga de trabajo y tickets activos asignados</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 my-4">
            <div v-for="t in tecnicosCarga" :key="t.id_usuario" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between gap-4">
              <div>
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-600 uppercase">
                    {{ t.nombre_completo.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="text-sm font-bold text-slate-800">{{ t.nombre_completo }}</h4>
                    <p class="text-xs text-slate-400">Técnico de Soporte</p>
                  </div>
                </div>
              </div>

              <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                <div class="text-left">
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets Activos</span>
                  <p class="text-2xl font-extrabold text-slate-800 leading-none mt-1">{{ t.tickets_activos }}</p>
                </div>
                
                <span :class="['text-xs font-bold px-2.5 py-1 rounded-full border', 
                  t.tickets_activos <= 2 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' :
                  t.tickets_activos <= 4 ? 'bg-amber-50 text-amber-700 border-amber-100' :
                  'bg-rose-50 text-rose-700 border-rose-100']">
                  {{ t.tickets_activos <= 2 ? 'Carga Ligera' : t.tickets_activos <= 4 ? 'Carga Moderada' : 'Carga Pesada' }}
                </span>
              </div>
            </div>
            <div v-if="tecnicosCarga.length === 0" class="col-span-full py-12 text-center text-slate-400">
              No hay técnicos registrados o disponibles en el sistema.
            </div>
          </div>
        </div>

        <!-- SECCIÓN 5: LOGS DE AUDITORÍA -->
        <div v-if="seccionActiva === 'auditoria'" class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
          <div class="mb-6 text-left">
            <h2 class="text-xl font-bold text-slate-800">Log de Auditoría y Cambios</h2>
            <p class="text-sm text-slate-400">Historial completo de modificaciones en solicitudes, evidencias e insumos.</p>
          </div>

          <div v-if="loadingLogs" class="text-center py-8 text-slate-500 text-left">Cargando bitácora de auditoría...</div>
          <div v-else-if="logsAuditoria.length === 0" class="text-center py-8 text-slate-400 text-left">No se han registrado cambios aún.</div>
          
          <div v-else class="flex flex-col gap-4">
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left text-sm">
                <thead>
                  <tr class="border-y border-slate-100 bg-slate-50/60 text-xs font-medium tracking-wide text-slate-400">
                    <th class="px-6 py-3">TICKET</th>
                    <th class="px-3 py-3">FECHA</th>
                    <th class="px-3 py-3">USUARIO</th>
                    <th class="px-3 py-3">CAMPO MODIFICADO</th>
                    <th class="px-3 py-3">VALOR ANTERIOR</th>
                    <th class="px-3 py-3">VALOR NUEVO</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="log in logsAuditoria" :key="log.id_historial" class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 font-semibold text-blue-600 cursor-pointer" @click="verDetalle(log.id_solicitud)">
                      #SOL-{{ abreviarId(log.id_solicitud) }}
                    </td>
                    <td class="px-3 py-4 text-slate-500 whitespace-nowrap">{{ formatFechaHora(log.created_at) }}</td>
                    <td class="px-3 py-4 text-slate-700 font-medium">
                      {{ log.usuario ? `${log.usuario.nombre} ${log.usuario.apellido}` : 'Usuario' }}
                    </td>
                    <td class="px-3 py-4">
                      <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                        {{ log.campo_modificado }}
                      </span>
                    </td>
                    <td class="px-3 py-4 text-slate-500 max-w-xs truncate" :title="log.valor_anterior">{{ log.valor_anterior || '—' }}</td>
                    <td class="px-3 py-4 text-slate-800 font-medium max-w-xs truncate" :title="log.valor_nuevo">{{ log.valor_nuevo || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div v-if="totalPaginasLogs > 1" class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
              <span class="text-xs text-slate-400">Total: {{ totalLogs }} registros</span>
              <div class="flex gap-2">
                <button :disabled="paginaActualLogs === 1" @click="cargarLogsAuditoria(paginaActualLogs - 1)"
                  class="rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50">
                  Anterior
                </button>
                <button :disabled="paginaActualLogs === totalPaginasLogs" @click="cargarLogsAuditoria(paginaActualLogs + 1)"
                  class="rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50">
                  Siguiente
                </button>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>

    <!-- MODAL: NUEVA SOLICITUD INLINE -->
    <div v-if="showNuevaSolicitudModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="mb-1 text-lg font-bold text-slate-800">Nueva Solicitud de Servicio</h3>
        <p class="mb-5 text-sm text-slate-400">Complete los datos para reportar una falla o requerimiento.</p>
        <form @submit.prevent="guardarNuevaSolicitud" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tipo de Servicio</label>
            <select v-model="formSolicitud.tipoServicio" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option value="" disabled>Seleccione el tipo</option>
              <option v-for="t in tiposServicio" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Ubicación del Problema</label>
            <input type="text" v-model="formSolicitud.ubicacion" required placeholder="Piso, ala, habitación o área" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Descripción Detallada</label>
            <textarea v-model="formSolicitud.descripcion" rows="3" required placeholder="Describa la falla o requerimiento..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Adjuntar Evidencia Fotográfica <span class="font-normal text-slate-400">(Opcional)</span></label>
            <label class="flex flex-col items-center justify-center gap-2 cursor-pointer rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-5 hover:border-blue-400 hover:bg-blue-50/40 transition-colors">
              <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4 4 4m0 0l4-4 4 4M12 8v8" />
              </svg>
              <span class="text-xs text-blue-600 font-medium">{{ formSolicitud.archivo ? formSolicitud.archivo.name : 'Subir un archivo o arrastrar y soltar' }}</span>
              <span class="text-xs text-slate-400">PNG, JPG hasta 10MB</span>
              <input type="file" accept="image/png,image/jpeg" class="hidden" @change="e => formSolicitud.archivo = e.target.files[0]" />
            </label>
          </div>
          <p v-if="errorSolicitud" class="text-sm text-red-500">{{ errorSolicitud }}</p>
          <p v-if="exitoSolicitud" class="text-sm text-emerald-600 font-medium">{{ exitoSolicitud }}</p>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showNuevaSolicitudModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="submit" :disabled="enviandoSolicitud" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
              {{ enviandoSolicitud ? 'Enviando...' : 'Enviar Solicitud' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL INTERACTIVO: NUEVO USUARIO -->
    <div v-if="showUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="mb-4 text-lg font-bold text-slate-800">Crear Nuevo Usuario</h3>
        <form @submit.prevent="guardarUsuario" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre</label>
            <input type="text" v-model="formUser.nombre" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Apellido</label>
            <input type="text" v-model="formUser.apellido" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Correo Electrónico</label>
            <input type="email" v-model="formUser.correo" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Contraseña <span class="font-normal text-slate-400">(Opcional)</span></label>
            <input type="password" v-model="formUser.password" placeholder="Dejar vacío para autogenerar" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Rol</label>
            <select v-model="formUser.id_rol" @change="alCambiarRol" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option value="" disabled>Seleccione el rol</option>
              <option v-for="r in roles" :key="r.id_rol" :value="r.id_rol">{{ r.nombre_rol }}</option>
            </select>
          </div>
          <div v-if="esRolOperativo">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Sede (Hospital / Clínicas)</label>
            <select v-model="formUser.id_sede" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option value="" disabled>Seleccione la sede correspondiente</option>
              <option v-for="s in sedes" :key="s.id_sede" :value="s.id_sede">{{ s.nombre_sede }}</option>
            </select>
          </div>
          <div v-if="esTecnico">
            <label class="block text-xs font-semibold text-slate-500 mb-1">Área de Especialidad</label>
            <select v-model="formUser.area_servicio" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option value="" disabled>Seleccione el área de trabajo</option>
              <option value="Servicios Generales">Servicios Generales (Eléctrico, Plomería, A/C, Limpieza)</option>
              <option value="Soporte Técnico">Soporte Técnico (Equipos Médicos, Redes, Software)</option>
            </select>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showUserModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              Guardar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL INTERACTIVO: NUEVA SEDE -->
    <div v-if="showSedeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="mb-4 text-lg font-bold text-slate-800">Crear Nueva Sede</h3>
        <form @submit.prevent="guardarSede" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre de la Sede</label>
            <input type="text" v-model="formSede.nombre_sede" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Dirección</label>
            <input type="text" v-model="formSede.direccion" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showSedeModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              Guardar Sede
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL INTERACTIVO: NUEVA UNIDAD -->
    <div v-if="showUnidadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="mb-4 text-lg font-bold text-slate-800">Crear Nueva Unidad Médica</h3>
        <form @submit.prevent="guardarUnidad" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre de la Unidad</label>
            <input type="text" v-model="formUnidad.nombre_unidad" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Sede Asociada</label>
            <select v-model="formUnidad.id_sede" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option v-for="s in sedes" :key="s.id_sede" :value="s.id_sede">{{ s.nombre_sede }}</option>
            </select>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showUnidadModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
              Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              Guardar Unidad
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL: EDITAR USUARIO -->
    <div v-if="showEditUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3 mb-5">
          <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </span>
          <div>
            <h3 class="text-base font-bold text-slate-800">Editar Usuario</h3>
            <p class="text-xs text-slate-400">{{ editUserForm.nombre }} {{ editUserForm.apellido }}</p>
          </div>
        </div>
        <form @submit.prevent="showConfirmGuardarUser = true" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Correo Electrónico</label>
            <input type="email" v-model="editUserForm.correo" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Rol</label>
            <select v-model="editUserForm.id_rol" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option v-for="r in roles" :key="r.id_rol" :value="r.id_rol">{{ r.nombre_rol }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nueva Contraseña <span class="font-normal text-slate-400">(dejar vacío para no cambiar)</span></label>
            <input type="password" v-model="editUserForm.password" placeholder="Nueva contraseña..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showEditUserModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancelar</button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Guardar Cambios</button>
          </div>
        </form>
        <!-- Confirmación guardar -->
        <div v-if="showConfirmGuardarUser" class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
          <p class="text-sm font-semibold text-blue-800 mb-3">¿Confirmas guardar los cambios del usuario?</p>
          <div class="flex justify-end gap-2">
            <button @click="showConfirmGuardarUser = false" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-white">No, volver</button>
            <button @click="guardarEdicionUsuario" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Sí, guardar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: CONFIRMAR DESHABILITAR / HABILITAR USUARIO -->
    <div v-if="showConfirmEstadoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 text-center">
        <span :class="usuarioAccion?.estado === 'Activo' ? 'bg-amber-50 text-amber-500' : 'bg-emerald-50 text-emerald-500'"
          class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full">
          <svg v-if="usuarioAccion?.estado === 'Activo'" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
          <svg v-else class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <h3 class="text-base font-bold text-slate-800 mb-1">
          {{ usuarioAccion?.estado === 'Activo' ? 'Deshabilitar usuario' : 'Habilitar usuario' }}
        </h3>
        <p class="text-sm text-slate-500 mb-5">
          ¿Estás seguro de que deseas <strong>{{ usuarioAccion?.estado === 'Activo' ? 'deshabilitar' : 'habilitar' }}</strong> a
          <strong>{{ usuarioAccion?.nombre }} {{ usuarioAccion?.apellido }}</strong>?
          {{ usuarioAccion?.estado === 'Activo' ? 'El usuario perderá acceso al sistema.' : 'El usuario recuperará acceso al sistema.' }}
        </p>
        <div class="flex gap-2 justify-center">
          <button @click="showConfirmEstadoModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancelar</button>
          <button @click="ejecutarCambiarEstado" :class="usuarioAccion?.estado === 'Activo' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'"
            class="rounded-lg px-4 py-2 text-sm font-semibold text-white">
            {{ usuarioAccion?.estado === 'Activo' ? 'Sí, deshabilitar' : 'Sí, habilitar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: CONFIRMAR ELIMINAR USUARIO -->
    <div v-if="showConfirmDeleteUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 text-center">
        <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-500">
          <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </span>
        <h3 class="text-base font-bold text-slate-800 mb-1">Eliminar usuario</h3>
        <p class="text-sm text-slate-500 mb-2">
          Vas a eliminar permanentemente a <strong>{{ usuarioAccion?.nombre }} {{ usuarioAccion?.apellido }}</strong>.
        </p>
        <div v-if="usuarioAccion?.estado === 'Activo'" class="mb-4 rounded-lg bg-amber-50 border border-amber-100 px-4 py-2 text-xs text-amber-700 font-medium">
          ⚠️ Este usuario está activo. Debes deshabilitarlo antes de poder eliminarlo.
        </div>
        <div v-else class="mb-4 rounded-lg bg-rose-50 border border-rose-100 px-4 py-2 text-xs text-rose-700">
          Esta acción es <strong>irreversible</strong>. El usuario será borrado de la base de datos.
        </div>
        <div class="flex gap-2 justify-center">
          <button @click="showConfirmDeleteUserModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancelar</button>
          <button v-if="usuarioAccion?.estado !== 'Activo'" @click="ejecutarEliminarUsuario"
            class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
            Sí, eliminar
          </button>
          <button v-else @click="showConfirmDeleteUserModal = false; confirmarCambiarEstado(usuarioAccion)"
            class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
            Deshabilitar primero
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: EDITAR SEDE -->
    <div v-if="showEditSedeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-100">
        <h3 class="mb-4 text-base font-bold text-slate-800">Editar Sede</h3>
        <form @submit.prevent="guardarEdicionSede" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre de la Sede</label>
            <input type="text" v-model="formEditSede.nombre_sede" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Dirección</label>
            <input type="text" v-model="formEditSede.direccion" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showEditSedeModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancelar</button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL: CONFIRMAR ELIMINAR SEDE -->
    <div v-if="showConfirmDeleteSedeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 text-center">
        <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-500">
          <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </span>
        <h3 class="text-base font-bold text-slate-800 mb-1">Eliminar Sede</h3>
        <p class="text-sm text-slate-500 mb-5">
          ¿Estás seguro de que deseas eliminar la sede <strong>{{ sedeAccion?.nombre_sede }}</strong>?
        </p>
        <div class="flex gap-2 justify-center">
          <button @click="showConfirmDeleteSedeModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancelar</button>
          <button @click="ejecutarEliminarSede" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Sí, eliminar</button>
        </div>
      </div>
    </div>

    <!-- MODAL: EDITAR UNIDAD -->
    <div v-if="showEditUnidadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-100">
        <h3 class="mb-4 text-base font-bold text-slate-800">Editar Unidad Médica</h3>
        <form @submit.prevent="guardarEdicionUnidad" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre de la Unidad</label>
            <input type="text" v-model="formEditUnidad.nombre_unidad" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Sede Asociada</label>
            <select v-model="formEditUnidad.id_sede" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option v-for="s in sedes" :key="s.id_sede" :value="s.id_sede">{{ s.nombre_sede }}</option>
            </select>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="showEditUnidadModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancelar</button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL: CONFIRMAR ELIMINAR UNIDAD -->
    <div v-if="showConfirmDeleteUnidadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 text-center">
        <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-500">
          <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </span>
        <h3 class="text-base font-bold text-slate-800 mb-1">Eliminar Unidad Médica</h3>
        <p class="text-sm text-slate-500 mb-5">
          ¿Estás seguro de que deseas eliminar la unidad <strong>{{ unidadAccion?.nombre_unidad }}</strong>?
        </p>
        <div class="flex gap-2 justify-center">
          <button @click="showConfirmDeleteUnidadModal = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancelar</button>
          <button @click="ejecutarEliminarUnidad" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Sí, eliminar</button>
        </div>
      </div>
    </div>

    <!-- MODAL PREMIUM: REABRIR TICKET -->
    <div v-if="showModalReabrir" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-amber-500 text-xl font-bold">↺</span>
          <h3 class="text-lg font-bold text-slate-800">Reabrir Ticket</h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Esta acción cambiará el estado del ticket a "Asignada" y registrará una entrada en la bitácora indicando el motivo de la reapertura.</p>
        
        <form @submit.prevent="confirmarReabrir" class="flex flex-col gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Seleccionar Técnico Asignado</label>
            <select v-model="formReabrir.tecnico_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option :value="null" disabled>Seleccionar técnico...</option>
              <option v-for="t in tecnicos" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Motivo de Reapertura (mín. 5 caracteres)</label>
            <textarea v-model="formReabrir.motivo" required rows="3" placeholder="Ej. El equipo sigue presentando la misma falla intermitente tras la reparación..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
          </div>

          <div class="mt-2 flex justify-end gap-2">
            <button type="button" @click="showModalReabrir = false" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50" :disabled="reabriendo">
              Cancelar
            </button>
            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700 transition-colors" :disabled="reabriendo">
              {{ reabriendo ? 'Reabriendo...' : 'Reabrir Ticket' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Notification Toast System -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-[-1rem] opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="toast.show" class="fixed top-5 right-5 z-[100] flex w-full max-w-md overflow-hidden rounded-2xl border bg-white/95 backdrop-blur-md shadow-2xl transition-all duration-300"
           :class="{
             'border-emerald-100 shadow-emerald-100/20': toast.type === 'success',
             'border-rose-100 shadow-rose-100/20': toast.type === 'error',
             'border-blue-100 shadow-blue-100/20': toast.type === 'info',
             'border-amber-100 shadow-amber-100/20': toast.type === 'warning'
           }">
        <div class="flex items-center gap-3.5 p-4 w-full">
          <!-- Icon -->
          <div class="flex-shrink-0">
            <span v-if="toast.type === 'success'" class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </span>
            <span v-else-if="toast.type === 'error'" class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </span>
            <span v-else-if="toast.type === 'info'" class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </span>
            <span v-else class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </span>
          </div>
          <!-- Message -->
          <div class="flex-1 text-left">
            <h4 class="text-sm font-bold text-slate-800">
              {{ toast.type === 'success' ? 'Éxito' : toast.type === 'error' ? 'Error' : 'Notificación' }}
            </h4>
            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ toast.message }}</p>
          </div>
          <!-- Close Button -->
          <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';
import tecnicosService from '../../services/tecnicosService';
import authService from '../../services/authService';
import api from '../../services/api';
import { Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const authStore = useAuthStore();
const router = useRouter();

const userInitial = computed(() => (authStore.user?.nombre ?? 'A').charAt(0).toUpperCase());

const rolMostrar = computed(() => {
  const r = authStore.user?.rol;
  if (!r) return 'Usuario';
  if (r === 'admin') return 'Administrador';
  if (r === 'coordinador') return 'Coordinador';
  return r.charAt(0).toUpperCase() + r.slice(1);
});

const tituloEncabezado = computed(() => {
  return esCoordinador.value 
    ? 'Panel de Gestión y Coordinación' 
    : 'Panel de Administración y Reportes';
});

const tituloDashboard = computed(() => {
  return esCoordinador.value
    ? 'Dashboard de Coordinación'
    : 'Dashboard Principal';
});

const subtituloDashboard = computed(() => {
  return esCoordinador.value
    ? 'Hospital Salud Integral — Control operativo de solicitudes y equipo técnico.'
    : 'Hospital Salud Integral — Monitoreo general de mantenimiento.';
});

// Variables reactivas de navegación
const seccionActiva = ref('dashboard');
const sidebarColapsado = ref(false);
const showRangoMenu = ref(false);
const rangoDias = ref(0); // 0 = Todo, 7, 30, 90
const buscarQuery = ref('');       // filtro interno (no usado en header)
const buscarGlobal = ref('');      // buscador global del header (independiente)
const mostrarDropdownBuscar = ref(false);
const solicitudes = ref([]);
const resumenData = ref({ pendientes: 0, en_proceso: 0, completadas: 0, total: 0 });

// Variables de coordinación (para el rol Coordinador y Administrador en dashboard compartido)
const solicitudesCoord = ref([]);
const tecnicos = ref([]);
const tecnicosCarga = ref([]);
const cambiosPendientes = ref({});
const totalSolicitudesCoord = ref(0);
const paginaActualCoord = ref(1);
const totalPaginasCoord = ref(1);
const busquedaCoord = ref('');
const loadingCoord = ref(false);
const errorCoord = ref('');
const guardandoTodosCoord = ref(false);

const resumenCoord = ref({
  pendientes: 0,
  en_proceso: 0,
  personal_disponible: 8,
  delta_pendientes: 5,
  delta_en_proceso: -2,
  delta_personal: 0,
});

// Notificaciones premium del Admin / Coordinador
const showNotificaciones = ref(false);
const notificaciones = ref([]);

const notificacionesNoLeidas = computed(() => {
  return (notificaciones.value || []).filter(n => !n.leida);
});

// IDs de notificaciones ya marcadas como leidas, persistidas en localStorage
const STORAGE_KEY_LEIDAS = 'admin_notif_leidas';
function getNotifLeidas() {
  try { return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY_LEIDAS) || '[]')); }
  catch { return new Set(); }
}
function setNotifLeidas(ids) {
  localStorage.setItem(STORAGE_KEY_LEIDAS, JSON.stringify([...ids]));
}

function toggleNotificaciones() {
  showNotificaciones.value = !showNotificaciones.value;
}

function limpiarNotificaciones() {
  // Persistir todos los IDs actuales como leidos sin eliminar los elementos de la lista
  const leidas = getNotifLeidas();
  notificaciones.value.forEach(n => {
    leidas.add(n.id);
    n.leida = true;
  });
  setNotifLeidas(leidas);
}

function clickNotificacion(n) {
  // Marcar esta notificacion individual como leida manteniendola en el historial
  const leidas = getNotifLeidas();
  leidas.add(n.id);
  setNotifLeidas(leidas);
  n.leida = true;
  showNotificaciones.value = false;
  if (n.solicitudId) router.push(`/solicitudes/${n.solicitudId}`);
}

// Alert Toast system
const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

function showToast(message, type = 'success') {
  toast.value.message = message;
  toast.value.type = type;
  toast.value.show = true;
  setTimeout(() => {
    toast.value.show = false;
  }, 4000);
}

// Reapertura de Tickets
const showModalReabrir = ref(false);
const reabriendo = ref(false);
const solicitudParaReabrir = ref(null);
const formReabrir = ref({
  tecnico_id: null,
  motivo: ''
});

function abrirModalReabrir(sol) {
  solicitudParaReabrir.value = sol;
  formReabrir.value.tecnico_id = sol.tecnico_id || null;
  formReabrir.value.motivo = '';
  showModalReabrir.value = true;
}

async function confirmarReabrir() {
  if (!solicitudParaReabrir.value) return;
  if (!formReabrir.value.tecnico_id) {
    showToast('Seleccione un técnico para reasignar el ticket.', 'warning');
    return;
  }
  if (formReabrir.value.motivo.trim().length < 5) {
    showToast('El motivo debe tener al menos 5 caracteres.', 'warning');
    return;
  }

  reabriendo.value = true;
  try {
    await solicitudesService.reabrir(solicitudParaReabrir.value.id, {
      tecnico_id: formReabrir.value.tecnico_id,
      motivo: formReabrir.value.motivo.trim()
    });
    showModalReabrir.value = false;
    showToast('El ticket se ha reabierto y reasignado exitosamente.', 'success');
    await cargarSolicitudesCoord(paginaActualCoord.value);
  } catch (err) {
    console.error('[confirmarReabrir] Error al reabrir:', err);
    showToast(err.response?.data?.mensaje || 'No se pudo reabrir el ticket.', 'error');
  } finally {
    reabriendo.value = false;
  }
}

// Variables y Carga de Logs de Auditoria
const logsAuditoria = ref([]);
const totalLogs = ref(0);
const paginaActualLogs = ref(1);
const totalPaginasLogs = ref(1);
const loadingLogs = ref(false);

function formatFechaHora(fechaStr) {
  if (!fechaStr) return '';
  const date = new Date(fechaStr);
  return date.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

async function cargarLogsAuditoria(pagina = 1) {
  loadingLogs.value = true;
  paginaActualLogs.value = pagina;
  try {
    const response = await api.get(`/coordinador/auditoria/logs?page=${pagina}`);
    logsAuditoria.value = response.data.data || [];
    totalPaginasLogs.value = response.data.last_page || 1;
    totalLogs.value = response.data.total || 0;
  } catch (e) {
    console.error('Error al cargar logs de auditoria:', e);
  } finally {
    loadingLogs.value = false;
  }
}

// Listas de datos de Administración
const listadoUsuarios = ref([]);
const sedes = ref([]);
const unitsBySede = ref({});
const roles = ref([]);
const buscarUsuarioQuery = ref('');

// Modales y Formularios
const showUserModal = ref(false);
const showSedeModal = ref(false);
const showUnidadModal = ref(false);

const formUser = ref({ nombre: '', apellido: '', correo: '', password: '', id_rol: '', id_sede: '', area_servicio: '' });
const formSede = ref({ nombre_sede: '', direccion: '' });
const formUnidad = ref({ nombre_unidad: '', id_sede: '' });

const esRolOperativo = ref(false);
const esTecnico = ref(false);
function alCambiarRol() {
  if (!formUser.value.id_rol) {
    esRolOperativo.value = false;
    esTecnico.value = false;
    return;
  }
  const rolObj = roles.value.find(r => r.id_rol === formUser.value.id_rol);
  esRolOperativo.value = !!(rolObj && ['Técnico', 'Coordinador', 'Solicitante'].includes(rolObj.nombre_rol));
  esTecnico.value = !!(rolObj && rolObj.nombre_rol === 'Técnico');
  if (!esTecnico.value) formUser.value.area_servicio = '';
}


// Variables para Nueva Solicitud inline
const showNuevaSolicitudModal = ref(false);
const enviandoSolicitud = ref(false);
const errorSolicitud = ref('');
const exitoSolicitud = ref('');
const tiposServicio = ['Eléctrico', 'Plomería', 'Aire Acondicionado', 'Equipo Médico', 'Limpieza', 'Otro'];
const formSolicitud = ref({ tipoServicio: '', ubicacion: '', descripcion: '', archivo: null });

const rangosDisponibles = [
  { dias: 0, texto: 'Todo el histórico' },
  { dias: 7, texto: 'Últimos 7 días' },
  { dias: 30, texto: 'Últimos 30 días' },
  { dias: 90, texto: 'Últimos 90 días' },
];

const rangoTexto = computed(() => {
  const rango = rangosDisponibles.find(r => r.dias === rangoDias.value);
  return rango ? rango.texto : 'Todo el histórico';
});

const esAdmin = computed(() => {
  const rol = authStore.user?.rol;
  if (!rol) return false;
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  return r === 'administrador' || r === 'admin';
});

const esCoordinador = computed(() => {
  const rol = authStore.user?.rol;
  if (!rol) return false;
  const r = rol.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  return r === 'coordinador';
});

const navItems = ref([]);

function inicializarNavItems() {
  const items = [
    {
      label: 'Dashboard',
      active: seccionActiva.value === 'dashboard',
      iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>`
    },
    {
      label: 'Coordinación',
      active: seccionActiva.value === 'coordinacion',
      iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>`
    },
    {
      label: 'Técnicos',
      active: seccionActiva.value === 'tecnicos',
      iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" /></svg>`
    }
  ];

  if (esAdmin.value) {
    items.push(
      {
        label: 'Usuarios',
        active: seccionActiva.value === 'usuarios',
        iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`
      },
      {
        label: 'Departamentos',
        active: seccionActiva.value === 'departamentos',
        iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`
      }
    );
  }

  items.push(
    {
      label: 'Reportes',
      active: seccionActiva.value === 'reportes',
      iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2z" /></svg>`
    },
    {
      label: 'Auditoría',
      active: seccionActiva.value === 'auditoria',
      iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>`
    }
  );

  navItems.value = items;
}

function toggleSidebar() {
  sidebarColapsado.value = !sidebarColapsado.value;
}

function seleccionarRango(dias) {
  rangoDias.value = dias;
  showRangoMenu.value = false;
}

// Navegación reactiva interna por secciones
function navegarA(item) {
  const seccion = item.label.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  
  // Protección defensiva en el frontend: Bloquear navegación si no es Admin
  if ((seccion === 'usuarios' || seccion === 'departamentos') && !esAdmin.value) {
    seccionActiva.value = 'dashboard';
    inicializarNavItems();
    return;
  }
  
  seccionActiva.value = seccion;
  
  if (seccionActiva.value === 'dashboard') {
    cargarDatosDashboard();
  } else if (seccionActiva.value === 'coordinacion') {
    cargarSolicitudesCoord(1);
    cargarTecnicos();
  } else if (seccionActiva.value === 'tecnicos') {
    cargarTecnicos();
  } else if (seccionActiva.value === 'usuarios') {
    cargarUsuarios();
  } else if (seccionActiva.value === 'departamentos') {
    cargarDepartamentos();
  } else if (seccionActiva.value === 'auditoria') {
    cargarLogsAuditoria(1);
  }
  inicializarNavItems();
}

// Bug fix: Configuración abre el panel de configuración interno
function irAConfiguracion() {
  seccionActiva.value = 'configuracion';
  inicializarNavItems();
}

function crearNuevaSolicitud() {
  // Abrimos el modal de nueva solicitud en lugar de navegar fuera
  showNuevaSolicitudModal.value = true;
}

function verTodasLasSolicitudes() {
  const itemCoord = navItems.value.find(i => i.label === 'Coordinación');
  if (itemCoord) {
    navegarA(itemCoord);
  } else {
    seccionActiva.value = 'coordinacion';
    cargarSolicitudesCoord(1);
    cargarTecnicos();
    inicializarNavItems();
  }
}

function verDetalle(id) {
  router.push(`/solicitudes/${id}`);
}

async function cerrarSesion() {
  await authStore.logout();
  router.push('/login');
}

async function cargarNotificaciones() {
  try {
    const leidas = getNotifLeidas();
    const nuevasNotif = [];

    // 1. Consultar logs de auditoría para cancelaciones de técnicos
    try {
      const resLogs = await api.get('/coordinador/auditoria/logs', { params: { per_page: 50 } });
      const logs = resLogs.data?.data || (Array.isArray(resLogs.data) ? resLogs.data : []);
      
      logs.forEach(log => {
        if (log.campo_modificado === 'cancelacion_tecnico') {
          const idNotif = `notif-cancel-${log.id_historial}`;
          const fechaStr = log.created_at ? formatFecha(log.created_at) : 'Reciente';
          nuevasNotif.unshift({
            id: idNotif,
            solicitudId: log.id_solicitud,
            tipo: 'cancelacion',
            mensaje: `⚠️ ${log.valor_nuevo || 'Un técnico canceló la tarea asignada.'} (Ticket #${abreviarId(log.id_solicitud)})`,
            tiempo: fechaStr,
            leida: leidas.has(idNotif)
          });
        }
      });
    } catch (e) {
      console.warn('[PanelAdmin] No se pudieron cargar logs de auditoría:', e);
    }

    // 2. Consultar historial de solicitudes para reportes, pausas y pendientes
    try {
      const resHistorial = await api.get('/coordinador/solicitudes/historial', { params: { per_page: 100 } });
      const historial = Array.isArray(resHistorial.data) ? resHistorial.data : (resHistorial.data?.data || []);

      historial.forEach(sol => {
        // Pausadas
        if (sol.asignacion?.is_pausada) {
          const idNotif = `notif-pausa-${sol.id_solicitud}`;
          const tec = sol.asignacion.tecnico ? `${sol.asignacion.tecnico.nombre} ${sol.asignacion.tecnico.apellido}` : 'asignado';
          nuevasNotif.push({
            id: idNotif,
            solicitudId: sol.id_solicitud,
            tipo: 'pendiente',
            mensaje: `El técnico ${tec} pausó el ticket #SOL-${abreviarId(sol.id_solicitud)}. Motivo: ${sol.asignacion.motivo_pausa || 'En espera de repuestos'}.`,
            tiempo: 'Pausada',
            leida: leidas.has(idNotif)
          });
        }

        // Bitácoras con [REPORTE DE PROBLEMA]
        const bitacoras = sol.asignacion?.bitacoras || [];
        bitacoras.forEach(b => {
          if (b.descripcion_trabajo && b.descripcion_trabajo.startsWith('[REPORTE DE PROBLEMA]')) {
            const idNotif = `notif-reporte-${b.id_bitacora}`;
            const detalle = b.descripcion_trabajo.replace('[REPORTE DE PROBLEMA] ', '');
            nuevasNotif.unshift({
              id: idNotif,
              solicitudId: sol.id_solicitud,
              tipo: 'reporte',
              mensaje: `Reporte de técnico en #${abreviarId(sol.id_solicitud)}: ${detalle}`,
              tiempo: 'Reporte',
              leida: leidas.has(idNotif)
            });
          }
        });

        // Pendientes que requieren asignación
        if (sol.estado_solicitud === 'Abierta' || sol.estado_solicitud === 'Devuelta') {
          const idNotif = `notif-abierta-${sol.id_solicitud}`;
          const dpto = sol.unidad ? sol.unidad.nombre_unidad : 'Sin área';
          nuevasNotif.push({
            id: idNotif,
            solicitudId: sol.id_solicitud,
            tipo: 'pendiente',
            mensaje: `Solicitud #${abreviarId(sol.id_solicitud)} en ${dpto} requiere asignación.`,
            tiempo: sol.fecha_apertura ? formatFecha(sol.fecha_apertura) : 'Pendiente',
            leida: leidas.has(idNotif)
          });
        }
      });
    } catch (repErr) {
      console.warn('[PanelAdmin] No se pudieron cargar solicitudes para notificaciones:', repErr);
    }

    // Ordenar: no leídas primero y luego por novedad
    nuevasNotif.sort((a, b) => {
      if (a.leida === b.leida) return 0;
      return a.leida ? 1 : -1;
    });

    notificaciones.value = nuevasNotif;
  } catch (e) {
    console.error('Error cargando notificaciones:', e);
  }
}

// CARGA DE DATOS DESDE EL BACKEND
async function cargarDatosDashboard() {
  try {
    const res = await solicitudesService.resumen();
    if (res) {
      resumenData.value = res;
    }
    const list = await solicitudesService.listar();
    if (list) {
      const arraySolicitudes = Array.isArray(list) ? list : (list.data || []);
      solicitudes.value = arraySolicitudes;
    }
    await cargarNotificaciones();
  } catch (e) {
    console.error('Error cargando reportes:', e);
  }
}

// Cargar Usuarios
async function cargarUsuarios() {
  try {
    const res = await api.get('/admin/usuarios');
    // Laravel devuelve paginado: { current_page, data: [...], total, ... }
    listadoUsuarios.value = res.data.data || res.data.usuarios || res.data || [];
    
    const resRoles = await api.get('/catalogos/roles');
    roles.value = resRoles.data || [];
  } catch (e) {
    console.error('Error cargando usuarios:', e);
  }
}

// Cargar Sedes y Unidades
async function cargarDepartamentos() {
  try {
    const resSedes = await api.get('/catalogos/sedes');
    sedes.value = resSedes.data || [];
    
    // Bug fix: reemplazar el objeto completo para garantizar reactividad de Vue 3
    const nuevoMap = {};
    for (const s of sedes.value) {
      try {
        const resUn = await api.get(`/catalogos/sedes/${s.id_sede}/unidades`);
        nuevoMap[s.id_sede] = resUn.data || [];
      } catch {
        nuevoMap[s.id_sede] = [];
      }
    }
    unitsBySede.value = { ...nuevoMap };
  } catch (e) {
    console.error('Error cargando sedes/unidades:', e);
  }
}

onMounted(async () => {
  try {
    await authStore.fetchUser();
  } catch (err) {
    console.error('[PanelAdminView.vue] Error al refrescar el usuario autenticado:', err);
  }
  inicializarNavItems();
  cargarDatosDashboard();
  
  if (seccionActiva.value === 'coordinacion' || esCoordinador.value) {
    cargarSolicitudesCoord(1);
    cargarTecnicos();
  }
  
  // Cargar notificaciones del sistema siempre
  cargarNotificaciones();

  // Cargar catálogos iniciales por si acaso
  api.get('/catalogos/sedes').then(res => sedes.value = res.data || []).catch(console.error);
  // Cargar usuarios en background para que la búsqueda global funcione desde cualquier sección
  cargarUsuarios();

  // Cerrar buscador al hacer clic fuera
  document.addEventListener('click', (e) => {
    const buscadorEl = document.getElementById('buscador-global');
    if (buscadorEl && !buscadorEl.contains(e.target)) {
      mostrarDropdownBuscar.value = false;
    }
  });
});

// FILTRADOS REACTIVOS DE USUARIOS Y TICKETS
const usuariosFiltrados = computed(() => {
  const query = buscarUsuarioQuery.value.toLowerCase().trim();
  if (!query) return listadoUsuarios.value;
  return listadoUsuarios.value.filter(u => 
    (u.nombre && u.nombre.toLowerCase().includes(query)) ||
    (u.apellido && u.apellido.toLowerCase().includes(query)) ||
    (u.correo && u.correo.toLowerCase().includes(query))
  );
});

const solicitudesFiltradas = computed(() => {
  let list = solicitudes.value;

  if (rangoDias.value > 0) {
    const limite = new Date();
    limite.setDate(limite.getDate() - rangoDias.value);
    list = list.filter(s => {
      if (!s.fecha) return false;
      const f = new Date(s.fecha);
      return f >= limite;
    });
  }

  return list;
});

const actividadReciente = computed(() => {
  return solicitudesFiltradas.value.slice(0, 10);
});

// Lista consolidada de todas las solicitudes conocidas en el sistema
const solicitudesTodas = computed(() => {
  const map = new Map();
  (solicitudes.value || []).forEach(s => { if (s && s.id) map.set(s.id, s); });
  (solicitudesCoord.value || []).forEach(s => { if (s && s.id && !map.has(s.id)) map.set(s.id, s); });
  return Array.from(map.values());
});

// Filtro reactivo en vivo para la tabla de Coordinación
const solicitudesCoordFiltradas = computed(() => {
  const q = busquedaCoord.value.trim().toLowerCase()
    .replace(/^#(sol|req)-/i, '').replace(/^#/i, '');
  if (!q) return solicitudesCoord.value || [];

  return (solicitudesCoord.value || []).filter(s => {
    const folio = abreviarId(s.id).toLowerCase();
    const tecnico = (s.tecnico_nombre || '').toLowerCase();
    return (
      folio.includes(q) ||
      `sol-${folio}`.includes(q) ||
      (s.id && s.id.toString().toLowerCase().includes(q)) ||
      (s.titulo && s.titulo.toLowerCase().includes(q)) ||
      (s.departamento && s.departamento.toLowerCase().includes(q)) ||
      (s.estado && s.estado.toLowerCase().includes(q)) ||
      (s.prioridad && s.prioridad.toLowerCase().includes(q)) ||
      tecnico.includes(q) ||
      (s.fecha && s.fecha.includes(q))
    );
  });
});

// BÚSQUEDA GLOBAL — busca sobre TODAS las solicitudes y usuarios
const resultadosBusqueda = computed(() => {
  const raw = buscarGlobal.value.trim().toLowerCase();
  // Normalizar: quitar prefijos #SOL- #REQ- # para comparar solo el código
  const q = raw.replace(/^#(sol|req)-/i, '').replace(/^#/i, '');
  if (!q || q.length < 1) return [];

  const resultados = [];

  // 1. Buscar en todas las solicitudes disponibles
  const fuente = solicitudesTodas.value;
  fuente.forEach(s => {
    const folio = abreviarId(s.id).toLowerCase(); // AGHRWMXPG
    const match = (
      folio.includes(q) ||
      `sol-${folio}`.includes(q) ||
      `req-${folio}`.includes(q) ||
      (s.id && s.id.toString().toLowerCase().includes(q)) ||
      (s.titulo && s.titulo.toLowerCase().includes(q)) ||
      (s.departamento && s.departamento.toLowerCase().includes(q)) ||
      (s.area && s.area.toLowerCase().includes(q)) ||
      (s.estado && s.estado.toLowerCase().includes(q)) ||
      (s.prioridad && s.prioridad.toLowerCase().includes(q)) ||
      (s.descripcion && s.descripcion.toLowerCase().includes(q)) ||
      (s.solicitante && s.solicitante.toLowerCase().includes(q)) ||
      (s.nombre_solicitante && s.nombre_solicitante.toLowerCase().includes(q)) ||
      (s.tecnico_nombre && s.tecnico_nombre.toLowerCase().includes(q)) ||
      (s.tipo && s.tipo.toLowerCase().includes(q)) ||
      (s.ubicacion && s.ubicacion.toLowerCase().includes(q))
    );
    if (match) resultados.push({ ...s, _tipo: 'ticket', _folio: `#SOL-${abreviarId(s.id)}` });
  });

  // 2. Buscar en personas (usuarios)
  const personas = listadoUsuarios.value || [];
  personas.forEach(u => {
    const nombreCompleto = `${u.nombre || ''} ${u.apellido || ''}`.toLowerCase().trim();
    const match = (
      nombreCompleto.includes(q) ||
      (u.nombre && u.nombre.toLowerCase().includes(q)) ||
      (u.apellido && u.apellido.toLowerCase().includes(q)) ||
      (u.correo && u.correo.toLowerCase().includes(q)) ||
      (u.rol && u.rol.toLowerCase().includes(q))
    );
    if (match) resultados.push({ ...u, _tipo: 'persona' });
  });

  return resultados.slice(0, 10);
});

const cerrarBuscador = () => {
  mostrarDropdownBuscar.value = false;
};

const ejecutarBusqueda = () => {
  if (resultadosBusqueda.value.length === 1) {
    const item = resultadosBusqueda.value[0];
    if (item._tipo === 'ticket') {
      irATicket(item);
    } else {
      irAUsuario(item);
    }
  } else {
    mostrarDropdownBuscar.value = true;
  }
};

const irATicket = (sol) => {
  cerrarBuscador();
  buscarGlobal.value = '';
  // Redirigir directamente al detalle del ticket para máxima utilidad
  verDetalle(sol.id);
};

const irAUsuario = (usuario) => {
  cerrarBuscador();
  buscarGlobal.value = '';
  if (esAdmin.value) {
    seccionActiva.value = 'usuarios';
    nextTick(() => {
      buscarUsuarioQuery.value = `${usuario.nombre || ''} ${usuario.apellido || ''}`.trim();
    });
  } else {
    showToast(`Usuario: ${usuario.nombre} ${usuario.apellido} (${usuario.rol || 'Usuario'})`, "info");
  }
};

const badgeEstadoSearch = (estado) => {
  const map = {
    'Pendiente': 'bg-amber-100 text-amber-700',
    'En Proceso': 'bg-blue-100 text-blue-700',
    'Completada': 'bg-emerald-100 text-emerald-700',
    'Cancelada': 'bg-slate-100 text-slate-500',
    'Cerrada': 'bg-slate-100 text-slate-500',
  };
  return map[estado] || 'bg-slate-100 text-slate-600';
};

// TARJETAS Y KPIs DINÁMICOS
const stats = computed(() => {
  const list = solicitudesFiltradas.value;
  const total = list.length;
  const criticas = list.filter(s => s.prioridad === 'Alta').length;
  
  const completadas = list.filter(s => s.estado === 'Completada').length;
  const pctEficiencia = total > 0 ? Math.round((completadas / total) * 100) : 0;

  return [
    {
      label: 'SOLICITUDES TOTALES',
      description: 'Total de solicitudes registradas en el sistema',
      value: total.toLocaleString(),
      trend: '↗ +12.5%',
      trendLabel: 'vs mes anterior',
      trendColor: 'text-emerald-500',
      iconBg: 'bg-blue-50',
      iconSvg: `<svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>`
    },
    {
      label: 'PENDIENTES CRÍTICAS',
      description: 'Solicitudes de prioridad alta sin resolver',
      value: criticas.toString(),
      trend: '↗ +5%',
      trendLabel: 'Atención requerida',
      trendColor: 'text-rose-500',
      iconBg: 'bg-rose-50',
      iconSvg: `<svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`
    },
    {
      label: 'TIEMPO DE RESPUESTA',
      description: 'Promedio desde creación hasta primera atención',
      value: '2.4 hrs',
      trend: '↘ -10%',
      trendLabel: 'Más rápido',
      trendColor: 'text-emerald-500',
      iconBg: 'bg-amber-50',
      iconSvg: `<svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
    },
    {
      label: 'EFICIENCIA OPERATIVA',
      description: 'Porcentaje de solicitudes completadas a tiempo',
      value: `${pctEficiencia}%`,
      trend: '↗ +2.1%',
      trendLabel: 'Nivel óptimo',
      trendColor: 'text-emerald-500',
      iconBg: 'bg-indigo-50',
      iconSvg: `<svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>`
    }
  ];
});

const efficiencyData = computed(() => {
  const list = solicitudesFiltradas.value;
  const total = list.length;
  if (total === 0) {
    return [
      { name: 'Completadas', value: 0, color: '#2563eb' },
      { name: 'En Proceso', value: 0, color: '#f59e0b' },
      { name: 'Pendientes', value: 0, color: '#e2e8f0' }
    ];
  }
  const comp = list.filter(s => s.estado === 'Completada').length;
  const proc = list.filter(s => s.estado === 'En Proceso').length;
  const pend = list.filter(s => s.estado === 'Pendiente').length;

  return [
    { name: 'Completadas', value: Math.round((comp / total) * 100), color: '#2563eb' },
    { name: 'En Proceso', value: Math.round((proc / total) * 100), color: '#f59e0b' },
    { name: 'Pendientes', value: Math.round((pend / total) * 100), color: '#e2e8f0' },
  ];
});

const tasaEficienciaGeneral = computed(() => {
  const total = solicitudesFiltradas.value.length;
  if (total === 0) return 0;
  const comp = solicitudesFiltradas.value.filter(s => s.estado === 'Completada').length;
  return Math.round((comp / total) * 100);
});

// Estilos de la tabla de actividades
const priorityDot = { Alta: 'bg-rose-500', Media: 'bg-amber-500', Baja: 'bg-blue-500' };
const statusStyles = { 'En Proceso': 'bg-blue-50 text-blue-600', Completada: 'bg-emerald-50 text-emerald-600', Pendiente: 'bg-slate-100 text-slate-500' };

// Gráficos calculados dinámicamente
const barData = computed(() => {
  const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN'];
  const mantenimiento = [0, 0, 0, 0, 0, 0];
  const reparaciones = [0, 0, 0, 0, 0, 0];

  solicitudesFiltradas.value.forEach(s => {
    if (!s.fecha) return;
    const fechaObj = new Date(s.fecha);
    const mesIdx = fechaObj.getMonth();
    // Mapear los meses ENE-JUN (0 a 5)
    if (mesIdx >= 0 && mesIdx <= 5) {
      if (s.subtitulo === 'Mantenimiento' || s.subtitulo === 'Soporte Técnico') {
        mantenimiento[mesIdx]++;
      } else {
        reparaciones[mesIdx]++;
      }
    }
  });

  return {
    labels: meses,
    datasets: [
      { label: 'Mantenimiento', data: mantenimiento, backgroundColor: '#2563eb', borderRadius: 4, barThickness: 14 },
      { label: 'Reparaciones', data: reparaciones, backgroundColor: '#cbd5e1', borderRadius: 4, barThickness: 14 },
    ]
  };
});

const barOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { display: false } } };
const doughnutData = computed(() => ({
  labels: efficiencyData.value.map(e => e.name),
  datasets: [{ data: efficiencyData.value.map(e => e.value), backgroundColor: efficiencyData.value.map(e => e.color), borderWidth: 0 }]
}));
const doughnutOptions = { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } };

// GESTIÓN E INTERACCIÓN DE MODALES DE ADMINISTRACIÓN

function abrirModalCrearUsuario() {
  formUser.value = { nombre: '', apellido: '', correo: '', password: '', id_rol: '', id_sede: '' };
  esRolOperativo.value = false;
  showUserModal.value = true;
}

async function guardarUsuario() {
  try {
    await api.post('/admin/usuarios', formUser.value);
    showUserModal.value = false;
    showToast("Usuario creado correctamente.", "success");
    cargarUsuarios();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al crear usuario.", "error");
  }
}

async function cambiarEstado(u) {
  try {
    const action = u.estado === 'Activo' ? 'deshabilitar' : 'habilitar';
    await api.patch(`/admin/usuarios/${u.id ?? u.id_usuario}/${action}`);
    showToast(`Usuario ${action === 'habilitar' ? 'habilitado' : 'deshabilitado'} correctamente.`, "success");
    cargarUsuarios();
  } catch (e) {
    showToast("Error al cambiar el estado del usuario.", "error");
  }
}

// ─── EDITAR USUARIO ────────────────────────────────────────────
const showEditUserModal = ref(false);
const showConfirmGuardarUser = ref(false);
const editUserForm = ref({ id: null, nombre: '', apellido: '', correo: '', id_rol: '', password: '' });

function abrirModalEditarUsuario(u) {
  editUserForm.value = {
    id: u.id ?? u.id_usuario,
    nombre: u.nombre ?? u.nombre_solo,
    apellido: u.apellido ?? '',
    correo: u.correo ?? '',
    id_rol: u.id_rol ?? roles.value.find(r => r.nombre_rol === u.rol)?.id_rol ?? '',
    password: ''
  };
  showConfirmGuardarUser.value = false;
  showEditUserModal.value = true;
}

async function guardarEdicionUsuario() {
  try {
    const payload = {
      correo: editUserForm.value.correo,
      id_rol: editUserForm.value.id_rol,
    };
    if (editUserForm.value.password?.trim()) payload.password = editUserForm.value.password;
    await api.patch(`/admin/usuarios/${editUserForm.value.id}/editar`, payload);
    showEditUserModal.value = false;
    showConfirmGuardarUser.value = false;
    showToast("Usuario actualizado correctamente.", "success");
    cargarUsuarios();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al actualizar el usuario.", "error");
    showConfirmGuardarUser.value = false;
  }
}

// ─── CONFIRMAR DESHABILITAR / HABILITAR ────────────────────────
const showConfirmEstadoModal = ref(false);
const usuarioAccion = ref(null);

function confirmarCambiarEstado(u) {
  usuarioAccion.value = u;
  showConfirmEstadoModal.value = true;
}

async function ejecutarCambiarEstado() {
  showConfirmEstadoModal.value = false;
  await cambiarEstado(usuarioAccion.value);
  usuarioAccion.value = null;
}

// ─── CONFIRMAR ELIMINAR USUARIO ─────────────────────────────────
const showConfirmDeleteUserModal = ref(false);

function confirmarEliminarUsuario(u) {
  usuarioAccion.value = u;
  showConfirmDeleteUserModal.value = true;
}

async function ejecutarEliminarUsuario() {
  try {
    const uid = usuarioAccion.value.id ?? usuarioAccion.value.id_usuario;
    await api.delete(`/admin/usuarios/${uid}`);
    showConfirmDeleteUserModal.value = false;
    showToast("Usuario eliminado permanentemente.", "success");
    usuarioAccion.value = null;
    cargarUsuarios();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al eliminar el usuario.", "error");
    showConfirmDeleteUserModal.value = false;
  }
}

function abrirModalCrearSede() {
  formSede.value = { nombre_sede: '', direccion: '' };
  showSedeModal.value = true;
}

async function guardarSede() {
  try {
    await api.post('/admin/catalogos/sedes', formSede.value);
    showSedeModal.value = false;
    showToast("Sede agregada al catálogo de salud.", "success");
    cargarDepartamentos();
  } catch (e) {
    showToast("Error al crear la sede.", "error");
  }
}

function abrirModalCrearUnidad() {
  formUnidad.value = { nombre_unidad: '', id_sede: sedes.value[0]?.id_sede || '' };
  showUnidadModal.value = true;
}

async function guardarUnidad() {
  try {
    await api.post('/admin/catalogos/unidades', formUnidad.value);
    showUnidadModal.value = false;
    showToast("Unidad médica añadida correctamente.", "success");
    cargarDepartamentos();
  } catch (e) {
    showToast("Error al crear la unidad.", "error");
  }
}

// ─── EDITAR Y ELIMINAR SEDES ──────────────────────────────────
const showEditSedeModal = ref(false);
const showConfirmDeleteSedeModal = ref(false);
const formEditSede = ref({ id_sede: null, nombre_sede: '', direccion: '' });
const sedeAccion = ref(null);

function abrirEditarSede(sede) {
  formEditSede.value = {
    id_sede: sede.id_sede,
    nombre_sede: sede.nombre_sede,
    direccion: sede.direccion,
  };
  showEditSedeModal.value = true;
}

async function guardarEdicionSede() {
  try {
    await api.put(`/admin/catalogos/sedes/${formEditSede.value.id_sede}`, {
      nombre_sede: formEditSede.value.nombre_sede,
      direccion: formEditSede.value.direccion,
    });
    showEditSedeModal.value = false;
    showToast("Sede actualizada correctamente.", "success");
    cargarDepartamentos();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al actualizar la sede.", "error");
  }
}

function confirmarEliminarSede(sede) {
  sedeAccion.value = sede;
  showConfirmDeleteSedeModal.value = true;
}

async function ejecutarEliminarSede() {
  try {
    await api.delete(`/admin/catalogos/sedes/${sedeAccion.value.id_sede}`);
    showConfirmDeleteSedeModal.value = false;
    showToast("Sede eliminada correctamente.", "success");
    sedeAccion.value = null;
    cargarDepartamentos();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al eliminar la sede.", "error");
    showConfirmDeleteSedeModal.value = false;
  }
}

// ─── EDITAR Y ELIMINAR UNIDADES ────────────────────────────────
const showEditUnidadModal = ref(false);
const showConfirmDeleteUnidadModal = ref(false);
const formEditUnidad = ref({ id_unidad: null, nombre_unidad: '', id_sede: '' });
const unidadAccion = ref(null);

function abrirEditarUnidad(un) {
  formEditUnidad.value = {
    id_unidad: un.id_unidad,
    nombre_unidad: un.nombre_unidad,
    id_sede: un.id_sede,
  };
  showEditUnidadModal.value = true;
}

async function guardarEdicionUnidad() {
  try {
    await api.put(`/admin/catalogos/unidades/${formEditUnidad.value.id_unidad}`, {
      nombre_unidad: formEditUnidad.value.nombre_unidad,
      id_sede: formEditUnidad.value.id_sede,
    });
    showEditUnidadModal.value = false;
    showToast("Unidad médica actualizada.", "success");
    cargarDepartamentos();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al actualizar la unidad.", "error");
  }
}

function confirmarEliminarUnidad(un) {
  unidadAccion.value = un;
  showConfirmDeleteUnidadModal.value = true;
}

async function ejecutarEliminarUnidad() {
  try {
    await api.delete(`/admin/catalogos/unidades/${unidadAccion.value.id_unidad}`);
    showConfirmDeleteUnidadModal.value = false;
    showToast("Unidad médica eliminada.", "success");
    unidadAccion.value = null;
    cargarDepartamentos();
  } catch (e) {
    showToast(e.response?.data?.message || "Error al eliminar la unidad.", "error");
    showConfirmDeleteUnidadModal.value = false;
  }
}

// Guardar Nueva Solicitud (desde modal interno admin)
async function guardarNuevaSolicitud() {
  enviandoSolicitud.value = true;
  errorSolicitud.value = '';
  exitoSolicitud.value = '';
  try {
    await solicitudesService.crear({
      titulo: formSolicitud.value.tipoServicio,
      subtitulo: formSolicitud.value.descripcion,
      ubicacion: formSolicitud.value.ubicacion,
      fecha: new Date().toISOString().slice(0, 10),
      prioridad: 'Media',
      estado: 'Pendiente',
    });
    exitoSolicitud.value = 'Solicitud enviada correctamente.';
    formSolicitud.value = { tipoServicio: '', ubicacion: '', descripcion: '' };
    setTimeout(() => {
      showNuevaSolicitudModal.value = false;
      exitoSolicitud.value = '';
      cargarDatosDashboard();
    }, 1500);
  } catch (e) {
    errorSolicitud.value = 'No se pudo enviar la solicitud. Intenta de nuevo.';
  } finally {
    enviandoSolicitud.value = false;
  }
}

// Exportar Reporte Ejecutivo del Hospital en PDF Formal
function exportarPDF() {
  const doc = new jsPDF();

  const countPendientes = solicitudesFiltradas.value.filter(s => s.estado === 'Pendiente').length;
  const countEnProceso = solicitudesFiltradas.value.filter(s => s.estado === 'En Proceso').length;
  const countCompletadas = solicitudesFiltradas.value.filter(s => s.estado === 'Completada').length;
  const countTotal = solicitudesFiltradas.value.length;

  // Encabezado
  doc.setFontSize(24);
  doc.setFont('helvetica', 'bold');
  doc.setTextColor(0, 0, 0);
  doc.text("Hospital Salud Integral", 14, 20);

  doc.setFontSize(14);
  doc.setFont('helvetica', 'normal');
  doc.text("Reporte de Solicitudes de Mantenimiento", 14, 28);

  const hoy = new Date();
  const fechaGenerado = `${hoy.getDate()}/${hoy.getMonth() + 1}/${hoy.getFullYear()}`;
  doc.setFontSize(10);
  doc.setTextColor(100, 100, 100);
  doc.text(`Generado: ${fechaGenerado}`, 14, 35);
  doc.setTextColor(0, 0, 0);

  // Tabla resumen KPI
  autoTable(doc, {
    startY: 43,
    head: [['Pendientes', 'En Proceso', 'Completadas', 'Total']],
    body: [[countPendientes, countEnProceso, countCompletadas, countTotal]],
    theme: 'grid',
    headStyles: { 
      fillColor: [37, 99, 235],
      halign: 'left',
      textColor: [255, 255, 255],
      fontStyle: 'bold'
    },
    bodyStyles: { 
      halign: 'left',
      textColor: [50, 50, 50]
    },
    styles: { fontSize: 10, font: 'helvetica', cellPadding: 5 }
  });

  const finalY = doc.lastAutoTable.finalY + 12;
  doc.setFontSize(14);
  doc.setFont('helvetica', 'bold');
  doc.text("Detalle de Solicitudes", 14, finalY);

  // Formateador de fecha
  function formatFechaPDF(fechaStr) {
    if (!fechaStr) return '';
    const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    
    if (/[a-zA-Z]/.test(fechaStr)) {
      return fechaStr;
    }
    
    if (fechaStr.includes('-')) {
      const partes = fechaStr.split('-');
      if (partes.length === 3) {
        const mesNum = parseInt(partes[1], 10);
        const dia = parseInt(partes[2], 10);
        const anio = partes[0];
        const mesStr = meses[mesNum - 1] || partes[1];
        return `${dia} ${mesStr} ${anio}`;
      }
    }
    
    if (fechaStr.includes('/')) {
      const partes = fechaStr.split('/');
      if (partes.length === 3) {
        const dia = parseInt(partes[0], 10);
        const mesNum = parseInt(partes[1], 10);
        const anio = partes[2];
        const mesStr = meses[mesNum - 1] || partes[1];
        return `${dia} ${mesStr} ${anio}`;
      }
    }
    
    return fechaStr;
  }

  const headers = [["ID", "Servicio", "Ubicación", "Fecha", "Estado"]];
  const listData = solicitudesFiltradas.value.map(s => {
    let servicio = s.titulo;
    let ubicacion = s.departamento || s.ubicacion || '—';
    if (s.titulo && s.titulo.includes(' - ')) {
      const partes = s.titulo.split(' - ');
      servicio = partes[0].trim();
      ubicacion = partes[1].trim();
    }
    return [
      `#SOL-${abreviarId(s.id)}`,
      servicio,
      ubicacion,
      formatFechaPDF(s.fecha),
      s.estado
    ];
  });

  autoTable(doc, {
    startY: finalY + 5,
    head: headers,
    body: listData,
    theme: 'striped',
    headStyles: { fillColor: [37, 99, 235], fontStyle: 'bold', textColor: [255, 255, 255] },
    styles: { fontSize: 9, font: 'helvetica', cellPadding: 5 }
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
  inputName.value = 'reporte-solicitudes.pdf';
  form.appendChild(inputName);

  document.body.appendChild(form);
  form.submit();
  setTimeout(() => {
    document.body.removeChild(form);
  }, 1000);
}

// FUNCIONES DEL COORDINADOR (Integradas para el panel compartido)
async function cargarTecnicos() {
  try {
    const res = await api.get('/coordinador/tecnicos/carga-trabajo');
    tecnicosCarga.value = res.data || [];
    
    tecnicos.value = tecnicosCarga.value.map(t => ({
      id: t.id_usuario,
      name: `${t.nombre_completo} (${t.tickets_activos} tickets activos)`
    }));
  } catch (e) {
    console.error('No se pudieron cargar los técnicos', e);
  }
}

function registrarCambioCoord(solicitud, campo, valor) {
  if (!cambiosPendientes.value[solicitud.id]) {
    cambiosPendientes.value[solicitud.id] = {};
  }
  cambiosPendientes.value[solicitud.id][campo] = valor;
}

async function guardarCambiosCoord(solicitud) {
  const cambios = cambiosPendientes.value[solicitud.id];
  if (!cambios) return;

  console.group('[guardarCambiosCoord] Intentando guardar cambio');
  console.log('Solicitud ID:', solicitud.id);
  console.log('Cambios a aplicar:', JSON.stringify(cambios, null, 2));
  console.log('Rol del usuario:', authStore.user?.rol ?? authService.getUser()?.rol);
  console.groupEnd();

  try {
    await solicitudesService.actualizar(solicitud.id, cambios);
    delete cambiosPendientes.value[solicitud.id];
    await cargarSolicitudesCoord(paginaActualCoord.value);
    showToast("Cambio guardado con éxito.", "success");
  } catch (e) {
    console.error('[guardarCambiosCoord] ERROR al guardar:');
    console.error('Mensaje:', e?.message);
    console.error('Status HTTP:', e?.response?.status);
    console.error('Respuesta del servidor:', e?.response?.data);
    console.error('Error completo:', e);
    showToast(`No se pudo guardar el cambio. (${e?.response?.data?.mensaje || e?.message || 'Error desconocido'})`, "error");
  }
}

const hasChangesCoord = computed(() => Object.keys(cambiosPendientes.value).length > 0);

async function guardarTodosLosCambiosCoord() {
  guardandoTodosCoord.value = true;
  errorCoord.value = '';
  console.log('[guardarTodosLosCambiosCoord] Guardando', Object.keys(cambiosPendientes.value).length, 'cambios...');
  try {
    const promesas = [];
    for (const [id, cambios] of Object.entries(cambiosPendientes.value)) {
      console.log(`  -> Solicitud ${id}:`, cambios);
      promesas.push(solicitudesService.actualizar(id, cambios));
    }
    await Promise.all(promesas);
    cambiosPendientes.value = {};
    await cargarSolicitudesCoord(paginaActualCoord.value);
    showToast("Todos los cambios se han guardado con éxito.", "success");
  } catch (e) {
    console.error('[guardarTodosLosCambiosCoord] ERROR al guardar batch:');
    console.error('Mensaje:', e?.message);
    console.error('Status HTTP:', e?.response?.status);
    console.error('Respuesta del servidor:', e?.response?.data);
    console.error('Error completo:', e);
    showToast(`Error al guardar: ${e?.response?.data?.mensaje || e?.message || 'Error desconocido'}`, "error");
  } finally {
    guardandoTodosCoord.value = false;
  }
}

async function cargarSolicitudesCoord(pagina = 1) {
  loadingCoord.value = true;
  errorCoord.value = '';
  try {
    const params = { page: pagina };
    if (busquedaCoord.value) params.buscar = busquedaCoord.value;
    const [resumenData, listaData] = await Promise.all([
      solicitudesService.resumen(),
      solicitudesService.listar(params),
    ]);
    resumenCoord.value.pendientes = resumenData.pendientes;
    resumenCoord.value.en_proceso = resumenData.en_proceso;
    
    const rawList = listaData.data ?? [];
    solicitudesCoord.value = rawList.map(s => {
      const pending = cambiosPendientes.value[s.id];
      if (pending) {
        return { ...s, ...pending };
      }
      return s;
    });

    totalSolicitudesCoord.value = listaData.total ?? solicitudesCoord.value.length;
    totalPaginasCoord.value = listaData.last_page ?? 1;
    paginaActualCoord.value = listaData.current_page ?? 1;
    
    cargarNotificaciones();
  } catch (e) {
    console.error('[cargarSolicitudesCoord] ERROR al cargar solicitudes:');
    console.error('Mensaje:', e?.message);
    console.error('Status HTTP:', e?.response?.status);
    console.error('Respuesta del servidor:', e?.response?.data);
    console.error('Error completo:', e);
    errorCoord.value = `Error: ${e?.response?.data?.mensaje || e?.message || 'No se pudieron cargar las solicitudes.'}`;
  } finally {
    loadingCoord.value = false;
  }
}

function cambiarPaginaCoord(pagina) {
  if (pagina < 1 || pagina > totalPaginasCoord.value) return;
  cargarSolicitudesCoord(pagina);
}

let buscarTimeoutCoord = null;
function buscarCoord() {
  clearTimeout(buscarTimeoutCoord);
  buscarTimeoutCoord = setTimeout(() => cargarSolicitudesCoord(1), 400);
}

function prioridadClaseCoord(prioridad) {
  if (prioridad === 'Alta') return 'priority-alta';
  if (prioridad === 'Media') return 'priority-media';
  return 'priority-baja';
}

function estadoClaseCoord(estado) {
  if (estado === 'Pendiente') return 'status-pendiente';
  if (estado === 'En Proceso') return 'status-proceso';
  if (estado === 'Completada') return 'status-completada';
  return '';
}

function formatFecha(fechaStr) {
  if (!fechaStr) return '';
  const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
  
  if (/[a-zA-Z]/.test(fechaStr)) {
    return fechaStr;
  }
  
  if (fechaStr.includes('-')) {
    const partes = fechaStr.split('-');
    if (partes.length === 3) {
      const mesNum = parseInt(partes[1], 10);
      const dia = parseInt(partes[2], 10);
      const anio = partes[0];
      const mesStr = meses[mesNum - 1] || partes[1];
      return `${dia} ${mesStr} ${anio}`;
    }
  }
  
  if (fechaStr.includes('/')) {
    const partes = fechaStr.split('/');
    if (partes.length === 3) {
      const dia = parseInt(partes[0], 10);
      const mesNum = parseInt(partes[1], 10);
      const anio = partes[2];
      const mesStr = meses[mesNum - 1] || partes[1];
      return `${dia} ${mesStr} ${anio}`;
    }
  }
  
  return fechaStr;
}

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