# Sistema CRM (Customer Relationship Management)

Este es un sistema CRM básico basado en web, diseñado para ayudar a gestionar clientes, ventas, tareas e informes de manera eficiente. Proporciona una interfaz de usuario limpia y responsiva con soporte para modo oscuro.

## Características

El sistema ofrece las siguientes funcionalidades clave:

### 1. Gestión de Clientes
*   **Añadir Clientes**: Formulario para registrar nuevos clientes con detalles como nombre, empresa, email, teléfono y dirección.
*   **Ver Detalles**: Visualización de la información detallada de cada cliente.
*   **Editar y Eliminar**: Funcionalidad para modificar la información de clientes existentes y eliminarlos del sistema.
*   **Búsqueda**: Búsqueda de clientes por nombre o empresa.
*   **Ordenamiento**: Ordenar la lista de clientes por nombre (A-Z, Z-A) o por empresa (A-Z).

### 2. Gestión de Ventas
*   **Añadir Ventas**: Registro de nuevas ventas asociadas a un cliente, incluyendo producto/servicio, monto, fecha y estado.
*   **Visualización**: Listado de ventas (implícito en la sección de informes).

### 3. Gestión de Tareas
*   **Añadir Tareas**: Creación de tareas con título, descripción, fecha de vencimiento, prioridad y la opción de asignarlas a un cliente.
*   **Filtrado**: Filtrar tareas por estado (pendientes o completadas).
*   **Cambiar Estado**: Marcar tareas como completadas directamente desde la lista.
*   **Búsqueda**: Buscar tareas por título o descripción.

### 4. Informes y Métricas
*   **Panel de Métricas**: Visualización de métricas clave como ingresos del mes, nuevas oportunidades, tasa de cierre y el pipeline de ventas.
*   **Gráficos de Ventas**: Gráfico interactivo de ventas con filtrado por rango de fechas (requiere Chart.js).
*   **Generación de Informes**: Opciones para generar informes de ventas y clientes (botones de descarga).
*   **Añadir Informes**: Formulario para añadir nuevos informes con título, descripción, tipo, fecha y estado.

### 5. Autenticación de Usuarios
*   **Registro**: Formulario para que nuevos usuarios se registren en el sistema.
*   **Inicio de Sesión**: Formulario para que los usuarios existentes inicien sesión.
*   **Visibilidad de Contraseña**: Funcionalidad para mostrar/ocultar la contraseña en los campos de entrada.

### 6. Interfaz de Usuario
*   **Diseño Responsivo**: Adaptable a diferentes tamaños de pantalla.
*   **Modo Oscuro**: Soporte para tema oscuro, mejorando la experiencia visual.

## Tecnologías Utilizadas

*   **Frontend**:
    *   HTML5
    *   CSS (Tailwind CSS para estilos)
    *   JavaScript (Vanilla JS para interactividad, Chart.js para gráficos)
*   **Backend**:
    *   PHP
*   **Base de Datos**:
    *   MySQL

## Configuración del Proyecto

### Prerrequisitos
*   Servidor web (Apache, Nginx, etc.)
*   PHP (versión 7.x o superior)
*   MySQL

### Estructura de Archivos
*   `config/`: Contiene archivos de configuración, como la conexión a la base de datos (`config.php`).
*   `api/`: Contiene los endpoints de la API para manejar las solicitudes del frontend (ej. `add_client_handler.php`).
*   `js/`: Contiene los archivos JavaScript para la lógica del lado del cliente.
*   `public/`: Contiene los archivos PHP principales que sirven las vistas y la lógica de negocio.

### Base de Datos
1.  Crea una base de datos MySQL (ej. `crm_db`).
2.  Ejecuta el siguiente esquema SQL para crear las tablas necesarias:

    ```sql
    CREATE TABLE clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        empresa VARCHAR(255),
        email VARCHAR(255) UNIQUE NOT NULL,
        telefono VARCHAR(50),
        direccion TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE ventas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        descripcion TEXT NOT NULL,
        monto DECIMAL(10, 2) NOT NULL,
        estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
        fecha_venta DATE NOT NULL,
        notas TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    );

    CREATE TABLE tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE NOT NULL,
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        completed BOOLEAN DEFAULT FALSE,
        client_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE SET NULL
    );

    CREATE TABLE informes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        descripcion TEXT,
        tipo ENUM('ventas', 'clientes', 'rendimiento', 'financiero', 'personalizado') NOT NULL,
        fecha DATE NOT NULL,
        estado ENUM('borrador', 'pendiente', 'publicado', 'archivado') DEFAULT 'borrador',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```

3.  Configura la conexión a tu base de datos editando el archivo `config/config.php` con tus credenciales:

    ```php
    <?php
    $servername = "localhost";
    $username = "tu_usuario_mysql";
    $password = "tu_contraseña_mysql";
    $dbname = "crm_db"; // El nombre de tu base de datos

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    ?>
    ```

## Instalación

1.  Clona este repositorio en tu servidor web:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    ```
2.  Configura tu servidor web (Apache/Nginx) para que el directorio raíz del documento apunte a la carpeta `public/`.
3.  Asegúrate de que los prerrequisitos estén instalados y configurados.
4.  Accede a la aplicación a través de tu navegador (ej. `http://localhost/`).

## Uso

1.  **Registro/Inicio de Sesión**: Si es la primera vez, regístrate para crear una cuenta. De lo contrario, inicia sesión con tus credenciales.
2.  **Navegación**: Utiliza la barra de navegación o los enlaces para acceder a las secciones de Clientes, Tareas, Ventas e Informes.
3.  **Gestión de Datos**: Usa los formularios para añadir, editar o eliminar información en cada sección.
4.  **Búsqueda y Filtrado**: Aprovecha las funcionalidades de búsqueda y filtrado para encontrar rápidamente la información que necesitas.

## Contribución

Las contribuciones son bienvenidas. Por favor, abre un "issue" o envía un "pull request".


