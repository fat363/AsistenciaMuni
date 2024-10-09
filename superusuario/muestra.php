<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    // Aquí deberías validar las credenciales del usuario
    $_SESSION['loggedin'] = true; // Establecer la sesión como iniciada
    $_SESSION['username'] = $_POST['username']; // Asignar nombre de usuario
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy(); // Destruir todas las sesiones
    header("Location: ../index.php"); // Redirigir al usuario a la página de inicio
    exit();
}

// Configuración de la base de datos

$username = "c6bd_ipet363";
$password = "bd_ipet363";
$dbname = "c6municipalidad2024";


// Inicializar variables de mensaje
$message = '';
$message_type = '';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar solicitudes de eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $tipo = $_POST['tipo']; // 'depa' o 'cargo'
    $id = intval($_POST['id']); // ID a eliminar

    if ($tipo === 'depa') {
        // Eliminar departamento
        $stmt = $conn->prepare("DELETE FROM departamento WHERE id_depa = ?");
        $stmt->bind_param("i", $id);
    } elseif ($tipo === 'cargo') {
        // Eliminar cargo
        $stmt = $conn->prepare("DELETE FROM cargo WHERE id_cargo = ?");
        $stmt->bind_param("i", $id);
    } else {
        $message = "Tipo de eliminación no válido.";
        $message_type = "danger";
    }

    if (isset($stmt)) {
        if ($stmt->execute()) {
            $message = ucfirst($tipo) . " eliminado con éxito.";
            $message_type = "success";
        } else {
            $message = "Error al eliminar el " . $tipo . ": " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// Consultar tabla departamento
$sql_departamento = "SELECT id_depa, descripcion FROM departamento";
$result_departamento = $conn->query($sql_departamento);

// Consultar tabla cargo
$sql_cargo = "SELECT id_cargo, descripcion FROM cargo";
$result_cargo = $conn->query($sql_cargo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamentos y Cargos - Municipalidad</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      :root {
            --azul-oscuro: #0d47a1;
            --azul-claro: #42a5f5;
            --celeste-claro: #e3f2fd;
            --celeste-medio: #64b5f6;
            --blanco: #ffffff;
            --gris-claro: #b0bec5;
            --rojo: #d32f2f;
            --rojo-oscuro: #c62828;
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
            padding: 0;
            padding-top: 80px;
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
            background-color: var(--celeste-medio); /* Color de fondo al pasar el ratón */
        }

        h1 {
            color: var(--azul-oscuro);
            text-align: center;
            margin: 60px 0 20px;
        }

        h2 {
            color: #0288d1; /* Azul medio */
        }
        .container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 0 20px;
        }
        .table-container {
            width: 45%;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #ffffff; /* Fondo blanco para las tablas */
            border-radius: 5px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #81d4fa; /* Línea celeste claro */
        }
        th {
            background-color: #0288d1; /* Azul medio */
            color: white;
            font-size: 1.1em;
        }
        tr:nth-child(even) {
            background-color: #e1f5fe; /* Azul muy claro */
        }
        tr:hover {
            background-color: #b3e5fc; /* Azul claro al pasar el ratón */
        }
        .delete-btn {
            background-color: #ef5350; /* Rojo suave */
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c62828; /* Rojo más oscuro al pasar el ratón */
        }
        .export-menu a {
            display: block;
            padding: 10px;
            color: var(--azul-oscuro);
            text-decoration: none;
            font-size: 14px;
        }

        .export-menu a:hover {
            background-color: var(--celeste-medio);
        }
        .export-btn {
            background-color: var(--azul-oscuro);
            color: var(--blanco);
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .export-btn:hover {
            background-color: var(--celeste-claro);
        }

        /* Responsivo: Ajustes para pantallas pequeñas */
        @media (max-width: 768px) {
            .user-info {
                display: none; /* Ocultar información del usuario en pantallas pequeñas */
            }
            .table-container {
                width: 100%;
            }
        }

        /* Estilos para mensajes de retroalimentación */
        .alert-custom {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            border-radius: 5px;
            padding: 15px;
            font-family: Arial, sans-serif;
            font-size: 16px;
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toggleMenu(); // Asegurarse de que el menú esté oculto al cargar la página
        });

        function toggleMenu() {
            var menu = document.getElementById("nav-menu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
    </script>
</head>
<body>

<header>
    <div class="logo">
        <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
    </div>
    <div class="header-right">
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Invitado'); ?></span>
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

<main>
    <h1>Gestión de Departamentos y Cargos</h1>

    <!-- Mostrar mensajes de retroalimentación -->
    <?php if (!empty($message)): ?>
        <div class="alert-custom alert-<?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="table-container">
            <h2>Departamentos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_departamento->num_rows > 0): ?>
                        <?php while($row = $result_departamento->fetch_assoc()): ?>
                            <tr id="depa-<?php echo $row['id_depa']; ?>">
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este departamento?');">
                                        <input type="hidden" name="tipo" value="depa">
                                        <input type="hidden" name="id" value="<?php echo $row['id_depa']; ?>">
                                        <button type="submit" name="delete" class="delete-btn">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No hay departamentos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2>Cargos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_cargo->num_rows > 0): ?>
                        <?php while($row = $result_cargo->fetch_assoc()): ?>
                            <tr id="cargo-<?php echo $row['id_cargo']; ?>">
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este cargo?');">
                                        <input type="hidden" name="tipo" value="cargo">
                                        <input type="hidden" name="id" value="<?php echo $row['id_cargo']; ?>">
                                        <button type="submit" name="delete" class="delete-btn">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No hay cargos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybczfWcRtvvFX6pT0B4u0BxXktI1E8C27EyS3z7g7aJHTH0R" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-69sd6gd/tvAdbKObK6u3RlXY2C7YYZ8O2Q1ctBoqzYJO2smXJnMxZcLq+5/VOO/6" crossorigin="anonymous"></script>
</body>
</html>
