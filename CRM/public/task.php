<?php

include '../config/config.php';
$tasks = [];
$sql_condition = '';
$bind_params = [];
$bind_types = '';

// Handle POST requests first, as they might change data and require a fresh fetch
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['toggle_task_status'])) {
        $task_id = intval($_POST['task_id']);
        // If checkbox is checked, value is '1'. If unchecked, it's not sent, so default to 0.
        $completed = isset($_POST['completed']) ? 1 : 0; 

        $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->bind_param("ii", $completed, $task_id);
        $stmt->execute();
        $stmt->close();
        header("Location: task.php"); // Redirect to prevent form re-submission
        exit;
    } elseif (isset($_POST['action'])) {
        $action = $_POST['action'];
        $task_id = intval($_POST['task_id']);
        header("Location: task.php"); // Redirect after action
        exit;
    } elseif (isset($_POST['search_query'])) {
        $search_query = "%" . htmlspecialchars(trim($_POST['search_query'])) . "%";
        $sql_condition = " WHERE t.title LIKE ? OR t.description LIKE ?";
        $bind_params = [$search_query, $search_query];
        $bind_types = "ss";
    }
}

// Handle GET requests for filtering
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['filter'])) {
    if ($_GET['filter'] == 'pending') {
        $sql_condition = " WHERE t.completed = 0";
    } elseif ($_GET['filter'] == 'completed') { // Add a 'completed' filter option
        $sql_condition = " WHERE t.completed = 1";
    }
}

// Fetch tasks based on conditions
$sql = "SELECT t.id, t.title, t.description, t.due_date, t.priority, t.completed, c.nombre AS client_name FROM tasks t LEFT JOIN clientes c ON t.client_id = c.id" . $sql_condition . " ORDER BY t.due_date ASC";
$stmt = $conn->prepare($sql);

if (!empty($bind_params)) {
    $stmt->bind_param($bind_types, ...$bind_params);
}
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $tasks[] = $fila;
    }
}
$stmt->close();

$conn->close(); // Close connection once at the very end
?>


<!DOCTYPE html>

<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Gestión de Tareas CRM</title>
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
              "priority-high": "#E76F51",
              "priority-medium": "#E9C46A",
              "priority-low": "#A8DADC"
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
  </head>
<body class="bg-background-light dark:bg-background-dark font-display">
<div class="relative mx-auto flex h-auto min-h-screen w-full max-w-md flex-col overflow-x-hidden">
<header class="sticky top-0 z-10 w-full bg-background-light dark:bg-background-dark/80 backdrop-blur-sm">
<div class="flex items-center p-4 pb-2 justify-between">
<div class="flex size-12 shrink-0 items-center"></div>
<h2 class="text-lg font-bold leading-tight tracking-[-0.015em] text-[#1D2A39] dark:text-white flex-1 text-center">Tareas</h2>
<div class="flex items-center justify-end gap-1">
    <button class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 w-10 bg-transparent text-[#1D2A39] dark:text-white text-base font-bold leading-normal tracking-[0.015em] min-w-0 p-0" id="search-button">
        <span class="material-symbols-outlined">search</span>
    </button>


</div>
</div>
<div>
<div class="flex border-b border-slate-200 dark:border-slate-800 px-4 justify-between">
<a class="flex flex-col items-center justify-center border-b-[3px] pb-[13px] pt-4 flex-1 <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'pending') ? 'border-b-primary text-primary' : 'border-b-transparent text-slate-500 dark:text-slate-400'; ?>" href="task.php?filter=pending">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Pendientes</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] pb-[13px] pt-4 flex-1 <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'completed') ? 'border-b-primary text-primary' : 'border-b-transparent text-slate-500 dark:text-slate-400'; ?>" href="task.php?filter=completed">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Completadas</p>
</a>
</div>
</div>
<div id="search-bar-container" class="hidden p-4 pt-0">
    <form method="POST" action="task.php" class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <span class="material-symbols-outlined text-slate-400 dark:text-slate-500">search</span>
        </div>
        <input type="text" name="search_query" placeholder="Buscar tareas..."
               class="form-input w-full rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 py-3 pl-11 pr-4 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-primary focus:ring-2 focus:ring-primary/50"
               value="<?php echo isset($_POST['search_query']) ? htmlspecialchars($_POST['search_query']) : ''; ?>">
    </form>
</div>
</header>
<main class="flex-grow pb-24">
<div class="flex flex-col gap-1 py-4">
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
            <div class="flex items-center gap-4 bg-background-light dark:bg-background-dark px-4 min-h-[72px] py-2 justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-7 items-center justify-center">
                        <form method="POST" action="task.php">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="toggle_task_status" value="1">
                            <input type="checkbox" name="completed" value="1" onchange="this.form.submit()"
                                class="h-5 w-5 rounded-full border-slate-400 dark:border-slate-600 border-2 bg-transparent text-primary checked:bg-primary checked:border-primary focus:ring-0 focus:ring-offset-0 focus:border-slate-400 dark:focus:border-slate-600"
                                <?php echo $task['completed'] ? 'checked' : ''; ?>/>
                        </form>
                    </div>
                    <div class="flex flex-col justify-center">
                        <p class="text-[#1D2A39] dark:text-white text-base font-medium leading-normal line-clamp-1 <?php echo $task['completed'] ? 'line-through text-slate-500 dark:text-slate-400' : ''; ?>">
                            <?php echo htmlspecialchars($task['title']); ?>
                        </p>
                        <p class="text-[#6C757D] dark:text-slate-400 text-sm font-normal leading-normal line-clamp-2">
                            <?php echo htmlspecialchars($task['description']); ?> <?php echo (!empty($task['client_name'])) ? ' - ' . htmlspecialchars($task['client_name']) : ''; ?> - <?php echo date("d M", strtotime($task['due_date'])); ?>
                        </p>
                    </div>
                </div>
                <div class="shrink-0">
                    <div class="flex size-7 items-center justify-center">
                        <?php
                            $priority_class = '';
                            switch (strtolower($task['priority'])) {
                                case 'high':
                                    $priority_class = 'bg-priority-high';
                                    break;
                                case 'medium':
                                    $priority_class = 'bg-priority-medium';
                                    break;
                                case 'low':
                                    $priority_class = 'bg-priority-low';
                                    break;
                                default:
                                    $priority_class = 'bg-slate-400'; // Default color if priority is not recognized
                                    break;
                            }
                        ?>
                        <div class="h-3 w-3 rounded-full <?php echo $priority_class; ?>"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="p-4 text-sm text-slate-500 dark:text-slate-400 text-center">
            No hay tareas para mostrar.
        </div>
    <?php endif; ?>
</div>
</main>
<a href="addTask.php" class="absolute bottom-24 right-4 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg" title="Añadir Tarea">
    <span class="material-symbols-outlined text-3xl">add</span>
</a>
<nav class="fixed bottom-0 left-0 right-0 z-10 mx-auto max-w-md border-t border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
<div class="flex h-16 items-center justify-around">
<a class="flex flex-col items-center justify-center gap-1 text-slate-500 dark:text-slate-400" href="index.php">
<span class="material-symbols-outlined">group</span>
<span class="text-xs font-medium">Clientes</span>

<a class="flex flex-col items-center justify-center gap-1 text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">task_alt</span>
<span class="text-xs font-bold">Tareas</span>

</div>
</nav>
</div>
<script src="../js/task.js"></script>
</body></html>