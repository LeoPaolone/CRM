<?php
include '../config/config.php';

$clientes = [];

//consultar sql para obtener los datos de clientes
$sql = "SELECT id, nombre, empresa, imagen_perfil FROM clientes ORDER BY nombre ASC";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $clientes[] = $fila;
    }

} else {
    echo "no hay clientes";

}
$conn->close();


?>


<!DOCTYPE html>
<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Lista de Clientes</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
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
<body class="bg-background-light dark:bg-background-dark">
<div class="relative flex h-auto min-h-screen w-full flex-col dark group/design-root overflow-x-hidden font-display" style='font-family: Manrope, "Noto Sans", sans-serif;'>
<div class="flex flex-col grow pb-24">
<header class="sticky top-0 z-10 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm p-4 pt-6">
<div class="flex items-center justify-between">
<h1 class="text-slate-900 dark:text-white text-3xl font-bold">Clientes</h1>
<button class="flex items-center justify-center h-12 w-12 rounded-full bg-slate-200 dark:bg-slate-800">
<span class="material-symbols-outlined text-slate-700 dark:text-slate-200" style="font-size: 28px;">person</span>
</button>
</div>
</header>
<main class="flex-grow p-4">
<div class="flex flex-col gap-6">
<div class="flex gap-2">
<div class="relative flex-grow">
<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
<span class="material-symbols-outlined text-slate-400 dark:text-slate-500">search</span>
</div>
<input class="form-input w-full rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-3 pl-11 pr-4 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/50" placeholder="Buscar clientes..." type="text" id="client-search-input"/>
</div>

</button>
</div>
<!-- Contenedor de la lista de clientes -->
<div class="flex flex-col gap-3">
    <?php if (!empty($clientes)): ?>
        <?php foreach ($clientes as $cliente): ?> 
            <div class="flex items-center gap-4 rounded-xl bg-white dark:bg-slate-900/40 p-4 border border-slate-200 dark:border-slate-800" data-client-id="<?php echo htmlspecialchars($cliente['id']); ?>">
                <?php if (!empty($cliente['imagen_perfil'])): ?>
                    <img alt="<?php echo htmlspecialchars($cliente['nombre']); ?>" class="h-12 w-12 rounded-full object-cover" src="<?php echo htmlspecialchars($cliente['imagen_perfil']); ?>"/>
                <?php else: ?>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/20">
                        <span class="text-lg font-bold text-primary"><?php echo strtoupper(substr($cliente['nombre'], 0, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex-grow">
                    <p class="font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($cliente['nombre']); ?></p>
                    <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($cliente['empresa']); ?></p>
                </div>
                <button class="relative text-slate-400 dark:text-slate-500 more-options-button">
                    <span class="material-symbols-outlined">more_vert</span>
                </button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="p-4 text-sm text-slate-500 dark:text-slate-400 text-center">
            No hay clientes para mostrar. ¡Añade uno nuevo!
        </div>
    <?php endif; ?>
</div>
</div>
</main>
</div>
<div class="fixed bottom-24 right-4 z-20">
<a href="addClient.php" class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-white shadow-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-background-dark">
    <span class="material-symbols-outlined" style="font-size: 32px;">add</span>
</a>
</div>
<nav class="fixed bottom-0 left-0 right-0 z-10 border-t border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
<div class="mx-auto flex h-20 max-w-md justify-around">
<a class="flex flex-col items-center justify-center gap-1 text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span>
<span class="text-xs font-bold">Clientes</span>
</a>
<a href="ventas.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">receipt_long</span>
<span class="text-xs font-medium">Ventas</span>
</a>
<a href="task.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">task_alt</span>
<span class="text-xs font-medium">Tareas</span>
</a>
<a href="informes.php" class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">bar_chart</span>
<span class="text-xs font-medium">Informes</span>
</a>
</div>
</nav>
</div>
<script src="../js/index.js"></script>

</body></html>