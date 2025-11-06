<?php

include '../config/config.php';
// La conexión $conn ya se establece en config.php

$sales = [];

// Corregido: Consultar la tabla 'ventas' y unirla con 'clientes' para obtener el nombre del cliente.
$sql = "SELECT 
            v.id, 
            c.nombre AS client_name, 
            v.monto, 
            v.estado, 
            v.fecha_venta 
        FROM ventas AS v
        JOIN clientes AS c ON v.cliente_id = c.id
        ORDER BY v.fecha_venta DESC";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $sales[] = $fila;
    }
} else {
    // No es necesario un mensaje de "no hay ventas" aquí, se manejará en el HTML
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_sale'])) {
    // Reabrir la conexión si es necesario o gestionar la conexión de forma diferente
    $sale_id = intval($_POST['sale_id']);

    $sql = "DELETE FROM ventas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sale_id);

    if ($stmt->execute()) {
        // Redirigir para evitar reenvío del formulario y actualizar la lista
        header("Location: ventas.php");
        exit;
    } else {
        echo "<script>alert('Error al eliminar la venta: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_sale'])) {
    $client_name = htmlspecialchars(trim($_POST['client_name']));
    $monto = floatval($_POST['monto']);
    $estado = htmlspecialchars(trim($_POST['estado']));
    $fecha_venta = htmlspecialchars(trim($_POST['fecha_venta']));
    $cliente_id = intval($_POST['cliente_id']); // Asumiendo que se envía el ID del cliente

    $sql = "INSERT INTO ventas (cliente_id, descripcion, monto, estado, fecha_venta) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdss", $cliente_id, $_POST['descripcion'], $monto, $estado, $fecha_venta);

    if ($stmt->execute()) {
        // Redirigir para evitar reenvío del formulario y actualizar la lista
        header("Location: ventas.php");
        exit;
    } else {
        echo "<script>alert('Error al añadir la venta: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$conn->close();

?>


<!DOCTYPE html>
<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ventas CRM</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 24px;
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-light dark:bg-background-dark font-display text-base">
<div class="relative flex min-h-screen w-full flex-col">
<header class="sticky top-0 z-10 flex items-center bg-background-light/80 dark:bg-background-dark/80 p-4 pb-2 justify-between backdrop-blur-sm">
<h1 class="text-slate-900 dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em] flex-1">Ventas</h1>
<div class="flex items-center justify-end">


<a href="logout.php" class="flex h-12 w-12 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-transparent text-slate-600 dark:text-slate-300" title="Cerrar Sesión">
    <span class="material-symbols-outlined">logout</span>
</a>

</div>
</header>
<main class="flex-1 pb-28">
<section class="flex flex-wrap items-center justify-between gap-2 px-4 pt-4">
<a href="addVentas.php" class="flex items-center justify-center gap-2 rounded-lg bg-primary py-3 text-white transition-colors hover:bg-primary/90">
<span class="material-symbols-outlined !text-xl" style="font-variation-settings: 'FILL' 1;">add_circle</span>
<span class="text-base font-semibold">Añadir Venta</span>
</section>
<section class="flex flex-wrap gap-4 p-4">
<div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
<p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Ingresos del Mes</p>
<p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight">€25,600</p>
</div>
<div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
<p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Nuevas Oportunidades</p>
<p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight">18</p>
</div>
<div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
<p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Tasa de Cierre</p>
<p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight">24%</p>
</div>
</section>
<section class="flex flex-wrap gap-4 px-4 py-4">
<div class="flex min-w-72 flex-1 flex-col gap-4 rounded-xl bg-white dark:bg-slate-800/50 p-6 border border-slate-200 dark:border-slate-800">
<div class="flex flex-col gap-1">
<p class="text-slate-900 dark:text-white text-lg font-bold leading-normal">Pipeline de Ventas</p>
<p class="text-slate-900 dark:text-white tracking-tight text-[32px] font-bold leading-tight truncate">€150,000</p>
<div class="flex gap-1">
<p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">Este Trimestre</p>
<p class="text-green-500 dark:text-green-400 text-sm font-medium leading-normal">+5.2%</p>
</div>
</div>
<div class="grid min-h-[180px] gap-x-4 gap-y-3 grid-cols-[auto_1fr] items-center py-3">
<p class="text-slate-500 dark:text-slate-400 text-xs font-semibold leading-normal uppercase tracking-wider">Prospección</p>
<div class="h-2 flex-1 rounded-full bg-slate-200 dark:bg-slate-700"><div class="bg-primary h-full rounded-full" style="width: 100%;"></div></div>
<p class="text-slate-500 dark:text-slate-400 text-xs font-semibold leading-normal uppercase tracking-wider">Calificación</p>
<div class="h-2 flex-1 rounded-full bg-slate-200 dark:bg-slate-700"><div class="bg-primary h-full rounded-full" style="width: 75%;"></div></div>
<p class="text-slate-500 dark:text-slate-400 text-xs font-semibold leading-normal uppercase tracking-wider">Propuesta</p>
<div class="h-2 flex-1 rounded-full bg-slate-200 dark:bg-slate-700"><div class="bg-primary h-full rounded-full" style="width: 60%;"></div></div>
<p class="text-slate-500 dark:text-slate-400 text-xs font-semibold leading-normal uppercase tracking-wider">Cierre</p>
<div class="h-2 flex-1 rounded-full bg-slate-200 dark:bg-slate-700"><div class="bg-primary h-full rounded-full" style="width: 24%;"></div></div>
</div>
</div>
</section>
<header class="px-4 pb-2 pt-4">
<h2 class="text-slate-900 dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em]">Oportunidades Recientes</h2>
</header>
<!-- Lista dinámica de ventas -->
<section class="flex flex-col gap-4 p-4">
    <?php if (!empty($sales)): ?>
        <?php foreach ($sales as $sale): ?>
            <div class="flex items-start justify-between rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50 p-4 gap-4">
                <div class="flex flex-col items-stretch justify-start grow">
                    <div class="flex w-full grow flex-col items-stretch justify-center gap-1">
                        <p class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]"><?php echo htmlspecialchars($sale['client_name']); ?></p>
                        <p class="text-slate-600 dark:text-slate-400 text-sm font-normal leading-normal">Monto: €<?php echo number_format($sale['monto'], 2); ?></p>
                        <div class="mt-2 flex flex-col gap-1">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">Etapa: <?php echo htmlspecialchars(ucfirst($sale['estado'])); ?></p>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">Fecha de Venta: <?php echo date("d M Y", strtotime($sale['fecha_venta'])); ?></p>
                        </div>
                    </div>
                </div>
                <div class="flex-none">
                    <form method="POST" action="ventas.php" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta venta?');">
                        <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
                        <button type="submit" name="delete_sale" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent text-slate-500 hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="p-4 text-sm text-slate-500 dark:text-slate-400 text-center">
            No hay oportunidades de venta para mostrar.
        </div>
    <?php endif; ?>
</section>
</main>
<nav class="fixed bottom-0 left-0 right-0 z-10 grid grid-cols-5 justify-around border-t border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 p-2 backdrop-blur-sm">
<a href="index.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">group</span>
<span class="text-xs font-medium">Clientes</span>
</a>
<a class="flex flex-col items-center justify-center gap-1 text-primary dark:text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">trending_up</span>
<span class="text-xs font-bold">Ventas</span>
</a>
<a href="task.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">task_alt</span>
<span class="text-xs font-medium">Tareas</span>
</a>
<a href="informes.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">analytics</span>
<span class="text-xs font-medium">Informes</span>
</a>
</nav>
</div>
<script src="../js/ventas.js"></script>
<script src="../js/addVentas.js"></script>
<body></body>
</html>