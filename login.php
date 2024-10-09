<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Manejo de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
$servername = "localhost";
$username = "c6bd_ipet363";
$password = "bd_ipet363";
$dbname = "c6municipalidad2024";
// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variable para almacenar el mensaje de error
$error_message = "";

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    // Preparar y ejecutar la consulta para obtener el usuario y la contraseña
    $stmt = $conn->prepare("SELECT id_usuarios, usuario, contraseña FROM usuarios WHERE usuario = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id_usuario, $db_usuario, $db_contraseña);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();
        // Verificar la contraseña
        if (password_verify($contraseña, $db_contraseña)) {
            // Obtener el rol del usuario
           
            $stmt_rol = $conn->prepare("
                SELECT rol.id_rol, rol.descripcion 
                FROM usuario_rol 
                JOIN rol ON usuario_rol.id_rol = rol.id_rol 
                WHERE usuario_rol.id_usuarios = ?
            ");
            if (!$stmt_rol) {
                die("Error en la preparación de la consulta de rol: " . $conn->error);
            }

            $stmt_rol->bind_param("i", $id_usuario);
            $stmt_rol->execute();
            $stmt_rol->bind_result($id_rol, $descripcion_rol);
            $stmt_rol->fetch();

            // Almacenar el nombre de usuario en la sesión
            $_SESSION['username'] = $db_usuario;

           // Redirigir según el rol
if ($id_rol == 1 && $descripcion_rol == 'empleado') {
    header("Location: empleado/formulario_emple.php");
    exit(); // Asegúrate de usar exit después de header
} elseif ($id_rol == 2 && $descripcion_rol == 'supervisor') {
    header("Location: supervisor/mostrar_datos.php");
    exit(); // Asegúrate de usar exit después de header
} elseif ($id_rol == 3 && $descripcion_rol == 'superusuario') {
    header("Location: superusuario/mostrar_datos.php");
    exit(); // Asegúrate de usar exit después de header
} else {
    echo "Rol no reconocido o acceso no autorizado.";
}

            // Cerrar la declaración del rol
            $stmt_rol->close();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
        } else {
         $error_message = "Usuario no encontrado.";
        }

    // Cerrar las declaraciones
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #d7f6e5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1000px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .image-section {
            width: 50%;
            background: linear-gradient(to right, #44add1, #44add1);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .image-section img {
            width: 90%;
            height: auto;
        }

        .login-section {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .user-login-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-login-logo img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .user-login-logo h2 {
            color: #555;
            font-size: 22px;
            font-weight: bold;
        }

        .login-form {
            width: 90%;
        }

        label {
            color: #555;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-forgot label {
            margin: 0;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            background: #2b99bf;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background: #44add1;
        }

        .error-message {
            color: #ff4d4d;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="lo.jpeg" alt="Imagen de inicio de sesión">
        </div>
        
        <div class="login-section">
            <div class="user-login-logo">
                <svg width="100" height="100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="8" r="5" fill="#00BFFF" />
                    <path d="M12 14c-4 0-7 3-7 7v1h14v-1c0-4-3-7-7-7z" fill="#00BFFF" />
                </svg>

                <h2>INICIAR SESION</h2>
            </div>
            <form class="login-form" action="login.php" method="post">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
                <div class="password-container">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                    <span class="toggle-password" onclick="togglePassword()">🔒</span>
                </div>
                <input type="submit" class="login-btn" value="Iniciar sesión">
            </form>
            <?php if (!empty($error_message)) : ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('contraseña');
            const togglePasswordBtn = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePasswordBtn.textContent = '🔓';
            } else {
                passwordField.type = 'password';
                togglePasswordBtn.textContent = '🔒';
            }
        }
    </script>
</body>
</html>
