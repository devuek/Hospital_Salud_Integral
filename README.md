# Sistema de Control de Mantenimiento y Servicios Generales
## Hospital Salud Integral – El Salvador

Sistema web empresarial para la gestión, seguimiento, asignación, ejecución y auditoría de solicitudes de mantenimiento hospitalario y servicios generales. Desarrollado con arquitectura desacoplada bajo Laravel 11 en el backend y Vue 3 (Single Page Application) en el frontend.

---

## Arquitectura del Sistema

* **Backend**: Laravel 11.x (PHP 8.4+ / 8.5)
* **Frontend**: Vue.js 3 (Composition API) + Vite + Pinia + Vue Router
* **Base de Datos**: MySQL 8.0+
* **Contenedores**: Docker & Laravel Sail
* **Seguridad**: Autenticación con Laravel Sanctum, Control de Acceso Basado en Roles (RBAC) y Firmas Criptográficas de Integridad (HMAC/SHA-256).

---

## Roles y Módulos de Usuario

| Rol | Vistas Principales | Capacidades y Flujos |
| :--- | :--- | :--- |
| **Administrador** | `/admin` (Dashboard, Usuarios, Departamentos) | Control global, gestión de sedes y unidades, altas y bajas de personal, reportes analíticos con exportación PDF. |
| **Coordinador** | `/admin` (Coordinación) / `/solicitudes/:id` | Clasificación de prioridad, asignación y reasignación técnica en vivo, validación de cierre y centro de alertas. |
| **Técnico** | `/tareas` / `/tareas/:id` / `/inventario` | Recepción de tareas, registro en bitácora, control de insumos/repuestos, pausas, código OTP de verificación y cancelación con notificación formal. |
| **Solicitante** | `/solicitudes` / `/solicitudes/nueva` | Creación de tickets con evidencia fotográfica (Drag & Drop), consulta de estado en tiempo real, confirmación de cierre y encuestas de satisfacción. |

---

## Requisitos Previos

* Docker Desktop o Docker Engine (Linux)
* Git
* Node.js v20+ / v22+ (para compilación local de assets)

---

## Instalación y Puesta en Marcha

### 1. Clonar el repositorio
```bash
git clone https://github.com/devuek/Hospital_Salud_Integral.git
cd Hospital_Salud_Integral
```

### 2. Configuración del entorno
```bash
cp .env.example .env
```

### 3. Iniciar los contenedores con Laravel Sail
```bash
./vendor/bin/sail up -d
```

### 4. Generar clave de aplicación y ejecutar migraciones con datos iniciales
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
```

### 5. Compilar assets del Frontend (Vite)
```bash
npm install
npm run build
```

El sistema estará accesible en el navegador en: `http://localhost:8080`

---

## Credenciales de Acceso de Demostración

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@hospital.com` | `admin123` |
| **Coordinador** | `maria.garcia@hospital.com` | `password` |
| **Técnico** | `roberto.gomez@hospital.com` | `password` |
| **Solicitante** | `carlos.lopez@hospital.com` | `password` |

---

## Base de Datos y Estructura

* El script DDL con la estructura relacional se encuentra en: `database/docs/estructura_base_datos.sql`
* Acceso a la consola MySQL desde Sail:
  ```bash
  ./vendor/bin/sail mysql
  ```
* Conexión para clientes externos (DBeaver, TablePlus, MySQL Workbench):
  * **Host**: `127.0.0.1`
  * **Puerto**: `3306` (o `3307` según `.env`)
  * **Base de datos**: `laravel`
  * **Usuario**: `sail`
  * **Contraseña**: `password`

---

**Hospital Salud Integral – El Salvador**  
*Equipo de Desarrollo y Arquitectura de Software – Grupo 04 (Ciclo II / 2026)*
