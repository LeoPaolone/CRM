<?php
include '../config/config.php';

$message = "";
$toastclase = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Corregido: Se obtiene el email del formulario, no el username.
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Corregido: La consulta ahora también selecciona el username y el id.
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt -> bind_param("s", $email); // Ahora $email está definida.
    $stmt -> execute();
    $stmt -> store_result();

    if($stmt -> num_rows > 0){
        // Corregido: Se bindean los resultados de la consulta (id, username, password).
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Corregido: Se usa password_verify para comparar la contraseña ingresada con el hash de la BD.
        if(password_verify($password, $hashed_password)){
            $message = "Inicio de sesión exitoso";
            $toastclase = "bg-green-500";
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username; // Ahora $username se obtiene de la BD.
            header("Location: ../public/index.php");
            exit();
        } else{
            $message = "Credenciales incorrectas";
            $toastclase = "bg-red-500";
        }
    } else{
        $message = "Usuario no encontrado";
        $toastclase = "bg-red-500";
    }

    $stmt->close();
    $conn->close();
}


?>


<!DOCTYPE html>

<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>CRM Login</title>
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
  </head>
<body class="bg-background-light dark:bg-background-dark">
<div class="relative flex h-auto min-h-screen w-full flex-col dark group/design-root overflow-x-hidden font-display" style='font-family: Manrope, "Noto Sans", sans-serif;'>
<div class="flex w-full grow flex-col items-center justify-center p-4">
<div class="flex w-full max-w-sm flex-col items-center gap-6">
<!-- Logo -->
<div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary">
<span class="material-symbols-outlined text-white" style="font-size: 40px;">hub</span>
</div>
<!-- HeadlineText -->
<h1 class="text-slate-900 dark:text-white tracking-light text-[32px] font-bold leading-tight text-center">Bienvenido</h1>
<!-- Form -->
<!-- Corregido: Se añadió la etiqueta <form> y el bloque para mostrar mensajes -->
<form method="POST" action="login.php" class="flex w-full flex-col items-stretch gap-4">
    <!-- Mensaje de feedback -->
    <?php if(!empty($message)): ?>
        <div class="p-4 mb-2 text-sm text-white rounded-lg <?php echo $toastclase; ?>" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- TextField Email -->
    <label class="flex flex-col flex-1">
        <p class="text-slate-800 dark:text-slate-200 text-base font-medium leading-normal pb-2">Correo electrónico</p>
        <div class="flex w-full flex-1 items-stretch rounded-lg">
            <div class="text-slate-400 dark:text-slate-500 flex border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 items-center justify-center pl-4 rounded-l-lg border-r-0">
                <span class="material-symbols-outlined" data-icon="Envelope">mail</span>
            </div>
            <!-- Corregido: Añadido name="email" y required -->
            <input name="email" required class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/40 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 p-3.5 rounded-l-none border-l-0 text-base font-normal leading-normal" placeholder="tu@email.com" type="email" value=""/>
        </div>
    </label>
    <!-- TextField Password -->
    <label class="flex flex-col flex-1">
        <p class="text-slate-800 dark:text-slate-200 text-base font-medium leading-normal pb-2">Contraseña</p>
        <!-- Modificado: Se añade la estructura para el botón de visibilidad -->
        <div class="relative flex w-full flex-1 items-center">
            <input name="password" required class="form-input h-14 w-full flex-1 resize-none overflow-hidden rounded-lg border border-slate-300 bg-background-light p-3 pr-12 text-base font-normal leading-normal text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-0 focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-primary" placeholder="Ingresa tu contraseña" type="password" value=""/>
            <button type="button" class="absolute right-0 flex h-full w-12 items-center justify-center text-slate-400 dark:text-slate-500">
                <span class="material-symbols-outlined">visibility</span>
            </button>
        </div>
    </label>
    <!-- MetaText -->
    <a class="text-primary dark:text-primary/90 text-sm font-medium leading-normal self-end underline hover:no-underline" href="#">Olvidé mi contraseña</a>
    <!-- Primary Button -->
    <button type="submit" class="flex items-center justify-center h-14 w-full rounded-lg bg-primary text-white text-base font-bold leading-normal mt-4 hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-background-light dark:focus:ring-offset-background-dark focus:ring-primary">
        Iniciar Sesión
    </button>
</form>
<!-- Registration Link -->
<div class="flex items-center gap-2 pt-4">
<p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-normal">¿No tienes una cuenta?</p>
<!-- Corregido: El enlace ahora apunta a register.php -->
<a class="text-primary dark:text-primary/90 text-sm font-bold leading-normal underline hover:no-underline" href="register.php">Regístrate</a>
</div>
</div>
</div>
</div>
<script src="../js/login.js"></script>
</body></html>