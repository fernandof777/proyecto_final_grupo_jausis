# Grupo Los Jausis

## Ejecución con Docker

Requisitos:

- Docker Desktop con WSL 2 y virtualización habilitada.
- Puertos `8001`, `3308` y `5173` disponibles.

Iniciar todo el sistema:

```bash
docker compose up -d
```

La aplicación quedará disponible en
[http://localhost:8001](http://localhost:8001). Durante el primer arranque,
Docker instala las dependencias, crea las tablas y ejecuta los seeders.

Credenciales iniciales:

- Administrador: `luis@taller.com`
- Contraseña: `password123`

Comandos útiles:

```bash
docker compose ps
docker compose logs -f
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose down
```

Los datos de MySQL se conservan en el volumen `database_data`. Para detener
los contenedores sin borrar la información usa `docker compose down`.

Aplicación web para la gestión integral de un taller automotriz. Este proyecto
unifica la autenticación y el módulo de servicios con la dirección visual,
wireframes y mockups elaborados para el proyecto académico.

## Estado actual

- Autenticación manual con inicio y cierre de sesión.
- Dashboard con métricas obtenidas de la base de datos.
- Catálogo de servicios con listado, búsqueda, filtros, paginación, registro,
  edición y eliminación.
- Asociación automática entre servicios y usuarios.
- Autorización por propietario para editar y eliminar servicios.
- Validaciones reutilizables con mensajes en español.
- Navegación responsiva preparada para módulos futuros.
- Wireframes y mockups disponibles en `public/img`.
- Gestión completa de clientes y sus datos de contacto.
- Registro de vehículos vinculados con sus propietarios.
- Órdenes de trabajo con recepción, diagnóstico, estados, fechas y total.
- Inventario de repuestos con alertas automáticas de stock mínimo.
- Reportes operativos y financieros filtrados por periodo.
- Mapa interactivo con la ubicación exacta del taller y acceso a indicaciones.

Los módulos de roles, permisos avanzados y pagos se incorporarán
progresivamente.

## Requisitos

- PHP 8.3 o superior.
- Composer.
- MySQL o MariaDB.
- Node.js y NPM.

## Instalación

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Crear una base de datos llamada `tallerpro` y configurar sus credenciales en
`.env`. En la instalación de desarrollo actual, XAMPP utiliza `::1:3307`
porque el puerto 3306 pertenece a otra instancia de MySQL.

```bash
php artisan migrate:fresh --seed
php artisan serve
```

La aplicación estará disponible normalmente en `http://127.0.0.1:8000`.

## Usuarios iniciales

| Usuario | Correo | Contraseña |
|---|---|---|
| Luis Fernando | `luis@taller.com` | `password123` |
| Maria Garcia | `maria@taller.com` | `password123` |

Estas cuentas son únicamente para desarrollo y deben cambiarse antes de una
publicación real.

## Pruebas

```bash
php artisan test
```

Las pruebas cubren autenticación, protección de rutas, servicios, clientes,
vehículos, órdenes, consistencia de propietarios, inventario y reportes.

## Seguridad implementada

- Contraseñas con hashing seguro de Laravel.
- Bloqueo temporal después de cinco intentos de acceso fallidos.
- Límites de solicitudes globales y específicos para login y reportes.
- Cookies de sesión `HttpOnly`, cifradas y seguras en producción.
- Protección CSRF en formularios y consultas parametrizadas mediante Eloquent.
- Roles de administrador, recepción y almacén comprobados en el servidor.
- Cuentas desactivadas expulsadas automáticamente.
- Cabeceras CSP, anti-clickjacking, `nosniff`, Referrer Policy y Permissions Policy.
- HSTS cuando la petición utiliza HTTPS.
- Auditoría de creaciones, modificaciones y eliminaciones sin guardar contraseñas.
- Configuración segura de despliegue en `.env.production.example`.

Para producción se debe usar un servidor administrado con HTTPS, firewall y
protección DDoS/WAF como Cloudflare. XAMPP es exclusivamente para desarrollo.

## Próxima etapa sugerida

Implementar roles y permisos, asignación de mecánicos, consumo de repuestos por
orden y registro de pagos.
