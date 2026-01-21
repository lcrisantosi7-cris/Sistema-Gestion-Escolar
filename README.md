# 🏫 Sistema de Gestión Escolar - Mariscal Ramón Castilla

Sistema web integral desarrollado para la gestión administrativa y académica de instituciones educativas. Este proyecto aplica estándares modernos de desarrollo en PHP para garantizar seguridad, escalabilidad y una experiencia de usuario fluida.

## 🚀 Tecnologías Utilizadas

* **Backend:** PHP 8.x (Nativo)
* **Base de Datos:** MySQL / MariaDB (usando PDO para máxima seguridad)
* **Arquitectura:** Patrón de diseño **MVC** (Modelo-Vista-Controlador)
* **Frontend:** HTML5, CSS3 (Custom Variables), FontAwesome y Google Fonts (Plus Jakarta Sans)

## 🛠️ Características Técnicas y Seguridad

Este proyecto fue construido priorizando la integridad de los datos y la seguridad del servidor:

* **Protección SQL Injection:** Implementación estricta de **Sentencias Preparadas** con PDO para todas las consultas a la base de datos.
* **Gestión de Contraseñas:** Uso de algoritmos de hashing modernos (`password_hash` y `password_verify`).
* **Arquitectura Limpia:** Aplicación de **Inyección de Dependencias** para el manejo de la conexión a la base de datos, facilitando el mantenimiento.
* **Control de Acceso:** Sistema de autenticación con regeneración de ID de sesión para prevenir ataques de fijación de sesión.
* **Gestión por Roles:** Acceso diferenciado para Director, Secretarias y Docentes.

## 📂 Estructura del Proyecto

* `Config/`: Configuraciones del sistema y conexión a la DB.
* `Controllers/`: Lógica de negocio y manejo de peticiones.
* `Models/`: Interacción con la base de datos y lógica de datos.
* `Views/`: Interfaces de usuario limpias y responsivas.

## ⚙️ Instalación Local

1. Clonar el repositorio.
2. Importar el archivo `.sql` (disponible bajo petición) en phpMyAdmin.
3. Configurar las credenciales en `Config/database.php`.
4. Ejecutar mediante XAMPP o cualquier servidor con soporte PHP.

---
Desarrollado para la educación.
