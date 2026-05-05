# 📚 Memorias sin orden

> *Una colección literaria digital para publicar narraciones y relatos*

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Tailwind%20CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

## 🎭 **Características Principales**

### 📖 **Gestión de Narraciones**
- ✅ **CRUD completo** para crear, leer, actualizar y eliminar narraciones
- ✅ **Sistema de publicación** con estado (borrador/publicado)
- ✅ **URLs amigables** con slugs automáticos
- ✅ **Orden cronológico** por fecha de publicación
- ✅ **Paginación elegante** para listados

### 🎨 **Diseño Literario**
- ✅ **Estética vintage** con tonos pergamino y tinta
- ✅ **Tipografía elegante** (Playfair Display, Lora, Josefin Sans)
- ✅ **Letra capital** en la primera línea de cada narración
- ✅ **Diseño responsive** para todos los dispositivos
- ✅ **Grid de tarjetas** para presentación visual

### 🔐 **Sistema de Autenticación**
- ✅ **Laravel Breeze** para autenticación robusta
- ✅ **Login/Register/Logout** con diseño literario
- ✅ **Recuperación de contraseña** por email
- ✅ **Panel de administración** protegido
- ✅ **Sesiones seguras** con middleware

### 🛠 **Tecnologías Modernas**
- ✅ **Laravel 12** con PHP 8.2
- ✅ **Tailwind CSS** para estilos
- ✅ **Vite** para gestión de assets
- ✅ **MySQL** para base de datos
- ✅ **Blade** para plantillas

---

## 🚀 **Instalación Local**

### 📋 **Requisitos Previos**
- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL 8.0 o superior
- Git

### ⚙️ **Pasos de Instalación**

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/martinartacho/memorias.git
   cd memorias
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias Node**
   ```bash
   npm install
   ```

4. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configurar base de datos
   ```bash
   # Editar .env con tus credenciales de MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=memorias
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña_segura
   ```

6. Ejecutar migraciones y seeders
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Compilar assets**
   ```bash
   npm run build
   ```

8. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

### 🔑 **Credenciales de Administración**
- **Email**: Configurar en `SEEDER_ADMIN_EMAIL` en `.env`
- **Contraseña**: Configurar en `SEEDER_ADMIN_PASSWORD` en `.env`

---

## 🌐 **Despliegue en hartacho.com**

### 🎯 **Opción 1: Hosting Compartido (cPanel)**

1. **Subir archivos**
   ```bash
   # Excluir carpetas innecesarias
   # - node_modules
   # - .git
   # - storage/framework/cache/*
   # - storage/framework/sessions/*
   # - storage/framework/views/*
   ```

2. **Configurar entorno**
   - Crear archivo `.env` en el servidor
   - Configurar variables de producción:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://hartacho.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=nombre_db_hartacho
   DB_USERNAME=usuario_db
   DB_PASSWORD=tu_contraseña_segura
   
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hartacho.com
   MAIL_PORT=587
   ```

3. **Instalar dependencias**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install --production
   npm run build
   ```

4. **Permisos de carpetas**
   ```bash
   chmod 755 storage
   chmod 755 storage/framework
   chmod 755 storage/framework/cache
   chmod 755 storage/framework/sessions
   chmod 755 storage/framework/views
   chmod 755 bootstrap/cache
   ```

5. **Ejecutar migraciones**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Optimizar producción**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### 🚀 **Opción 2: VPS/Dedicado (Ubuntu/Debian)**

1. **Instalar servidor web**
   ```bash
   sudo apt update
   sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql
   ```

2. **Configurar Nginx**
   ```nginx
   server {
       listen 80;
       server_name hartacho.com www.hartacho.com;
       root /var/www/hartacho/public;
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
       }
   }
   ```

3. **Configurar SSL con Let's Encrypt**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d hartacho.com -d www.hartacho.com
   ```

4. **Desplegar aplicación**
   ```bash
   git clone https://github.com/martinartacho/memorias.git /var/www/hartacho
   cd /var/www/hartacho
   
   composer install --no-dev --optimize-autoloader
   npm install --production
   npm run build
   
   cp .env.example .env
   php artisan key:generate
   # Configurar .env para producción
   
   php artisan migrate --force
   php artisan db:seed --force
   php artisan optimize
   ```

### ☁️ **Opción 3: Plataforma Cloud (DigitalOcean/Laravel Forge)**

1. **Crear servidor** con Laravel Forge
2. **Conectar repositorio** GitHub
3. **Configurar variables de entorno**
4. **Activar SSL automático**
5. **Configurar cola de trabajos** (opcional)

---

## 📁 **Estructura del Proyecto**

```
hartacho/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/              # Controladores de autenticación
│   │   ├── NarracionController.php
│   │   └── ProfileController.php
│   ├── Models/
│   │   ├── Narracion.php
│   │   └── User.php
│   └── View/Components/       # Componentes Blade
├── database/
│   ├── migrations/           # Migraciones de base de datos
│   └── seeders/              # Datos iniciales
├── resources/views/
│   ├── layouts/
│   │   ├── literario.blade.php    # Layout principal
│   │   └── guest.blade.php        # Layout de autenticación
│   ├── auth/                 # Vistas de autenticación
│   ├── narraciones/          # Vistas públicas
│   └── admin/narraciones/    # Vistas de administración
├── routes/
│   ├── web.php              # Rutas web
│   └── auth.php             # Rutas de autenticación
└── public/                  # Assets públicos
```

---

## 🎨 **Personalización**

### 📝 **Editar Contenido**
- **Títulos**: Editar `resources/views/layouts/literario.blade.php`
- **Colores**: Modificar `resources/css/app.css`
- **Tipografías**: Configurar en `tailwind.config.js`

### 🖼️ **Cambiar Logo**
- Reemplazar `public/favicon.ico`
- Actualizar logo en `resources/views/components/application-logo.blade.php`

### 📧 **Configurar Email**
```env
MAIL_MAILER=smtp
MAIL_HOST=tu-servidor-smtp
MAIL_PORT=587
MAIL_USERNAME=tu-email@hartacho.com
MAIL_PASSWORD=tu_contraseña_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hartacho.com
MAIL_FROM_NAME="Memorias sin orden"
```

---

## 🔧 **Mantenimiento**

### 📊 **Comandos Útiles**
```bash
# Limpiar cache
php artisan optimize:clear

# Cache para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Backup de base de datos
php artisan db:backup --database=mysql --destination=backup.sql

# Crear nueva narración (seeder)
php artisan make:seeder NuevaNarracionSeeder
```

### 🐛 **Solución de Problemas**

**Error 500 - Internal Server Error**
```bash
# Verificar permisos
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Limpiar cache
php artisan optimize:clear
```

**CSS no carga**
```bash
# Recompilar assets
npm run build
npm run dev  # Para desarrollo
```

**Base de datos no conecta**
```bash
# Verificar configuración .env
php artisan config:clear
php artisan migrate:status
```

---

## 📄 **Licencia**

Este proyecto está bajo la Licencia MIT. Ver archivo [LICENSE](LICENSE) para más detalles.

---

## 🤝 **Contribuir**

1. Fork del proyecto
2. Crear rama de características (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abrir Pull Request

---

## 📞 **Soporte**

- **Web**: https://hartacho.com
- **GitHub**: https://github.com/martinartacho/memorias

---

<p align="center">
  <em>"Palabras que perduran en el tiempo digital"</em>
</p>