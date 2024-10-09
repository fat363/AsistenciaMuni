<?php
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

// registro_huella.php

// Configuración de la conexión a la base de datos
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

// Inicializar variables para mensajes
$mensaje = "";

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los datos recibidos del formulario
    $id_empleado = intval($_POST['empleado']);
    $fecha = $_POST['fecha'];
    $hora_ingreso = $_POST['hora_ingreso'];
    $hora_salida = $_POST['hora_salida'];

    // Validar que los campos no estén vacíos
    if (!empty($id_empleado) && !empty($fecha) && !empty($hora_ingreso) && !empty($hora_salida)) {
        // Validar el formato de la fecha (YYYY-MM-DD)
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) {
            $mensaje = "<p style='color: red;'>Formato de fecha inválido. Utiliza AAAA-MM-DD.</p>";
        } else {
            // Opcional: Validar que la hora de salida sea posterior a la hora de ingreso
            if ($hora_salida <= $hora_ingreso) {
                $mensaje = "<p style='color: red;'>La hora de salida debe ser posterior a la hora de ingreso.</p>";
            } else {
                // Preparar la sentencia SQL para prevenir inyecciones SQL
                $stmt = $conn->prepare("INSERT INTO huella (hora_ingreso, hora_salida, fecha, id_formu) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssi", $hora_ingreso, $hora_salida, $fecha, $id_empleado);

                    // Ejecutar la sentencia
                    if ($stmt->execute()) {
                        $mensaje = "<p style='color: #004d40;'>Horas registradas exitosamente.</p>";
                    } else {
                        $mensaje = "<p style='color: red;'>Error al registrar las horas: " . $stmt->error . "</p>";
                    }

                    $stmt->close();
                } else {
                    $mensaje = "<p style='color: red;'>Error en la preparación de la consulta: " . $conn->error . "</p>";
                }
            }
        }
    } else {
        $mensaje = "<p style='color: red;'>Por favor, completa todos los campos.</p>";
    }
}

// Consulta para obtener los empleados activos (sin fecha de baja)
$sql = "SELECT id_formu, nombre, apellido FROM formulario_empleado WHERE fecha_baja IS NULL";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Horas de Empleados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; /* Fondo verde claro */
            color: #004d40; /* Color del texto en verde oscuro */
            margin: 0;
            padding: 0;
            height: 100vh; /* Asegura que el body ocupe toda la altura */
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #ffffff; /* Fondo blanco */
            color: #004d40; /* Color del texto en verde oscuro */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            border-bottom: 1px solid #80cbc4; /* Línea inferior en verde suave */
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
            position: relative;
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

        .container {
            flex: 1; /* Toma el espacio restante */
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 80px; /* Espacio para el header fijo */
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #004d40; /* Borde verde */
            width: 100%;
            max-width: 400px; /* Aumenta el ancho máximo para mejor visualización */
            box-sizing: border-box;
        }
        h2 {
            color: #00796b; /* Verde oscuro */
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            color: #00796b;
        }
        select, input[type="date"], input[type="time"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #004d40;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #00796b;
            color: white;
            padding: 10px 15px;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #004d40;
        }
        .mensaje {
            text-align: center;
            margin-bottom: 15px;
        }
        /* Responsividad */
        @media (max-width: 500px) {
            .form-container {
                padding: 15px 20px;
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado Fijo -->
    <header>
        <div class="logo">
            <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
        </div>
        <nav>
            <div class="dropdown-trigger">
                <div class="menu-toggle">
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

    <!-- Contenedor Principal para Centrar el Formulario -->
    <div class="container">
        <div class="form-container">
            <h2>Registro de Horas</h2>
            
            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>
            
            <form action="ver_huella.php" method="POST">
                <label for="empleado">Empleado:</label>
                <select name="empleado" id="empleado" required>
                    <option value="" disabled selected>Selecciona un empleado</option>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $empleado = htmlspecialchars($row['nombre'] . ' ' . $row['apellido']);
                            echo "<option value='" . $row['id_formu'] . "'>$empleado</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay empleados disponibles</option>";
                    }
                    ?>
                </select>
        
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" max="<?php echo date('Y-m-d'); ?>" required>
        
                <label for="hora_ingreso">Hora de Ingreso:</label>
                <input type="time" id="hora_ingreso" name="hora_ingreso" required>
        
                <label for="hora_salida">Hora de Salida:</label>
                <input type="time" id="hora_salida" name="hora_salida" required>
        
                <input type="submit" value="Registrar">
            </form>
        </div>
    </div>
   
    <!-- Scripts para el Dropdown (Opcional) -->
    <script>
        // Si deseas agregar funcionalidades adicionales al dropdown
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
