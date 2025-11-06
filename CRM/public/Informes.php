<?php
include '../config/config.php';

// Obtener el total de ingresos del mes actual
$current_month_start = date('Y-m-01 00:00:00');
$current_month_end = date('Y-m-t 23:59:59');

$sql_ingresos_mes = "SELECT SUM(monto) AS total_ingresos FROM ventas WHERE fecha_venta BETWEEN ? AND ? AND estado = 'completado'";
$stmt_ingresos_mes = $conn->prepare($sql_ingresos_mes);
$stmt_ingresos_mes->bind_param("ss", $current_month_start, $current_month_end);
$stmt_ingresos_mes->execute();
$resultado_ingresos_mes = $stmt_ingresos_mes->get_result();
$ingresos_mes = $resultado_ingresos_mes->fetch_assoc()['total_ingresos'] ?? 0;
$stmt_ingresos_mes->close();

// Obtener el número de nuevas oportunidades (ventas pendientes) del mes actual
$sql_nuevas_oportunidades = "SELECT COUNT(id) AS nuevas_oportunidades FROM ventas WHERE fecha_venta BETWEEN ? AND ? AND estado = 'pendiente'";
$stmt_nuevas_oportunidades = $conn->prepare($sql_nuevas_oportunidades);
$stmt_nuevas_oportunidades->bind_param("ss", $current_month_start, $current_month_end);
$stmt_nuevas_oportunidades->execute();
$resultado_nuevas_oportunidades = $stmt_nuevas_oportunidades->get_result();
$nuevas_oportunidades = $resultado_nuevas_oportunidades->fetch_assoc()['nuevas_oportunidades'] ?? 0;
$stmt_nuevas_oportunidades->close();

// Obtener la tasa de cierre (ejemplo: ventas completadas / total de ventas)
$sql_total_ventas = "SELECT COUNT(id) AS total_ventas FROM ventas WHERE fecha_venta BETWEEN ? AND ?";
$stmt_total_ventas = $conn->prepare($sql_total_ventas);
$stmt_total_ventas->bind_param("ss", $current_month_start, $current_month_end);
$stmt_total_ventas->execute();
$resultado_total_ventas = $stmt_total_ventas->get_result();
$total_ventas = $resultado_total_ventas->fetch_assoc()['total_ventas'] ?? 0;
$stmt_total_ventas->close();

$tasa_cierre = ($total_ventas > 0) ? round(($ingresos_mes / $total_ventas) * 100, 2) : 0; // Esto es un ejemplo, la tasa de cierre real sería (completadas / (completadas + pendientes))

// Obtener el pipeline de ventas (suma de montos de ventas pendientes)
$sql_pipeline = "SELECT SUM(monto) AS total_pipeline FROM ventas WHERE estado = 'pendiente'";
$stmt_pipeline = $conn->prepare($sql_pipeline);
$stmt_pipeline->execute();
$resultado_pipeline = $stmt_pipeline->get_result();
$pipeline_ventas = $resultado_pipeline->fetch_assoc()['total_pipeline'] ?? 0;
$stmt_pipeline->close();

// Obtener las ventas recientes (últimas 5)
$sql_ventas_recientes = "SELECT v.id, c.nombre AS client_name, v.monto, v.estado, v.fecha_venta FROM ventas v JOIN clientes c ON v.cliente_id = c.id ORDER BY v.fecha_venta DESC LIMIT 5";
$resultado_ventas_recientes = $conn->query($sql_ventas_recientes);
$ventas_recientes = [];
if ($resultado_ventas_recientes->num_rows > 0) {
    while ($fila = $resultado_ventas_recientes->fetch_assoc()) {
        $ventas_recientes[] = $fila;
    }
}

?>

<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Informes</title>
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
                    <h1 class="text-slate-900 dark:text-white text-3xl font-bold">Informes</h1> 
                    <a href="index.php" class="flex items-center justify-center h-12 w-12 rounded-full bg-slate-200 dark:bg-slate-800" title="Recargar Informes">
                        <span class="material-symbols-outlined text-slate-700 dark:text-slate-200" style="font-size: 28px;">assessment</span>
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-grow p-4">
                <div class="flex flex-col gap-6">
                    <!-- Search and Filter -->
                    <div class="flex gap-2">
                        <div class="relative flex-grow">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <span class="material-symbols-outlined text-slate-400 dark:text-slate-500">search</span>
                            </div>
                            <input class="form-input w-full rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-3 pl-11 pr-4 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/50" placeholder="Buscar informes..." type="text"/>
                        </div>
                       
                    </div>

                    <!-- Reports List -->
                    <!-- Sección de Métricas -->
                    <div class="flex flex-col gap-4">
                        <!-- Example Report Card 1 -->
                        <div class="rounded-xl bg-white dark:bg-slate-900/40 p-4 border border-slate-200 dark:border-slate-800">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white">Informe de Ventas</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Filtros de fecha:</p>
                                </div> 
                                <span class="material-symbols-outlined text-primary">description</span>
                            </div>
                            <div class="mt-3 flex flex-col md:flex-row gap-2 items-center">
                                <input type="date" id="start_date" name="start_date" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 text-sm p-2 dark:text-white">
                                <input type="date" id="end_date" name="end_date" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 text-sm p-2 dark:text-white">
                                <button class="text-primary hover:text-primary/80 text-sm font-medium">
                                    Descargar
                                </button>
                            </div>
                        </div>

                        <!-- Example Report Card 2 -->
                        <div class="rounded-xl bg-white dark:bg-slate-900/40 p-4 border border-slate-200 dark:border-slate-800">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white">Informe de Clientes</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Activos al 31/10/2023</p>
                                </div>
                                <span class="material-symbols-outlined text-primary">groups</span>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Generado: 30/10/2023</span>
                                <button class="text-primary hover:text-primary/80 text-sm font-medium">
                                    Descargar
                                </button>
                            </div>
                        </div>

                        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
                            <p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Ingresos del Mes</p>
                            <p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight">€<?php echo number_format($ingresos_mes, 2); ?></p>
                        </div>
                        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
                            <p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Nuevas Oportunidades</p>
                            <p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight"><?php echo $nuevas_oportunidades; ?></p>
                        </div>
                        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-4 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
                            <p class="text-slate-600 dark:text-slate-400 text-sm font-medium leading-normal">Tasa de Cierre</p>
                            <p class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight"><?php echo $tasa_cierre; ?>%</p>
                        </div>

                        <div class="flex min-w-72 flex-1 flex-col gap-4 rounded-lg bg-white dark:bg-slate-800/50 p-6 border border-slate-200 dark:border-slate-800">
                            <div class="flex flex-col gap-1">
                                <p class="text-slate-900 dark:text-white text-lg font-bold leading-normal">Pipeline de Ventas</p>
                                <p class="text-slate-900 dark:text-white tracking-tight text-[32px] font-bold leading-tight truncate">€<?php echo number_format($pipeline_ventas, 2); ?></p>
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

                        <h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] mt-4">Oportunidades Recientes</h2>
                        <?php if (!empty($ventas_recientes)): ?>
                            <?php foreach ($ventas_recientes as $sale): ?>
                                <div class="flex flex-col items-stretch justify-start rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50 p-4">
                                    <div class="flex w-full grow flex-col items-stretch justify-center gap-1">
                                        <p class="text-slate-600 dark:text-slate-400 text-sm font-normal leading-normal">€<?php echo number_format($sale['monto'], 2); ?></p>
                                        <p class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]"><?php echo htmlspecialchars($sale['client_name']); ?></p>
                                        <div class="mt-2 flex flex-col gap-1">
                                            <p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">Etapa: <?php echo htmlspecialchars(ucfirst($sale['estado'])); ?></p>
                                            <p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">Cierre esperado: <?php echo date("d M Y", strtotime($sale['fecha_venta'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-sm text-slate-500 dark:text-slate-400 text-center">
                                No hay oportunidades de venta recientes para mostrar.
                            </div>
                        <?php endif; ?>





                    </div>

                    <!-- Add Report Button (Fixed at bottom) -->
                  <div class="fixed bottom-24 right-4 z-20 sm:right-6">
    <a href="addInformes.php" class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg transition-colors hover:bg-primary/90" title="Añadir Informe">
        <span class="material-symbols-outlined text-2xl">add</span>
    </a>
</div>
                </div>
            </main>
        </div>
    </div>
</body>
<script src="../js/informes.js"></script>
</html>