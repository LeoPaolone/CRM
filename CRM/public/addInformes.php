<?php
include '../config/config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $tipo = htmlspecialchars(trim($_POST['tipo']));
    $fecha = htmlspecialchars(trim($_POST['fecha']));
    $estado = htmlspecialchars(trim($_POST['estado']));

    if (empty($titulo) || empty($tipo) || empty($fecha) || empty($estado)) {
        $error = "Todos los campos obligatorios deben ser completados.";
    } else {
        $sql = "INSERT INTO informes (titulo, descripcion, tipo, fecha, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $titulo, $descripcion, $tipo, $fecha, $estado);

        if ($stmt->execute()) {
            header("Location: informes.php?message=Informe añadido con éxito.");
            exit;
        } else {
            $error = "Error al añadir el informe: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Nuevo Informe</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script id="tailwind-config">
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
                        "display": ["Manrope"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24
        }
        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark">
    <div class="relative flex h-auto min-h-screen w-full flex-col dark group/design-root overflow-x-hidden font-display" style='font-family: Manrope, "Noto Sans", sans-serif;'>
        <div class="flex flex-col grow pb-24">
            <!-- Header -->
            <header class="sticky top-0 z-10 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm p-4 pt-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-slate-900 dark:text-white text-3xl font-bold">Nuevo Informe</h1>
                    <a href="informes.php" class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">close</span>
                        <span class="hidden sm:inline">Cancelar</span>
                    </a>
                </div>
            </header>

            <main class="flex-grow p-4">
                <?php if (!empty($error)): ?>
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form id="informeForm" method="POST" class="max-w-3xl mx-auto">
                    <div class="bg-white dark:bg-slate-900/40 rounded-xl p-6 space-y-6 border border-slate-200 dark:border-slate-800">
                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Título del Informe <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" required
                                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-800 dark:text-white"
                                placeholder="Ej.: Informe de Ventas Q1 2025">
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="4"
                                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-800 dark:text-white"
                                placeholder="Descripción detallada del informe..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Informe -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Tipo de Informe <span class="text-red-500">*</span>
                                </label>
                                <select id="tipo" name="tipo" required
                                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-800 dark:text-white">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="ventas">Ventas</option>
                                    <option value="clientes">Clientes</option>
                                    <option value="rendimiento">Rendimiento</option>
                                    <option value="financiero">Financiero</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Fecha <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="fecha" name="fecha" required
                                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-800 dark:text-white"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="estado" name="estado" required
                                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-800 dark:text-white">
                                    <option value="borrador">Borrador</option>
                                    <option value="pendiente" selected>Pendiente de revisión</option>
                                    <option value="publicado">Publicado</option>
                                    <option value="archivado">Archivado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="informes.php" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                Guardar Informe
                            </button>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
