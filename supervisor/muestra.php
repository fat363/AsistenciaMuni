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
    header("Location: ../index.php"); // Redirigir al usuario a la página de inicio
    exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Departamentos y Cargos - Municipalidad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; /* Verde agua claro */
            color: #00695c; /* Verde agua oscuro */
            margin: 0;
            padding: 0;
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
       
        h1 {
            text-align: center;
            color: #004d40;
        }
        .container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .table-container {
            width: 45%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #b2dfdb;
        }
        th {
            background-color: #00796b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #b2dfdb; /* Verde agua claro */
        }
        .delete-btn {
            background-color: #e57373; /* Color rojo suave */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        .delete-btn:hover {
            background-color: #d32f2f; /* Rojo más oscuro al pasar el ratón */
        }
       
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener las filas eliminadas del localStorage
            var eliminadosDepa = JSON.parse(localStorage.getItem('eliminadosDepa')) || [];
            var eliminadosCargo = JSON.parse(localStorage.getItem('eliminadosCargo')) || [];

            // Ocultar filas eliminadas al cargar la página
            eliminadosDepa.forEach(function(id) {
                var fila = document.getElementById('depa-' + id);
                if (fila) fila.style.display = 'none';
            });

            eliminadosCargo.forEach(function(id) {
                var fila = document.getElementById('cargo-' + id);
                if (fila) fila.style.display = 'none';
            });
        });

        function eliminarFila(btn, id, tipo) {
            var fila = btn.parentNode.parentNode;
            fila.style.display = 'none';

            // Guardar la eliminación en el localStorage
            var eliminados = JSON.parse(localStorage.getItem('eliminados' + tipo)) || [];
            eliminados.push(id);
            localStorage.setItem('eliminados' + tipo, JSON.stringify(eliminados));
        }
    </script>
</head>
<body>
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
<main>
    <h1>Departamentos y Cargos</h1>

    <div class="container">
        <div class="table-container">
            <h2>Departamentos</h2>
            <table>
                <tr>
                    <th>Descripción</th>
                    <th>Acción</th>
                </tr>
                <?php
                if ($result_departamento->num_rows > 0) {
                    while($row = $result_departamento->fetch_assoc()) {
                        echo "<tr id='depa-" . $row["id_depa"] . "'><td>" . $row["descripcion"]. "</td>";
                        echo "<td><button class='delete-btn' onclick='eliminarFila(this, \"" . $row["id_depa"] . "\", \"Depa\")'>Eliminar</button></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No hay resultados</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="table-container">
            <h2>Cargos</h2>
            <table>
                <tr>
                    <th>Descripción</th>
                    <th>Acción</th>
                </tr>
                <?php
                if ($result_cargo->num_rows > 0) {
                    while($row = $result_cargo->fetch_assoc()) {
                        echo "<tr id='cargo-" . $row["id_cargo"] . "'><td>" . $row["descripcion"]. "</td>";
                        echo "<td><button class='delete-btn' onclick='eliminarFila(this, \"" . $row["id_cargo"] . "\", \"Cargo\")'>Eliminar</button></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No hay resultados</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</main>

    <?php
    // Cerrar conexión
    $conn->close();
    ?>
<script>
    function toggleNav() {
        const dropdown = document.querySelector('.dropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
</script>
</body>
</html>
