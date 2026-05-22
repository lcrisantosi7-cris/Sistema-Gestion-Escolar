# EduCore — Sistema de Gestión Académica

Sistema web para la administración académica de instituciones educativas de nivel primaria y secundaria. Desarrollado en PHP nativo con PostgreSQL (Supabase) como base de datos en la nube.

---

## ¿Para qué sirve?

EduCore centraliza los procesos administrativos y académicos de un colegio en una sola plataforma:

- **Gestión de personal** — registro de directores, secretarias y docentes con control de acceso por rol
- **Matrículas** — inscripción de estudiantes nuevos y reingresantes, con validación de vacantes y situación académica
- **Periodos y secciones** — configuración del año lectivo, bimestres, grados y secciones
- **Asistencia** — registro diario por curso con historial editable
- **Calificaciones** — ingreso de notas por competencia y bimestre
- **Boletas de notas** — generación e impresión del informe de progreso del aprendizaje por alumno

---

## Roles del sistema

| Rol | Acceso |
|---|---|
| **Director** | Acceso completo a todos los módulos |
| **Secretaria** | Matrículas, directorio, boletas y personal |
| **Docente** | Asistencia y calificaciones de sus cursos asignados |

---

## Tecnologías

- **Backend:** PHP 8+ (sin framework)
- **Base de datos:** PostgreSQL 17 vía [Supabase](https://supabase.com)
- **Frontend:** HTML, CSS (design system propio), JavaScript vanilla
- **Servidor local:** XAMPP (Apache)
- **Fuentes e íconos:** Inter (Google Fonts), Font Awesome 6

---

## Requisitos previos

- [XAMPP](https://www.apachefriends.org/) con PHP 8.0 o superior
- Extensión **`pdo_pgsql`** habilitada en `php.ini`
- Cuenta en [Supabase](https://supabase.com) (plan gratuito es suficiente)
- Conexión a internet (la base de datos está en la nube)

### Habilitar pdo_pgsql en XAMPP

Abre `C:\xampp\php\php.ini`, busca la línea siguiente y quítale el `;`:

```ini
;extension=pdo_pgsql  →  extension=pdo_pgsql
```

Luego reinicia Apache desde el panel de XAMPP.

---

## Instalación

### Opción A — Setup automático (recomendado)

Después de configurar las credenciales en `Config/database.php`, abre en el navegador:

```
http://localhost/EduCore/setup.php
```

El script ejecuta todos los pasos del README de una sola vez: crea las tablas, carga los grados, crea el usuario admin y te lleva al login. Elimínalo después de usarlo.

---

### Opción B — Instalación manual

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/EduCore.git
```

Coloca la carpeta dentro de `C:\xampp\htdocs\`.

### 2. Crear el proyecto en Supabase

1. Ve a [supabase.com](https://supabase.com) → **New project**
2. Anota el **host**, **usuario** y **contraseña** de la base de datos
3. En **Settings → Database → Connection parameters** encontrarás los datos de conexión

### 3. Configurar la conexión

Edita `Config/database.php` y reemplaza los valores:

```php
private $host    = 'db.XXXXXXXXXX.supabase.co';  // tu host
private $usuario = 'postgres.XXXXXXXXXX';         // tu usuario
private $clave   = 'TU_PASSWORD';                 // tu contraseña
private $puerto  = '6543';                        // pooler port
```

### 4. Crear las tablas

Copia el contenido de `supabase_migration.sql` y ejecútalo en **Supabase → SQL Editor → New query → Run**.

### 5. Cargar los grados

Abre en el navegador:

```
http://localhost/EduCore/seed_grados.php
```

Esto inserta los 11 grados (6 de Primaria + 5 de Secundaria). Elimina el archivo después.

### 6. Crear el usuario administrador

```
http://localhost/EduCore/crear_admin.php
```

Credenciales por defecto:

```
Usuario:    admin
Contraseña: admin123
```

> ⚠️ Cambia la contraseña desde el perfil después del primer inicio de sesión. Elimina `crear_admin.php` una vez usado.

### 7. Ingresar al sistema

```
http://localhost/EduCore/Views/Auth/login.php
```

---

## Estructura del proyecto

```
EduCore/
├── Config/
│   ├── database.php          # Conexión PDO a Supabase (singleton)
│   └── session_check.php     # Validación de sesión activa
├── Controllers/              # Lógica de negocio por módulo
├── Models/                   # Acceso a datos (PDO)
├── Views/                    # Vistas PHP por módulo
│   ├── Layout/               # Header y sidebar compartidos
│   ├── Auth/                 # Login / logout
│   ├── Dashboard/            # Inicio y perfil
│   ├── Administracion/       # Gestión de personal
│   ├── Gestion_Estudiantes/  # Matrículas y directorio
│   ├── Gestion_Institucional/# Periodos, grados, secciones, cursos
│   ├── Asistencia/           # Registro e historial
│   ├── Notas/                # Calificaciones
│   └── Boleta_Notas/         # Generación de boletas
└── supabase_migration.sql    # Script DDL para crear las tablas
```

---

## Flujo de configuración inicial

```
1. Crear periodo académico   →  Periodo Académico
2. Verificar grados cargados →  Grados y Secciones
3. Registrar docentes        →  Personal
4. Crear secciones           →  Grados y Secciones → + Sección
5. Asignar docentes a cursos →  Carga Académica
6. Matricular estudiantes    →  Nueva Matrícula
7. Registrar asistencia      →  Asistencia (rol Docente)
8. Ingresar calificaciones   →  Calificaciones (rol Docente)
9. Generar boletas           →  Boletas de Notas
```

---

## Capturas

> *(Agrega aquí capturas de pantalla del sistema una vez desplegado)*

---

## Licencia

Proyecto de portafolio personal. Uso educativo y de demostración.
