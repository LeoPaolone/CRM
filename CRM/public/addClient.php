<?php
include '../config/config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars(trim($_POST['name']));
    $empresa = htmlspecialchars(trim($_POST['company']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telefono = htmlspecialchars(trim($_POST['phone']));
    $direccion = htmlspecialchars(trim($_POST['address']));

    // Validación básica de campos
    if (empty($nombre) || empty($empresa) || empty($email) || empty($telefono) || empty($direccion)) {
        $error = "Todos los campos obligatorios deben ser completados.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } else {
        // Preparar la consulta SQL para insertar un nuevo cliente
        $sql = "INSERT INTO clientes (nombre, empresa, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Vincular parámetros
        $stmt->bind_param("sssss", $nombre, $empresa, $email, $telefono, $direccion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir a la página principal con un mensaje de éxito
            header("Location: index.php?message=Cliente añadido con éxito.");
            exit;
        } else {
            $error = "Error al añadir el cliente: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>

<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Añadir Nuevo Cliente</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#137fec",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
            },
            fontFamily: {
              "display": ["Manrope", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
      body {
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
      }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-light dark:bg-background-dark font-display">
<div class="relative flex min-h-screen w-full flex-col">
<!-- Top App Bar -->
<header class="sticky top-0 z-10 flex h-16 w-full items-center justify-between border-b border-gray-200/10 bg-background-light/80 px-4 dark:bg-background-dark/80 backdrop-blur-sm">
<?php if (!empty($error)): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<a href="index.php" class="flex size-10 items-center justify-center rounded-full text-gray-600 dark:text-gray-300">
    <span class="material-symbols-outlined text-2xl">arrow_back</span>
</a>
<h1 class="text-lg font-bold text-gray-900 dark:text-white">Añadir Nuevo Cliente</h1>
<div class="w-10"></div>
</header>
<!-- Form Content -->
<form id="add-client-form" class="flex flex-1 flex-col" method="POST" action="addClient.php">
    <main class="flex-1 overflow-y-auto p-4">
        <div class="mx-auto flex w-full max-w-md flex-col gap-6">
            <!-- Personal Information Section -->
            <section>
                <h2 class="mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">Información del Cliente</h2>
                <div class="space-y-4">
                    <!-- Nombre Completo Field -->
                    <label class="flex flex-col">
                        <p class="pb-2 text-sm font-medium text-gray-800 dark:text-gray-200">Nombre Completo <span class="text-red-500">*</span></p>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">person</span>
                            <input name="name" required class="form-input h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border border-gray-300 bg-white/50 pl-11 pr-4 text-base font-normal leading-normal text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary" placeholder="Introduce el nombre completo" type="text"/>
                        </div>
                    </label>
                    <!-- Empresa Field -->
                    <label class="flex flex-col">
                        <p class="pb-2 text-sm font-medium text-gray-800 dark:text-gray-200">Empresa <span class="text-red-500">*</span></p>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">business</span>
                            <input name="company" required class="form-input h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border border-gray-300 bg-white/50 pl-11 pr-4 text-base font-normal leading-normal text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary" placeholder="Nombre de la empresa" type="text"/>
                        </div>
                    </label>
                </div>
            </section>
            <!-- Contact Information Section -->
            <section>
                <h2 class="mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">Contacto</h2>
                <div class="space-y-4">
                    <!-- Correo Electrónico Field -->
                    <label class="flex flex-col">
                        <p class="pb-2 text-sm font-medium text-gray-800 dark:text-gray-200">Correo Electrónico <span class="text-red-500">*</span></p>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">mail</span>
                            <input name="email" required class="form-input h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border border-gray-300 bg-white/50 pl-11 pr-4 text-base font-normal leading-normal text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary" placeholder="ejemplo@correo.com" type="email"/>
                        </div>
                    </label>
                    <!-- Teléfono Field -->
                    <label class="flex flex-col">
                        <p class="pb-2 text-sm font-medium text-gray-800 dark:text-gray-200">Teléfono <span class="text-red-500">*</span></p>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">phone</span>
                            <input name="phone" required class="form-input h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border border-gray-300 bg-white/50 pl-11 pr-4 text-base font-normal leading-normal text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary" placeholder="Introduce el número de teléfono" type="tel"/>
                        </div>
                    </label>
                </div>
            </section>
            <!-- Additional Information Section -->
            <section>
                <h2 class="mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">Información Adicional</h2>
                <div class="space-y-4">
                    <!-- Dirección Field -->
                    <label class="flex flex-col">
                        <p class="pb-2 text-sm font-medium text-gray-800 dark:text-gray-200">Dirección <span class="text-red-500">*</span></p>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-4 text-gray-400 dark:text-gray-500">location_on</span>
                            <textarea name="address" required class="form-textarea w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg border border-gray-300 bg-white/50 p-4 pl-11 text-base font-normal leading-normal text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-primary" placeholder="Introduce la dirección completa" rows="3"></textarea>
                        </div>
                    </label>
                </div>
            </section>
        </div>
    </main>
    <!-- Action Buttons -->
    <footer class="sticky bottom-0 flex gap-4 border-t border-gray-200/10 bg-background-light/80 p-4 dark:bg-background-dark/80 backdrop-blur-sm">
        <button type="button" onclick="window.history.back()" class="flex h-12 flex-1 items-center justify-center rounded-lg border border-gray-300 bg-transparent px-6 text-base font-bold text-gray-700 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Cancelar
        </button>
        <button type="submit" class="flex h-12 flex-1 items-center justify-center rounded-lg bg-primary px-6 text-base font-bold text-white shadow-sm transition-opacity hover:opacity-90">
            Guardar Cliente
        </button>
    </footer>
</form>
</div>
<script src="../js/addClient.js"></script>
</body></html>