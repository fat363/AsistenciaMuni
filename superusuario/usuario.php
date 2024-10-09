<?php
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
// Iniciar sesión
session_start();

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    $_SESSION['loggedin'] = true;
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}
// Eliminar usuario si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM usuarios WHERE id_usuarios='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Usuario eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el usuario: " . $conn->error . "');</script>";
    }
}

// Obtener usuarios
$sql = "SELECT id_usuarios, usuario, contraseña FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Lista de Usuarios</title>
    <style>
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

        header {
            background-color: #ffffff; /* Fondo blanco para el header */
            color: #01579b; /* Color del texto en tono celeste oscuro */
            padding: 10px 20px; /* Espaciado interno del header */
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 100vw; /* Asegura que el header ocupe todo el ancho de la ventana */
            box-sizing: border-box; /* Incluye el padding y border en el ancho total */
            border-bottom: 1px solid #b0bec5; /* Línea inferior del header */
            position: fixed; /* Fija el header en la parte superior */
            top: 0; /* Ajusta el header al borde superior */
            left: 0; /* Ajusta el header al borde izquierdo */
            z-index: 1000; /* Asegura que el header esté por encima de otros elementos */
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd; /* Azul claro */
            color: #0d47a1; /* Azul oscuro */
            margin: 0;
            padding: 0;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #bbdefb; /* Azul claro para los bordes */
        }
        th {
            background-color: #1976d2; /* Azul medio */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #bbdefb; /* Azul claro alternativo */
        }
        button {
            background-color: #e57373; /* Color rojo suave */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover {
            background-color: #d32f2f; /* Rojo más oscuro al pasar el ratón */
        }
        .logo img {
            height: 50px;
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
            background-color: var(--azul-secundario); /* Color de fondo del botón */
            color: var(--blanco); /* Color del texto del botón */
            padding: 10px 20px; /* Espaciado interno */
            border: none; /* Sin borde */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            font-size: 16px; /* Tamaño de la fuente */
            transition: background-color 0.3s; /* Transición suave */
        }

        .menu-btn:hover {
            background-color: var(--azul-claro); /* Color de fondo al pasar el ratón */
        }
        h1 {
            color: #01579b; /* Color del título en verde medio */
            text-align: center;
            margin-bottom: 20px;
            padding-top: 80px;
            font-size: 1.5rem;
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
            <span> <?php echo $_SESSION['username'] ?? 'Invitado'; ?></span>
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

<h1 style="text-align: center;">Lista de Usuarios</h1>

<table>
    <tr>
        <th>Usuario</th>
        <th></th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        // Salida de cada fila
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["usuario"]. "</td>
                    <td>
                        <form action='' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='" . $row["id_usuarios"] . "'>
                            <button type='submit'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No hay usuarios encontrados</td></tr>";
    }
    $conn->close();
    ?>
</table>
<script>
    function toggleMenu() {
        const menu = document.getElementById('nav-menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    window.onclick = function(event) {
        if (!event.target.matches('.menu-btn') && !event.target.matches('.nav-menu') && !event.target.matches('.nav-menu *')) {
            const menu = document.getElementById('nav-menu');
            menu.style.display = 'none';
        }
    }
</script>
</body>
</html>
