<?php
include '../config/config.php';

$message = "";
$toastclase = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $confirm_password = $_POST["confirm_password"];

    // 1. Verificar si las contraseñas coinciden
    if($password != $confirm_password){
        $message = "Las contraseñas no coinciden";
        $toastclase = "bg-red-500";
    } else {
        // 2. Si coinciden, verificar si el correo ya existe
        $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if($checkEmailStmt->num_rows > 0){
            $message = "El correo electrónico ya está en uso";
            $toastclase = "bg-red-500";
        } else {
            // 3. Si el correo no existe, cifrar contraseña e insertar usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            // Corregido: "sss" para 3 parámetros string
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if($stmt->execute()){
                $message = "Cuenta creada exitosamente. Redirigiendo a login...";
                $toastclase = "bg-green-500";
                header("refresh:2;url=login.php"); // Redirige a login.php después de 2 segundos
            } else {
                $message = "Error al crear la cuenta: " . $stmt->error;
                $toastclase = "bg-red-500";
            }
            $stmt->close();
        }
        $checkEmailStmt->close();
    }
    $conn->close();
}


?>


<!DOCTYPE html>

<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Crear Cuenta - CRM Móvil</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
              "display": ["Manrope", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
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
  </head>
<body class="bg-background-light dark:bg-background-dark font-display">
<div class="relative flex h-auto min-h-screen w-full flex-col">
<!-- Top App Bar -->
<header class="flex shrink-0 items-center justify-between p-4 bg-background-light dark:bg-background-dark">
<a href="login.php" class="flex size-10 items-center justify-center text-slate-700 dark:text-slate-300">
    <span class="material-symbols-outlined">arrow_back</span>
</a>
<h1 class="flex-1 text-center text-xl font-bold tracking-tight text-slate-900 dark:text-white">Crear Cuenta</h1>
<div class="size-10"></div> <!-- Spacer to balance the header -->
</header>
<main class="flex flex-1 flex-col justify-between px-4 pt-4 pb-8">
    <form method="POST" action="register.php" class="flex flex-col justify-between flex-1">
        <div class="space-y-5">
            <!-- Mensaje de feedback -->
            <?php if(!empty($message)): ?>
                <div class="p-4 text-white rounded-lg <?php echo $toastclase; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Full Name Field -->
            <label class="flex flex-col">
                <p class="pb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Nombre completo</p>
                <input name="username" required class="form-input h-12 w-full flex-1 resize-none overflow-hidden rounded-lg border border-slate-300 bg-background-light p-3 text-base font-normal leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-primary" placeholder="Ingresa tu nombre" type="text" value=""/>
            </label>
            <!-- Email Field -->
            <label class="flex flex-col">
                <p class="pb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Correo electrónico</p>
                <input name="email" required class="form-input h-12 w-full flex-1 resize-none overflow-hidden rounded-lg border border-slate-300 bg-background-light p-3 text-base font-normal leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-primary" placeholder="tu@correo.com" type="email" value=""/>
            </label>
            <!-- Password Field -->
            <label class="flex flex-col">
                <p class="pb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Contraseña</p>
                <div class="relative flex w-full flex-1 items-center">
                    <input name="password" required class="form-input h-12 w-full flex-1 resize-none overflow-hidden rounded-lg border border-slate-300 bg-background-light p-3 pr-12 text-base font-normal leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-primary" placeholder="Crea una contraseña segura" type="password" value=""/>
                    <button type="button" class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 dark:text-slate-500">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
            </label>
            <!-- Confirm Password Field -->
            <label class="flex flex-col">
                <p class="pb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar contraseña</p>
                <div class="relative flex w-full flex-1 items-center">
                    <input name="confirm_password" required class="form-input h-12 w-full flex-1 resize-none overflow-hidden rounded-lg border border-slate-300 bg-background-light p-3 pr-12 text-base font-normal leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-primary" placeholder="Vuelve a escribir la contraseña" type="password" value=""/>
                    <button type="button" class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 dark:text-slate-500">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
                <!-- Este mensaje estático se puede eliminar o controlar con JS para una mejor UX -->
                <!-- <p class="pt-2 text-xs text-red-600 dark:text-red-500">Las contraseñas no coinciden.</p> -->
            </label>
        </div>
        <div class="mt-8 space-y-4">
            <!-- Create Account Button -->
            <button type="submit" class="flex h-12 w-full items-center justify-center rounded-lg bg-primary px-4 text-base font-bold text-white transition-colors hover:bg-primary/90">
                Crear Cuenta
            </button>
            <!-- Login Link -->
            <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                ¿Ya tienes una cuenta? <a class="font-bold text-primary hover:underline" href="login.php">Inicia sesión</a>
            </p>
        </div>
    </form>
</main>
</div>
<script src="../js/register.js"></script>
</body></html>