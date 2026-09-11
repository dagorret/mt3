# Guía de Instalación y Configuración del Proyecto (`SETUP.md`)

Este documento detalla los comandos y configuraciones ejecutados para levantar el entorno de desarrollo del sistema administrativo.

**1. Clonado e Inicialización del Proyecto**

Descarga del starter kit oficial de Laravel Livewire e instalación de sus dependencias base:

Bash

```
# 1. Clonar el kit oficial de Livewire
git clone https://github.com/laravel/livewire-starter-kit.git mt3

# 2. Entrar al proyecto
cd mt3

# 3. Instalar las dependencias de PHP y JavaScript
composer install
npm install

# 4. Crear el archivo .env y generar la clave
cp .env.example .env
php artisan key:generate

# 5. Configurar credenciales de PostgreSQL en el archivo .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=mt3_db
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password

# 6. Correr migraciones iniciales y compilar assets
php artisan migrate
npm run dev
```

**2. Paquetes y Módulos Adicionales Instalados**

Módulos integrados para la gestión de acceso, roles y seguridad:

Bash

```
# Instalación del paquete de roles y permisos
composer require spatie/laravel-permission

# Publicar la configuración de Spatie Permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

**3. Módulos de Arquitectura de Datos Implementados**

El esquema de base de datos incluye las siguientes migraciones y modelos personalizados:

- **`unidades`**: Estructura de departamentos y facultades de la institución.

- **`personas`**: Padrón general de individuos, desacoplado del usuario de autenticación.

- **`bitacora`**: Registro polimórfico de auditoría en formato `jsonb`.

- **Trigger de Inmutabilidad**: Función PostgreSQL `prevenir_modificacion_bitacora()` que bloquea `UPDATE` y `DELETE` en la tabla de bitácora.

- **Trait `Auditable`**: Trait de Eloquent para automatizar el registro de eventos (`CREACION`, `MODIFICACION`, `ELIMINACION`) en los modelos observados.

**4. Capa de Presentación y Frontend**

- **Componente `<x-menu-item>`**: Componente Blade anónimo para enlaces del menú lateral con compatibilidad para SPA (`wire:navigate`).

- **Estilos Semánticos**: Clases `@apply` en `resources/css/app.css` (`.navegacion-item` y `.navegacion-item-activo`) para evitar el uso excesivo de utilidades de Tailwind en las vistas.

- **Layout Reusable**: Integración en `sidebar.blade.php` para la navegación modular.

Completo Atualizado

```
# 1. Clonar el kit oficial de Livewire

git clone https://github.com/laravel/livewire-starter-kit.git mt3

# 2. Entrar al proyecto

cd mt3

# 3. Instalar las dependencias de PHP y JavaScript

composer install
npm install

# 4. Crear el archivo .env y generar la clave

cp .env.example .env
php artisan key:generate

# 5. Instalar Paquetes de Roles y 2FA Local

composer require spatie/laravel-permission
composer require laravel/fortify

# 6. Publicar configuraciones

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan fortify:install

# 7. Configurar la base de datos PostgreSQL en el .env y migrar

php artisan migrate
npm run dev
```
