<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    $_SESSION['loggedin'] = true; 
    $_SESSION['username'] = 'nombre_usuario'; 
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir todas las sesiones
    header("Location: ../index.php"); // Redirigir al usuario a la página de inicio
    exit();
}

// Mensaje de éxito o error
$mensaje = ''; 

// Verificar si se recibió una solicitud POST
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

    // Registrar Departamento
    if (isset($_POST['descripcion_departamento'])) {
        $descripcion_departamento = $conn->real_escape_string($_POST['descripcion_departamento']);
        $sql = "INSERT INTO departamento (descripcion) VALUES ('$descripcion_departamento')";

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Nuevo departamento creado con éxito";
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Registrar Cargo
    if (isset($_POST['descripcion_cargo'])) {
        $descripcion_cargo = $conn->real_escape_string($_POST['descripcion_cargo']);
        $sql = "INSERT INTO cargo (descripcion) VALUES ('$descripcion_cargo')";

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Nuevo cargo creado con éxito";
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Cerrar conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularios de Municipalidad</title>
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
        --rojo-suave: #ef5350;
        --rojo-oscuro: #c62828;
        --gris-claro: #b0bec5;
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
        background-color: var(--celeste-claro);
        color: var(--azul-oscuro);
        margin: 0;
        padding-top: 100px; /* Espacio para el header */
    }

    .container {
        width: 40%;
        margin: auto;
        padding: 20px;
        background-color: var(--blanco);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1, h2 {
        text-align: center;
        color: var(--azul-medio);
    }

    form {
        margin-bottom: 40px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 10px;
        color: #0d47a1; /* Azul fuerte */
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid var(--azul);
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: var(--azul);
        color: var(--blanco);
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: var(--azul-primario);
    }

    .message {
        text-align: center;
        margin-top: 20px;
        font-weight: bold;
        color: var(--azul-medio);
    }

    header {
        background-color: var(--blanco);
        color: var(--azul-oscuro);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 100vw;
        box-sizing: border-box;
        border-bottom: 3px solid var(--celeste);
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

     

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            width: 90%;
        }
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
            <span><?php echo $_SESSION['username'] ?? 'Invitado'; ?></span>
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
   
    <form method="post">
        <h2>Registrar Departamento</h2>
        <label for="descripcion_departamento">Descripción del Departamento:</label>
        <input type="text" name="descripcion_departamento" id="descripcion_departamento" required>
        <input type="submit" value="Registrar Departamento">
    </form>

    <form method="post">
        <h2>Registrar Cargo</h2>
        <label for="descripcion_cargo">Descripción del Cargo:</label>
        <input type="text" name="descripcion_cargo" id="descripcion_cargo" required>
        <input type="submit" value="Registrar Cargo">
    </form>

    <?php if ($mensaje): ?>
        <div class="message"><?php echo $mensaje; ?></div>
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
</script>
</body>
</html>
