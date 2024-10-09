<?php
// Iniciar sesión
session_start();

// Conexión a la base de datos
$host = 'localhost'; // Cambia esto si tu base de datos está en otro host
$db = 'c6municipalidad2024'; // Nombre de la base de datos
$user = 'c6bd_ipet363'; // Usuario de la base de datos
$pass = 'bd_ipet363'; // Contraseña del usuario

// Inicializar variables de mensaje
$message = '';
$message_type = '';

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    // Aquí deberías validar las credenciales del usuario desde la base de datos
    // Ejemplo: consulta a la tabla `usuario`
    
    // Si las credenciales son correctas, almacena el nombre de usuario en la sesión
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $_POST['usuario']; // Asigna el nombre de usuario ingresado en el formulario de inicio de sesión
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir todas las sesiones
    header("Location: ../index.php"); // Redirigir al usuario a la página de inicio
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_usuario'])) {
        // Recibir los datos del formulario
        $usuario = $_POST['usuario'];
        $password = $_POST['contraseña']; // Sin encriptar para la comparación
        $confirmPassword = $_POST['confirmar_contraseña'];
        $rol = $_POST['rol'];

        // Verificar si las contraseñas coinciden
        if ($password !== $confirmPassword) {
            $message = "Las contraseñas no coinciden.";
            $message_type = "danger";
        } else {
            // Encriptar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT); 

            // Insertar el usuario en la tabla 'usuarios'
            $query = "INSERT INTO usuarios (usuario, contraseña) VALUES (:usuario, :password)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':password', $hashedPassword);
            $usuarioCreado = $stmt->execute();

            if ($usuarioCreado) {
                // Obtener el id del usuario recién insertado
                $id_usuario = $pdo->lastInsertId();

                // Insertar el rol en la tabla 'usuario_rol'
                $query_rol = "INSERT INTO usuario_rol (id_usuarios, id_rol) VALUES (:id_usuario, :id_rol)";
                $stmt_rol = $pdo->prepare($query_rol);
                $stmt_rol->bindParam(':id_usuario', $id_usuario);
                $stmt_rol->bindParam(':id_rol', $rol);
                $stmt_rol->execute();

                // Mensaje de éxito
                $message = "Usuario creado con éxito.";
                $message_type = "success";
            } else {
                $message = "Error al crear el usuario.";
                $message_type = "danger";
            }
        }
    }
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
    $message_type = "danger";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Paleta de Colores */
        :root {
            --celeste-claro: #e0f7fa;
            --celeste: #81d4fa;
            --azul-claro: #b3e5fc;
            --azul: #0288d1;
            --azul-oscuro: #01579b;
            --azul-medio: #0277bd;
            --blanco: #ffffff;
            --gris-claro: #b0bec5;
            --hover-celeste: #81d4fa;
            --hover-azul: #01579b;
            --azul-primario: #007BFF;        /* Azul Primario */
            --azul-secundario: #5DADE2;      /* Azul Secundario */
            --celeste-primario: #AED6F1;     /* Celeste Primario */
            --celeste-secundario: #D6EAF8;   /* Celeste Secundario */
            --blanco: #ffffff;
            --gris-claro: #b0bec5;
            --negro: #000000;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--celeste-claro); /* Celeste claro */
            margin: 0;
            padding-top: 290px; /* Espacio para el header */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: var(--blanco);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 500px;
            margin-bottom: 20px; /* Espacio inferior para los mensajes */
        }

        h2 {
            text-align: center;
            color: var(--azul-medio); /* Azul medio */
            margin-bottom: 20px;
        }

        label {
            color: var(--azul); /* Azul */
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid var(--azul); /* Azul */
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: var(--azul); /* Azul */
            color: var(--blanco);
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: var(--azul-oscuro); /* Azul oscuro */
        }

        .long-input {
            width: 100%; /* Esto hará que el campo ocupe todo el ancho disponible del contenedor */
            max-width: 400px; /* Puedes ajustar esto al tamaño deseado, opcional */
            padding: 50px; /* Espaciado interno */
            box-sizing: border-box; /* Incluye el padding en el ancho total */
            border: 1px solid #ccc; /* Borde del campo */
            border-radius: 5px; /* Bordes redondeados */
        }

        .input-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--azul); /* Color del icono */
        }


        .message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: var(--azul-medio); /* Azul medio */
        }

        header {
            background-color: var(--blanco);
            color: var(--azul-oscuro);
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        

        .logo img {
            height: 60px;
        }

         /* Contenedor para el Menú y la Información del Usuario */
   .header-right {
            display: flex;
            align-items: center;
            gap: 15px; /* Espacio entre el botón y la información del usuario */
        }

        .user-info {
            display: flex;
            align-items: center;
            color: var(--azul-primario);
            font-size: 1.1rem;
        }

        .user-info i {
            margin-right: 5px;
            font-size: 1.2em;
        }

        .nav-menu {
            display: none;
            position: absolute;
            top: 70px;
            right: 30px;
            background-color: var(--blanco);
            border: 1px solid var(--azul-primario);
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 200px;
            
        }

        .nav-menu a {
            display: block;
            padding: 15px;
            color: var(--azul-primario);
            text-decoration: none;
            text-align: center;
        }

        .nav-menu a:hover {
            background-color: var(--celeste-primario);
        }
        .menu-btn {
            background-color: var(--azul-secundario);
            color: var(--blanco);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .menu-btn:hover {
            background-color: var(--celeste-primario);
        }

        .alert {
            padding: 20px;
            margin: 20px 0; /* Margen ajustado */
            border-radius: 5px;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .alert-success {
            background-color: #d4edda; /* Verde claro */
            color: #155724; /* Texto verde oscuro */
            border: 1px solid #c3e6cb; /* Borde verde */
        }

        .alert-danger {
            background-color: #f8d7da; /* Rojo claro */
            color: #721c24; /* Texto rojo oscuro */
            border: 1px solid #f5c6cb; /* Borde rojo */
        }

    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
    </div>
    <div class="header-right">
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Invitado'); ?></span>
        </div>
        <button class="menu-btn" onclick="toggleMenu()">☰</button>
        <div id="nav-menu" class="nav-menu">
            <a href="mostrar_datos.php">Ver Empleados</a>
            <a href="gestionar_datos.php">Eliminar o Editar Empleados</a>
            <a href="muestra.php">Ver Departamentos y Cargos</a>
            <a href="cargar.php">Cargar Departamentos y Cargos</a>
            <a href="crearusuario.php">Crear usuario</a>
            <a href="usuario.php">Eliminar usuario</a>
            <a href="huella.php">Huella</a>
            <a href="../index.php">Cerrar Sesión</a>
        </div>
    </div>
</header>
    <div class="container">
        <h2>Crear Usuario</h2>
        <form method="POST">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
            <label for="contraseña">Contraseña:</label>
<div class="input-container">
    <input type="password" name="contraseña" id="contraseña" required>
    <i class="fas fa-eye eye-icon" toggle="#contraseña"></i>
</div>

<label for="confirmar_contraseña">Confirmar Contraseña:</label>
<div class="input-container">
    <input type="password" name="confirmar_contraseña" id="confirmar_contraseña" required>
    <i class="fas fa-eye eye-icon" toggle="#confirmar_contraseña"></i>
</div>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="1">Empleados</option>
                <option value="2">Supervisor</option>
            </select>

            <input type="submit" name="crear_usuario" value="Crear Usuario">
        </form>

        <!-- Mostrar el mensaje aquí, debajo del formulario -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>


    <script>
        
    function toggleMenu() {
        const menu = document.getElementById('nav-menu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
    
    // Cerrar el menú si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('.menu-btn') && !event.target.matches('#nav-menu a')) {
            const menu = document.getElementById('nav-menu');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            }
        }
    }

    const eyeIcons = document.querySelectorAll('.eye-icon');
eyeIcons.forEach(icon => {
    icon.addEventListener('click', () => {
        const inputSelector = icon.getAttribute('toggle');
        const passwordField = document.querySelector(inputSelector);
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});

    </script>
</body>
</html>
