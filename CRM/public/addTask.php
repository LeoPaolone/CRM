<?php
include '../config/config.php';

$clientes = [];
$sql_clientes = "SELECT id, nombre FROM clientes ORDER BY nombre ASC";
$resultado_clientes = $conn->query($sql_clientes);
if ($resultado_clientes->num_rows > 0) {
    while ($fila = $resultado_clientes->fetch_assoc()) {
        $clientes[] = $fila;
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $due_date = htmlspecialchars(trim($_POST['due_date']));
    $priority = htmlspecialchars(trim($_POST['priority']));
    // client_id es opcional, puede ser NULL
    $client_id = !empty($_POST['client_id']) ? intval($_POST['client_id']) : NULL;

    if (empty($title) || empty($due_date) || empty($priority)) {
        $error = "Los campos Título, Fecha de Vencimiento y Prioridad son obligatorios.";
    } else {
        $sql = "INSERT INTO tasks (title, description, due_date, priority, client_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // El tipo para client_id debe ser 'i' si no es NULL, o 's' si es NULL.
        // bind_param no maneja bien los NULL directamente con 'i', así que lo tratamos con cuidado.
        $stmt->bind_param("ssssi", $title, $description, $due_date, $priority, $client_id);

        if ($stmt->execute()) {
            header("Location: task.php?message=Tarea añadida con éxito.");
            exit;
        } else {
            $error = "Error al añadir la tarea: " . $stmt->error;
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
    <title>Nueva Tarea</title>
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
                    fontFamily: { "display": ["Manrope"] },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }
        body { min-height: max(884px, 100dvh); }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark">
    <div class="relative flex h-auto min-h-screen w-full flex-col dark font-display">
        <div class="flex flex-col grow pb-24">
            <header class="sticky top-0 z-10 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm p-4 pt-6">
                <div class="flex items-center justify-between">
                    <a href="task.php" class="text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
                    <h1 class="text-slate-900 dark:text-white text-2xl font-bold">Nueva Tarea</h1>
                    <div class="w-8"></div>
                </div>
            </header>

            <main class="flex-grow p-4">
                <?php if (!empty($error)): ?>
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form id="taskForm" method="POST" class="max-w-2xl mx-auto">
                    <div class="bg-white dark:bg-slate-900/40 rounded-xl p-6 space-y-6 border border-slate-200 dark:border-slate-800">
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Título <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-slate-800 dark:text-white" placeholder="Ej: Llamar para seguimiento">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary dark:bg-slate-800 dark:text-white" placeholder="Detalles de la tarea..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fecha de Vencimiento <span class="text-red-500">*</span></label>
                                <input type="date" id="due_date" name="due_date" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-800 dark:text-white" value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Prioridad <span class="text-red-500">*</span></label>
                                <select id="priority" name="priority" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-800 dark:text-white">
                                    <option value="low">Baja</option>
                                    <option value="medium" selected>Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="client_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Asignar a Cliente (Opcional)</label>
                            <select id="client_id" name="client_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-800 dark:text-white">
                                <option value="">Ninguno</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="task.php" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Cancelar</a>
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Guardar Tarea</button>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>




















































































































































































































































































































































































\ No newline at end of file
