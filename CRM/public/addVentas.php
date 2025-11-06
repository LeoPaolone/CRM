<?php
include '../config/config.php';

$clientes = [];
$sql_clientes = "SELECT id, nombre, empresa FROM clientes ORDER BY nombre ASC";
$resultado_clientes = $conn->query($sql_clientes);
if ($resultado_clientes->num_rows > 0) {
    while ($fila = $resultado_clientes->fetch_assoc()) {
        $clientes[] = $fila;
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = intval($_POST['cliente_id']);
    $producto = htmlspecialchars(trim($_POST['producto']));
    $fecha = htmlspecialchars(trim($_POST['fecha']));
    $monto = floatval($_POST['monto']);
    $estado = htmlspecialchars(trim($_POST['estado']));
    $notas = htmlspecialchars(trim($_POST['notas']));

    if (empty($cliente_id) || empty($producto) || empty($fecha) || empty($monto) || empty($estado)) {
        $error = "Todos los campos obligatorios deben ser completados.";
    } elseif ($monto <= 0) {
        $error = "El monto debe ser un valor positivo.";
    } else {
        // Preparar la descripción para la tabla de ventas (usando el producto/servicio)
        $descripcion = $producto;

        $sql = "INSERT INTO ventas (cliente_id, descripcion, monto, estado, fecha_venta, notas) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdsss", $cliente_id, $descripcion, $monto, $estado, $fecha, $notas);

        if ($stmt->execute()) {
            header("Location: ventas.php?message=Venta añadida con éxito.");
            exit;
        } else {
            $error = "Error al añadir la venta: " . $stmt->error;
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
    <title>Nueva Venta</title>
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
                    <div class="flex items-center gap-4">
                        <a href="ventas.php" class="text-primary">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                        <h1 class="text-slate-900 dark:text-white text-2xl font-bold">Nueva Venta</h1>
                    </div>
                    <button type="submit" form="ventaForm" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                        Guardar
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-grow p-4">
                <?php if (isset($error)): ?>
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form id="ventaForm" method="POST" class="max-w-2xl mx-auto">
                    <div class="bg-white dark:bg-slate-900/40 rounded-xl p-6 space-y-6 border border-slate-200 dark:border-slate-800">
                        <!-- Cliente -->
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Cliente <span class="text-red-500">*</span>
                            </label>
                            <select id="cliente_id" name="cliente_id" required
                                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 px-3.5 text-slate-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/50">
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>">
                                        <?php echo htmlspecialchars($cliente['nombre']); ?>
                                        <?php if (!empty($cliente['empresa'])): ?>
                                            (<?php echo htmlspecialchars($cliente['empresa']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Producto/Servicio -->
                        <div>
                            <label for="producto" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Producto/Servicio <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="producto" name="producto" required
                                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 px-3.5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/50"
                                placeholder="Ej: Desarrollo web, Consultoría, etc.">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha -->
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Fecha <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="fecha" name="fecha" required
                                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 px-3.5 text-slate-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/50"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <!-- Monto -->
                            <div>
                                <label for="monto" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Monto <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">$</span>
                                    <input type="number" id="monto" name="monto" step="0.01" min="0" required
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 pl-8 pr-3.5 text-slate-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/50"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="estado" name="estado" required
                                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 px-3.5 text-slate-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/50">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="completado">Completado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div>
                            <label for="notas" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Notas adicionales
                            </label>
                            <textarea id="notas" name="notas" rows="3"
                                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-2.5 px-3.5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/50"
                                placeholder="Detalles adicionales sobre la venta..."></textarea>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>