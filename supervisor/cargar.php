
<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    // Aquí deberías validar las credenciales del usuario
    $_SESSION['loggedin'] = true; // Establecer la sesión como iniciada
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir todas las sesiones
    header("Location: ../index.php");

// Redirigir al usuario a la página de inicio
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

if (isset($_POST['descripcion_departamento'])) {
$descripcion_departamento = $_POST['descripcion_departamento'];

$sql = "INSERT INTO departamento (descripcion) VALUES ('$descripcion_departamento')";

if ($conn->query($sql) === TRUE) {
    $mensaje = "Nuevo departamento creado con éxito";
} else {
    $mensaje = "Error: " . $sql . "<br>" . $conn->error;
}
}

if (isset($_POST['descripcion_cargo'])) {
$descripcion_cargo = $_POST['descripcion_cargo'];

$sql = "INSERT INTO cargo (descripcion) VALUES ('$descripcion_cargo')";

if ($conn->query($sql) === TRUE) {
    $mensaje = "Nuevo cargo creado con éxito";
} else {
     $mensaje = "Error: " . $sql . "<br>" . $conn->error;
}
}

$conn->close();
        }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Formularios de Municipalidad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; /* Verde agua */
            color: #00796b; /* Verde oscuro */
            margin: 0;
            padding-top: 100px; /* Espacio para el header */
        }

        .container {
            width: 40%;
            margin: auto;
            padding: 20px;
            background-color: #ffffff; /* Fondo blanco */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #004d40; /* Verde más oscuro */
        }

        form {
            margin-bottom: 40px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #004d40;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            background-color: #00796b; /* Verde oscuro */
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #004d40;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        header {
            background-color: #ffffff; /* Fondo blanco */
            color: #004d40;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 100vw;
            box-sizing: border-box;
            border-bottom: 1px solid #b0bec5; /* Línea inferior en gris claro */
            margin-bottom: 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .logo img {
            height: 50px;
        }
        nav {
            display: flex;
            align-items: center;
            position: relative;
        }
        .dropdown-trigger {
            position:relative;
            
        }
        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            border: 1px solid #00796b;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease-in-out;
            opacity: 0;
            transform: translateY(-20px);
            
        }
        .dropdown a {
            display: block;
            padding: 10px 20px;
            color: #004d40;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            
        }
        .dropdown a:hover {
            background-color: #e0f2f1;
        }
        .dropdown-trigger:hover .dropdown {
            display: block;
            opacity: 1;
            transform: translateY(0);
            

        }
        .menu-toggle {
            display: block;
            cursor: pointer;
        }

        .user-info {
            display: flex;
            flex-direction: column; /* Para apilar el ícono y el username */
            align-items: flex-start; /* Alinear a la izquierda */
            color: #004d40; /* Color del texto */
            margin-left: 20px; /* Ajustar margen a la izquierda */
        }
        .user-info i {
            color: #00796b; /* Color del icono */
            font-size: 2em; /* Tamaño del icono */
            margin-left: -80px;
        }
        .user-info span {
            font-size: 1rem; /* Tamaño de fuente para el username */
            margin-top: 5px; /* Espaciado entre el icono y el username */
            margin-left: -85px;
        }
       
    </style>
</head>
<header>
    <div class="logo">
        <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
    </div>
    <nav>
    <div class="dropdown-trigger">
        <div class="menu-toggle" onclick="toggleNav()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="dropdown">
            <a href="mostrar_datos.php">Ver Empleados</a>
            <a href="gestionar_datos.php">Eliminar o Editar Empleados</a>
            <a href="muestra.php">Ver Departamentos y Cargos</a>
            <a href="cargar.php">Cargar Departamentos y Cargos</a>
            <a href="ver_huella.php">Ingresar empleado</a>
            <a href="../index.php">Cerrar Sesión</a>
        </div>
    </div>
    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    <?php endif; ?>
</nav>
</header>
<body>

<div class="container">



    <!-- Mostrar mensaje si existe -->
    <?php if ($mensaje): ?>
        <div class="message"><?php echo $mensaje; ?></div>
        
    <?php endif; ?>

    <!-- Formulario de Departamento -->
    <h2>Agregar Departamento</h2>
    <form method="POST" action="">
        <label for="descripcion_departamento">Descripción del Departamento:</label>
        <input type="text" id="descripcion_departamento" name="descripcion_departamento" required>
        <input type="submit" value="Agregar Departamento">
    </form>

    <!-- Formulario de Cargo -->
    <h2>Agregar Cargo</h2>
    <form method="POST" action="">
        <label for="descripcion_cargo">Descripción del Cargo:</label>
        <input type="text" id="descripcion_cargo" name="descripcion_cargo" required>
        <input type="submit" value="Agregar Cargo">
    </form>
</div>
<script>
    function toggleNav() {
        const dropdown = document.querySelector('.dropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
</script>
</body>
</html>
