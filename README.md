# 📚 Memorias sin orden

> *Una colección literaria digital para publicar y compartir narraciones personales*

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind%20CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</p>

**Demo:** [h.artacho.org](https://h.artacho.org) · **Repo:** [github.com/martinartacho/memorias](https://github.com/martinartacho/memorias)

---

## ✨ Características

### 📖 Narraciones

- **CRUD completo** con slugs automáticos y fechas de publicación
- **Tres estados** de publicación:
  - `borrador` — solo visible en el panel de administración
  - `publicado` — visible con texto completo
  - `promocion` — visible con los primeros 100 caracteres (teaser para suscriptores)
- **Tres niveles de acceso** por narración:
  - `publico` — accesible para cualquier visitante
  - `seguidores` — solo para suscriptores verificados por email
  - `privado` — invisible al público, solo accesible desde el panel admin
- **Publicación programada** — narraciones con fecha futura no aparecen en el catálogo público
- **Contador de palabras** en cada tarjeta y en la vista de detalle
- **Narración destacada** (campo `orden`) fija en la portada, independiente de la paginación
- **Contador de lecturas** por narración

### 📧 Suscripción por email

Acceso a contenido exclusivo sin necesidad de crear una cuenta:

1. El visitante hace clic en una narración de suscriptores o en "Suscribirme"
2. Introduce su email → recibe un **magic link** (sin contraseña)
3. Clic en el enlace → cookie de acceso (1 año) → lectura inmediata
4. Baja en un clic desde el mismo email

### 👥 Roles de usuario

| Rol | Catálogo | Panel admin | Narraciones |
|-----|----------|-------------|-------------|
| `lector` | Público + suscriptor si cookie | No | Solo lectura |
| `editor` | Público + suscriptor si cookie | Sí | Solo las propias |
| `admin` | Todo (incluido privado/futuras) | Sí | Todas |

- Los **lectores** pueden solicitar el rol de editor desde su dashboard
- Los **admins** cambian roles desde `/admin/users` sin recargar

### 🎨 Diseño literario

- Estética vintage con paleta pergamino/tinta/acento rojo
- Tipografía: **Playfair Display** (títulos) · **Lora** (cuerpo) · **Josefin Sans** (labels)
- Layout de dos columnas en la vista de detalle (metadatos | contenido)
- Paginación personalizada en español integrada en el diseño
- Responsive para móvil y escritorio

---

## 🏗 Estructura del proyecto

```
hartacho/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    # Login, registro, contraseña
│   │   │   ├── NarracionController.php  # Catálogo público + panel admin
│   │   │   ├── SubscriberController.php # Flujo de suscripción por email
│   │   │   ├── UserController.php       # Gestión de usuarios (admin)
│   │   │   └── ProfileController.php
│   │   └── Middleware/
│   │       └── EnsureUserIsAdmin.php    # Protege rutas /admin/*
│   ├── Mail/
│   │   └── SubscriberMagicLink.php      # Email con enlace de acceso
│   └── Models/
│       ├── Narracion.php   # estados: borrador/publicado/promocion
│       ├── Subscriber.php  # Suscriptores por email (magic link)
│       ├── User.php        # Roles: lector/editor/admin
│       └── Follow.php
├── database/migrations/
│   ├── create_narracions_table          # Tabla principal con permisos y estado
│   ├── create_follows_table
│   ├── add_role_to_users_table
│   ├── create_subscribers_table         # email + token + verified_at
│   └── add_promocion_to_narracions_estado
├── resources/views/
│   ├── layouts/literario.blade.php      # Layout principal con CSS inline
│   ├── narraciones/
│   │   ├── index-literario.blade.php    # Catálogo con destacado + grid + teaser
│   │   └── show-literario.blade.php     # Detalle dos columnas + modo promocion
│   ├── suscripcion/
│   │   ├── form.blade.php               # Formulario de email
│   │   ├── enviada.blade.php            # Confirmación de envío
│   │   └── baja.blade.php              # Confirmación de baja
│   ├── admin/
│   │   ├── narraciones/                 # CRUD admin con editor WYSIWYG
│   │   └── users/index.blade.php        # Gestión de usuarios con cambio de rol
│   ├── emails/
│   │   └── subscriber-magic-link.blade.php
│   └── pagination/literario.blade.php   # Paginación con diseño literario
├── lang/es/pagination.php               # Traducciones de paginación
└── routes/web.php
```

---

## 🚀 Instalación local

### Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- MySQL 8.0+

### Pasos

```bash
# 1. Clonar
git clone https://github.com/martinartacho/memorias.git
cd memorias

# 2. Dependencias
composer install
npm install

# 3. Entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar .env (ver sección siguiente)

# 5. Base de datos
php artisan migrate
php artisan db:seed --class=AdminUserSeeder

# 6. Assets
npm run build

# 7. Enlace de storage (imágenes subidas)
php artisan storage:link

# 8. Servidor
php artisan serve
```

### Variables de entorno clave

```env
APP_URL=http://hartacho.test
APP_DEBUG=true

DB_DATABASE=hartacho
DB_USERNAME=root
DB_PASSWORD=

# Email — en local usar 'log' (los emails van a storage/logs/laravel.log)
MAIL_MAILER=log
# En producción:
# MAIL_MAILER=smtp
# MAIL_HOST=
# MAIL_PORT=587
# MAIL_USERNAME=
# MAIL_PASSWORD=
# MAIL_FROM_ADDRESS=
# MAIL_FROM_NAME="Memorias sin orden"

# Contraseña del usuario admin inicial (usado por AdminUserSeeder)
SEEDER_ADMIN_PASSWORD=
```

### Credenciales por defecto (seeder)

| Campo | Valor |
|-------|-------|
| Email | `hola@artacho.org` |
| Contraseña | valor de `SEEDER_ADMIN_PASSWORD` en `.env` |
| Rol | `admin` |

---

## 🌐 Despliegue en producción

### Variables de entorno (producción)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://h.artacho.org

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Memorias sin orden"

SESSION_ENCRYPT=true
```

### Comandos de despliegue

```bash
composer install --no-dev --optimize-autoloader
npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Permisos de carpetas (servidor Linux)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔧 Mantenimiento

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Ver emails enviados en local (abre el log)
tail -f storage/logs/laravel.log | grep -A 10 "Message-ID"

# Migraciones pendientes
php artisan migrate:status

# Recompilar assets tras cambios CSS/JS
npm run build
```

---

## 🔐 Seguridad

- Rutas `/admin/*` protegidas por middleware `EnsureUserIsAdmin`
- Los **editores** solo pueden editar/borrar sus propias narraciones (`user_id`)
- El **autosave** verifica que la narración pertenece al usuario autenticado
- Suscriptores autenticados mediante cookie `HttpOnly` con token único de 64 caracteres
- Narraciones `privado` devuelven 404 al público, nunca aparecen en listados
- Imágenes subidas con nombre `hashName()` (sin nombre original del cliente)

---

## 📄 Licencia

MIT — ver [LICENSE](LICENSE)

---

<p align="center"><em>"Palabras que perduran"</em></p>
