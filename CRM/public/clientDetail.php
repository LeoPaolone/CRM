<?php

include '../config/config.php';

$message = '';
$message_type = '';

if (isset($_GET['id'])) {
    $client_id = intval($_GET['id']);

    $sql = "SELECT * FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        echo "Cliente no encontrado.";
        exit;
    }
    $stmt->close();
} else {
    echo "ID de cliente no proporcionado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_client'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $company = htmlspecialchars(trim($_POST['company']));
        $address = htmlspecialchars(trim($_POST['address']));

        $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, empresa = ?, direccion = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $phone, $company, $address, $client_id);

        if ($stmt->execute()) {
            // Actualizar los datos del cliente en la variable $client para que se reflejen en el formulario
            $client['nombre'] = $name;
            $client['email'] = $email;
            $client['telefono'] = $phone;
            $client['empresa'] = $company;
            $client['direccion'] = $address;
            $message = 'Cliente actualizado con éxito.';
            $message_type = 'bg-green-500';
        } else {
            $message = 'Error al actualizar el cliente: ' . $stmt->error;
            $message_type = 'bg-red-500';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_client'])) {
        $sql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $client_id);
        
        if ($stmt->execute()) {
            header("Location: index.php?message=deleted");
            exit;
        } else {
            $message = 'Error al eliminar el cliente: ' . $stmt->error;
            $message_type = 'bg-red-500';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Detalles de <?php echo htmlspecialchars($client['nombre']); ?></title>
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
      min-height: 100dvh;
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
<div class="flex flex-col grow">
<header class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4">
<a href="index.php" class="flex items-center justify-center p-2 text-slate-500 dark:text-slate-400">
    <span class="material-symbols-outlined">arrow_back_ios_new</span>
</a>
<h1 class="text-lg font-bold text-slate-900 dark:text-white flex-1 text-center">Detalles del Cliente</h1>
<a href="logout.php" class="flex items-center justify-center p-2 text-slate-500 dark:text-slate-400" title="Cerrar Sesión">
    <span class="material-symbols-outlined">logout</span>
</a>
</header>
<form method="POST" action="clientDetail.php?id=<?php echo $client_id; ?>">
    <main class="flex-grow overflow-y-auto p-4">
        <div class="flex flex-col gap-6">
            <?php if (!empty($message)): ?>
                <div class="p-4 text-white rounded-lg <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <div class="flex flex-col items-center gap-4">
                <?php if (!empty($client['imagen_perfil'])): ?>
                    <img alt="<?php echo htmlspecialchars($client['nombre']); ?>" class="h-24 w-24 rounded-full object-cover" src="<?php echo htmlspecialchars($client['imagen_perfil']); ?>"/>
                <?php else: ?>
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-primary/20">
                        <span class="text-3xl font-bold text-primary"><?php echo strtoupper(substr($client['nombre'], 0, 2)); ?></span>
                    </div>
                <?php endif; ?>
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($client['nombre']); ?></h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">ID Cliente: <?php echo htmlspecialchars($client['id']); ?></p>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <label class="flex flex-col">
                    <p class="text-slate-800 dark:text-slate-200 text-sm font-medium pb-1.5">Nombre completo</p>
                    <input name="name" class="form-input w-full rounded-lg border-slate-300 bg-white p-3 text-slate-900 focus:border-primary focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white" type="text" value="<?php echo htmlspecialchars($client['nombre']); ?>"/>
                </label>
                <label class="flex flex-col">
                    <p class="text-slate-800 dark:text-slate-200 text-sm font-medium pb-1.5">Correo electrónico</p>
                    <input name="email" class="form-input w-full rounded-lg border-slate-300 bg-white p-3 text-slate-900 focus:border-primary focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white" type="email" value="<?php echo htmlspecialchars($client['email']); ?>"/>
                </label>
                <label class="flex flex-col">
                    <p class="text-slate-800 dark:text-slate-200 text-sm font-medium pb-1.5">Teléfono</p>
                    <input name="phone" class="form-input w-full rounded-lg border-slate-300 bg-white p-3 text-slate-900 focus:border-primary focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white" type="tel" value="<?php echo htmlspecialchars($client['telefono']); ?>"/>
                </label>
                <label class="flex flex-col">
                    <p class="text-slate-800 dark:text-slate-200 text-sm font-medium pb-1.5">Empresa</p>
                    <input name="company" class="form-input w-full rounded-lg border-slate-300 bg-white p-3 text-slate-900 focus:border-primary focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white" type="text" value="<?php echo htmlspecialchars($client['empresa']); ?>"/>
                </label>
                 <label class="flex flex-col">
                    <p class="text-slate-800 dark:text-slate-200 text-sm font-medium pb-1.5">Dirección</p>
                    <textarea name="address" class="form-textarea w-full rounded-lg border-slate-300 bg-white p-3 text-slate-900 focus:border-primary focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white" rows="3"><?php echo htmlspecialchars($client['direccion']); ?></textarea>
                </label>
            </div>
            <div class="flex flex-col gap-3 pt-2">
                <button type="submit" name="update_client" class="flex h-12 w-full items-center justify-center rounded-lg bg-primary text-base font-bold text-white hover:bg-primary/90">
                    Actualizar Cliente
                </button>
                <button type="submit" name="delete_client" class="flex h-12 w-full items-center justify-center rounded-lg border border-red-500/50 bg-transparent text-base font-bold text-red-500 hover:bg-red-500/10 dark:border-red-500/60 dark:text-red-500 dark:hover:bg-red-500/10" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.');">
                    Quitar Cliente
                </button>
            </div>
        </div>
    </main>
</form>
<nav class="flex h-20 shrink-0 items-center justify-around border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-background-dark">
<a class="flex flex-col items-center gap-1 text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1, 'wght' 600;">group</span>
<span class="text-xs font-bold">Clientes</span> 
</a>
<a href="ventas.php" class="flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">receipt_long</span>
<span class="text-xs font-medium">Ventas</span>
</a>
<a href="task.php" class="flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">task_alt</span>
<span class="text-xs font-medium">Tareas</span>
</a>
<a href="informes.php" class="flex flex-col items-center gap-1 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined">analytics</span>
<span class="text-xs font-medium">Informes</span>
</a>
</nav>
</div>
</div>
<script src="../js/clientDetail.js"></script>

</body></html>
