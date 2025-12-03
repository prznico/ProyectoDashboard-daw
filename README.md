# 📚 Dashboard de Gestión de Recursos Digitales

Sistema completo de gestión de recursos digitales (archivos) con autenticación de usuarios, panel de administración y catálogo público.

## 🎯 Características

✅ **Autenticación de Usuarios**
- Registro con validación de email
- Login con contraseñas hasheadas
- Roles: Admin y Visitante
- Bitácora de acceso (login/logout)

✅ **Gestión de Recursos (Admin)**
- Crear, editar, eliminar recursos
- Subida de archivos con validación (50MB máx, 13 tipos permitidos)
- Metadata: nombre, autor, departamento, empresa, fecha, descripción
- Descarga de archivos con registro en bitácora

✅ **Catálogo Público**
- Visualización de todos los recursos sin autenticación
- Búsqueda por nombre, autor, descripción
- Filtros por tipo de archivo
- Paginación (6 recursos por página)
- Descargas anónimas con registro de IP (bitácora)

✅ **Estadísticas y Analítica**
- 3 gráficas interactivas con Chart.js:
  - Descargas por tipo de archivo (Doughnut)
  - Descargas por día de la semana (Bar)
  - Descargas por hora del día (Line)
- Período: últimos 7-30 días
- Diferenciación entre descargas autenticadas vs anónimas

✅ **Seguridad**
- Prepared statements en todas las consultas
- Validación de entrada en formularios
- Control de rol en endpoints admin
- MIME type detection para archivos
- Contraseñas con `password_hash()` y `password_verify()`

## 🛠️ Stack Tecnológico

- **Backend:** PHP 7.x con mysqli
- **Base de Datos:** MySQL 5.7+ (utf8mb4)
- **Frontend:** HTML5, Bootstrap 4 (Superhero theme), jQuery 3.3.1
- **Gráficas:** Chart.js 3.9.1
- **Gestor de Paquetes:** Composer

## 📋 Requisitos Previos

- PHP 7.4+
- MySQL 5.7+
- Composer
- XAMPP (recomendado para desarrollo local)

## 🚀 Instalación

### 1. Clonar o descargar el proyecto

```bash
cd C:\xampp\htdocs\proyecto_daw
```

### 2. Instalar dependencias con Composer

```bash
cd backend
composer install
```

### 3. Configurar la Base de Datos

#### Opción A: Usando phpMyAdmin

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Crea una nueva base de datos: `dashboard_recursos`
3. Ejecuta el siguiente SQL:

```sql
-- Crear tabla de usuarios
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `contraseña` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'visitante') DEFAULT 'visitante',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `activo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de recursos
CREATE TABLE `recursos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `autor` VARCHAR(255),
  `departamento` VARCHAR(255),
  `empresa_institucion` VARCHAR(255),
  `fecha_creacion` DATE,
  `descripcion` TEXT,
  `nombre_archivo` VARCHAR(255) NOT NULL,
  `tipo_archivo` VARCHAR(50) NOT NULL,
  `url_archivo` VARCHAR(255) NOT NULL,
  `tamaño_mb` DECIMAL(10, 2),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `eliminado` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de bitácora de acceso
CREATE TABLE `bitacora_acceso` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT,
  `tipo_acceso` ENUM('login', 'logout', 'view') DEFAULT 'view',
  `recurso_id` INT,
  `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(45),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`recurso_id`) REFERENCES `recursos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de bitácora de descargas
CREATE TABLE `bitacora_descargas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT,
  `recurso_id` INT NOT NULL,
  `fecha_descarga` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(45),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`recurso_id`) REFERENCES `recursos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario admin de prueba
INSERT INTO `usuarios` (`nombre`, `email`, `contraseña`, `rol`) VALUES
('Admin', 'admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 'admin');
-- Contraseña: password123
```

#### Opción B: Usar archivo SQL (si existe)

```bash
mysql -u root -p dashboard_recursos < database.sql
```

### 4. Crear carpeta de descargas

```bash
mkdir backend\uploads\recursos
```

Otorgar permisos de escritura:
- En Windows: Click derecho > Propiedades > Seguridad > Editar permisos
- En Linux/Mac: `chmod 755 backend/uploads/recursos`

### 5. Verificar configuración de conexión

Editar `backend/database.php` si es necesario:

```php
$db = new mysqli('localhost', 'root', 'N1n1c0l3.', 'dashboard_recursos');
```

## 📂 Estructura del Proyecto

```
proyecto_daw/
├── login.html                    # Página de login
├── register.html                 # Página de registro
├── dashboard.html                # Dashboard admin
├── catalogo.html                 # Catálogo público
├── css/
│   └── styles.css                # Estilos personalizados
├── js/
│   ├── dashboard.js              # Lógica del dashboard
│   ├── catalogo.js               # Lógica del catálogo
│   └── charts.js                 # Gráficas con Chart.js
├── backend/
│   ├── database.php              # Conexión a BD
│   ├── composer.json             # Dependencias
│   ├── config/
│   │   └── constants.php          # Constantes del proyecto
│   ├── usuarios/
│   │   ├── register.php           # Endpoint: registrar usuario
│   │   ├── login.php              # Endpoint: iniciar sesión
│   │   ├── logout.php             # Endpoint: cerrar sesión
│   │   └── validate-session.php   # Endpoint: validar sesión
│   ├── recursos/
│   │   ├── resource-add.php       # Endpoint: agregar recurso
│   │   ├── resource-list.php      # Endpoint: listar recursos
│   │   ├── resource-single.php    # Endpoint: recurso específico
│   │   ├── resource-edit.php      # Endpoint: editar recurso
│   │   ├── resource-delete.php    # Endpoint: eliminar recurso
│   │   └── resource-download.php  # Endpoint: descargar archivo
│   ├── bitacora/
│   │   ├── record_download.php    # Endpoint: registrar descarga
│   │   ├── get-download-stats.php # Endpoint: stats por tipo
│   │   ├── get-downloads-by-day.php        # Endpoint: stats por día
│   │   ├── get-downloads-by-hour.php       # Endpoint: stats por hora
│   │   └── get-resource-type-stats.php     # Endpoint: stats de recursos
│   ├── myapi/
│   │   ├── DataBase.php           # Clase base (conexión + helpers)
│   │   ├── Create/Create.php      # Servicio: crear recurso
│   │   ├── Read/Read.php          # Servicio: leer recurso
│   │   ├── Update/Update.php      # Servicio: actualizar recurso
│   │   └── Delete/Delete.php      # Servicio: eliminar recurso
│   ├── uploads/recursos/          # Almacenamiento de archivos
│   └── vendor/                    # Dependencias Composer
└── README.md                      # Este archivo
```


## 🌐 URLs de Acceso

| Página | URL | Acceso |
|--------|-----|--------|
| Login | `http://localhost/proyecto_daw/login.html` | Público |
| Registro | `http://localhost/proyecto_daw/register.html` | Público |
| Catálogo | `http://localhost/proyecto_daw/catalogo.html` | Público |
| Dashboard | `http://localhost/proyecto_daw/dashboard.html` | Solo Admin |


## 📝 Validaciones

### Archivo
- Tamaño máximo: 50 MB
- Tipos permitidos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR, TXT, JPG, PNG, GIF

### Usuario
- Nombre: Mínimo 3 caracteres
- Email: Válido y único
- Contraseña: Mínimo 6 caracteres (hasheada con bcrypt)

### Recurso
- Nombre: Requerido
- Autor: Requerido
- Fecha de Creación: Requerido (formato YYYY-MM-DD)
- Archivo: Requerido

## 🔒 Seguridad Implementada

✅ **Prepared Statements** - Prevención de SQL injection en todas las consultas
✅ **Validación de Entrada** - Validación de tipos y longitudes
✅ **Validación de Rol** - Control de acceso en endpoints admin
✅ **Hashing de Contraseña** - `password_hash()` con algoritmo por defecto
✅ **Validación de Archivo** - Whitelist de extensiones, size limits, MIME detection
✅ **Sesiones** - Protección con tokens de sesión
✅ **CORS** - Headers de seguridad para peticiones AJAX
✅ **Bitácora** - Registro de acceso y descargas para auditoría

## 🐛 Troubleshooting

### Error: "Base de datos no encontrada"
- Verifica que `dashboard_recursos` existe en MySQL
- Ejecuta el SQL de creación de tablas en phpMyAdmin

### Error: "Carpeta de descargas no existe"
- Crea `backend/uploads/recursos/` manualmente
- Verifica los permisos de escritura

### Error: "No se puede subir archivo"
- Verifica el tamaño (máx 50MB)
- Verifica la extensión (13 tipos permitidos)
- Revisa los permisos de la carpeta `uploads`

### Las gráficas no se muestran
- Abre la consola del navegador (F12)
- Verifica que los endpoints de estadísticas retornan datos
- Revisa que Chart.js se cargó correctamente

### Sesión expira rápidamente
- Aumenta `session.gc_maxlifetime` en `php.ini`
- Valor recomendado: 86400 (24 horas)

## 📧 Contacto y Soporte

Para reportar errores o sugerencias, contacta al equipo de desarrollo.

# ProyectoDashboard-daw
Dashboardpara la gestión de recursos digitales (archivos de soporte para programadores)
