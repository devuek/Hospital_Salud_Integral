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
            <p class="text-xs text-slate-400 whitespace-nowrap">Administrador</p>
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
          <h1 class="text-lg font-semibold text-slate-800">Panel de Administración y Reportes</h1>
        </div>

        <div class="flex items-center gap-4">
          <div class="relative">
            <input type="text" v-model="buscarQuery" placeholder="Buscar..."
              class="hidden w-56 rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-sm text-slate-600 placeholder:text-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:block"/>
          </div>
          
          <!-- Campana de Notificaciones Premium -->
          <div class="relative">
            <button @click="toggleNotificaciones" class="relative rounded-full p-2 text-slate-500 hover:bg-slate-50 focus:outline-none">
              <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <!-- Indicador de alerta roja si hay notificaciones pendientes -->
              <span v-if="notificaciones.length > 0" class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
              </span>
            </button>
            
            <!-- Dropdown de Notificaciones -->
            <div v-show="showNotificaciones" class="absolute right-0 mt-2 z-50 w-80 rounded-2xl border border-slate-100 bg-white py-2 shadow-xl">
              <div class="flex items-center justify-between border-b border-slate-100 px-4 pb-2 pt-1">
                <span class="text-xs font-bold text-slate-800">Notificaciones</span>
                <button @click="limpiarNotificaciones" class="text-[10px] font-semibold text-blue-600 hover:underline">Marcar leídas</button>
              </div>
              <div class="max-h-64 overflow-y-auto">
                <div v-for="n in notificaciones" :key="n.id" @click="clickNotificacion(n)" class="flex gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50/50 last:border-0 transition-colors">
                  <div class="flex-shrink-0 mt-0.5">
                    <span v-if="n.tipo === 'pendiente'" class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
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
                  <div class="flex flex-col gap-0.5">
                    <p class="text-xs text-slate-700 font-medium leading-normal">{{ n.mensaje }}</p>
                    <span class="text-[10px] text-slate-400 font-medium">{{ n.tiempo }}</span>
                  </div>
                </div>
                <div v-if="notificaciones.length === 0" class="py-8 text-center text-xs text-slate-400">
                  No tienes notificaciones pendientes.
                </div>
              </div>
            </div>
          </div>

          <button @click="crearNuevaSolicitud" class="rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            + Nueva Solicitud
          </button>
        </div>
      </header>

      <!-- Contenedor del Dashboard Dinámico -->
      <main class="flex flex-col gap-6 p-6">
        
        <!-- SECCIÓN 1: DASHBOARD DE REPORTES -->
        <div v-if="seccionActiva === 'dashboard'" class="flex flex-col gap-6">
          <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Principal</h1>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-sm text-slate-400">Hospital Salud Integral — Monitoreo en tiempo real de mantenimiento.</p>
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
                    <td class="px-6 py-4 font-semibold text-blue-600 hover:text-blue-700 transition-colors">#REQ-{{ a.id }}</td>
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
        <div v-if="seccionActiva === 'usuarios'" class="flex flex-col gap-4">
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
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                      <button @click="cambiarEstado(u)" class="text-xs font-medium text-blue-600 hover:underline">
                        {{ u.estado === 'Activo' ? 'Deshabilitar' : 'Habilitar' }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- SECCIÓN 3: GESTIÓN DE DEPARTAMENTOS -->
        <div v-if="seccionActiva === 'departamentos'" class="flex flex-col gap-4">
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
              <div class="mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ sede.nombre_sede }}</h2>
                <p class="text-xs text-slate-400">{{ sede.direccion }}</p>
              </div>
              <div class="flex flex-wrap gap-2">
                <span v-for="un in unitsBySede[sede.id_sede]" :key="un.id_unidad" 
                  class="rounded-lg border border-slate-150 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600">
                  {{ un.nombre_unidad }}
                </span>
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
                <span class="text-sm font-medium text-slate-700">Administrador Activo</span>
                <span class="text-sm text-slate-500">{{ authStore.user?.nombre ?? 'Administrador' }}</span>
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import solicitudesService from '../../services/solicitudesService';
import api from '../../services/api';
import { Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const authStore = useAuthStore();
const router = useRouter();

const userInitial = computed(() => (authStore.user?.nombre ?? 'A').charAt(0).toUpperCase());

// Variables reactivas de navegación
const seccionActiva = ref('dashboard');
const sidebarColapsado = ref(false);
const showRangoMenu = ref(false);
const rangoDias = ref(0); // 0 = Todo, 7, 30, 90
const buscarQuery = ref('');
const solicitudes = ref([]);
const resumenData = ref({ pendientes: 0, en_proceso: 0, completadas: 0, total: 0 });

// Notificaciones premium del Admin
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

// Ítems de navegación adaptados para no salir de /admin
const navItems = ref([
  {
    label: 'Dashboard',
    active: true,
    iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>`
  },
  {
    label: 'Usuarios',
    active: false,
    iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`
  },
  {
    label: 'Departamentos',
    active: false,
    iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`
  },
  {
    label: 'Reportes',
    active: false,
    iconSvg: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2z" /></svg>`
  },
]);

function toggleSidebar() {
  sidebarColapsado.value = !sidebarColapsado.value;
}

function seleccionarRango(dias) {
  rangoDias.value = dias;
  showRangoMenu.value = false;
}

// Navegación reactiva interna por secciones
function navegarA(item) {
  // Reset todos los activos primero
  navItems.value = navItems.value.map(i => ({ ...i, active: i.label === item.label }));
  
  if (item.label === 'Dashboard') {
    seccionActiva.value = 'dashboard';
    cargarDatosDashboard();
  } else if (item.label === 'Usuarios') {
    seccionActiva.value = 'usuarios';
    cargarUsuarios();
  } else if (item.label === 'Departamentos') {
    seccionActiva.value = 'departamentos';
    cargarDepartamentos();
  } else if (item.label === 'Reportes') {
    seccionActiva.value = 'reportes';
  }
}

// Bug fix: Configuración abre el panel de configuración interno
function irAConfiguracion() {
  navItems.value = navItems.value.map(i => ({ ...i, active: false }));
  seccionActiva.value = 'configuracion';
}

function crearNuevaSolicitud() {
  // Abrimos el modal de nueva solicitud en lugar de navegar fuera
  showNuevaSolicitudModal.value = true;
}

function verTodasLasSolicitudes() {
  navItems.value = navItems.value.map(i => ({ ...i, active: i.label === 'Dashboard' }));
  seccionActiva.value = 'dashboard';
  cargarDatosDashboard();
}

function verDetalle(id) {
  router.push(`/solicitudes/${id}`);
}

async function cerrarSesion() {
  await authStore.logout();
  router.push('/login');
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
      
      // Generar notificaciones dinámicas a partir de solicitudes Pendientes (Abiertas)
      const pendientes = arraySolicitudes.filter(s => s.estado === 'Pendiente');
      const nuevasNotif = [];
      pendientes.forEach((s, idx) => {
        nuevasNotif.push({
          id: `notif-${s.id}-${idx}`,
          solicitudId: s.id,
          tipo: 'pendiente',
          mensaje: `Nueva solicitud #${s.id.substring(0,8)}... en ${s.departamento || 'Sin área'} requiere asignación.`,
          tiempo: 'Hace unos minutos'
        });
      });
      
      // Notificación de ejemplo si no hay pendientes
      if (nuevasNotif.length === 0 && arraySolicitudes.length > 0) {
        nuevasNotif.push({
          id: 'notif-default',
          solicitudId: arraySolicitudes[0].id,
          tipo: 'preventivo',
          mensaje: 'Mantenimiento preventivo completado en Administración.',
          tiempo: 'Hace 1 hora'
        });
      }
      
      notificaciones.value = nuevasNotif;
    }
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

onMounted(() => {
  cargarDatosDashboard();
  // Cargar catálogos iniciales por si acaso
  api.get('/catalogos/sedes').then(res => sedes.value = res.data || []).catch(console.error);
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

  if (buscarQuery.value.trim() !== '') {
    const query = buscarQuery.value.toLowerCase().replace('#req-', '');
    list = list.filter(s => {
      const fullId = `#req-${s.id}`.toLowerCase();
      return (s.id && s.id.toString().toLowerCase().includes(query)) ||
             fullId.includes(query) ||
             (s.departamento && s.departamento.toLowerCase().includes(query)) ||
             (s.titulo && s.titulo.toLowerCase().includes(query)) ||
             (s.prioridad && s.prioridad.toLowerCase().includes(query)) ||
             (s.estado && s.estado.toLowerCase().includes(query));
    });
  }

  return list;
});

const actividadReciente = computed(() => {
  return solicitudesFiltradas.value.slice(0, 10);
});

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
      `#SOL-${s.id}`,
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
</script>