<?php
// Habilitar la visualización de errores (solo para desarrollo; desactívalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires'); // Ajusta según tu ubicación

session_start();

// Obtener el ID del empleado desde la URL
if (isset($_GET['id'])) {
    $id_formu = intval($_GET['id']);

    $servername = "localhost";
    $username = "c6bd_ipet363";
    $password = "bd_ipet363";
    $dbname = "c6municipalidad2024";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consultar los datos del empleado, incluyendo el legajo
    $sql = "SELECT nombre, apellido, legajo FROM formulario_empleado WHERE id_formu = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $id_formu);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
    } else {
        die("Empleado no encontrado.");
    }

    $stmt->close();

    // Consultar todos los registros de huella para el empleado
    $sql_huella = "SELECT fecha, hora_ingreso, hora_salida FROM huella WHERE id_formu = ? ORDER BY fecha DESC, hora_ingreso ASC";
    $stmt_huella = $conn->prepare($sql_huella);
    if (!$stmt_huella) {
        die("Error en la preparación de la consulta de huella: " . $conn->error);
    }
    $stmt_huella->bind_param('i', $id_formu);
    $stmt_huella->execute();

    if ($stmt_huella->error) {
        die("Error en la ejecución de la consulta de huella: " . $stmt_huella->error);
    }

    $result_huella = $stmt_huella->get_result();

    // Inicializar un array para almacenar los registros de huella agrupados por fecha
    $registros_huella = [];

    if ($result_huella->num_rows > 0) {
        while ($row = $result_huella->fetch_assoc()) {
            $fecha = $row['fecha'];
            if (!isset($registros_huella[$fecha])) {
                $registros_huella[$fecha] = [];
            }
            $registros_huella[$fecha][] = $row;
        }
    }

    $stmt_huella->close();

    // Calcular total horas trabajadas
    $total_seconds = 0;

    foreach ($registros_huella as $fecha => $registros) {
        foreach ($registros as $registro) {
            if (!empty($registro['hora_ingreso']) && !empty($registro['hora_salida'])) {
                $hora_ingreso = strtotime($registro['hora_ingreso']);
                $hora_salida = strtotime($registro['hora_salida']);
                $diff = $hora_salida - $hora_ingreso;
                if ($diff > 0) { // Evitar valores negativos
                    $total_seconds += $diff;
                }
            }
        }
    }

    $total_hours = floor($total_seconds / 3600);
    $total_minutes = floor(($total_seconds % 3600) / 60);

    $conn->close();
} else {
    die("ID de empleado no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Huella</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; /* Celeste claro */
            color: #01579b; /* Azul oscuro para el texto */
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #0288d1; /* Azul celeste */
        }
        .info {
            display: flex;
            gap: 20px;
            margin: 10px 0;
            color: #0277bd; /* Azul medio */
        }
        .btn {
            background-color: #0288d1; /* Azul celeste */
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0277bd; /* Azul medio al pasar el mouse */
        }
        table {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #81d4fa; /* Azul muy claro para las fronteras */
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0288d1; /* Azul celeste */
            color: white;
        }
        .fecha-header {
            background-color: #b3e5fc; /* Azul muy claro para las cabeceras de fecha */
            text-align: center;
            font-weight: bold;
        }
        /* Estilos para la sección de Liquidación */
        #liquidacionResult {
            display: none;
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #81d4fa;
            border-radius: 5px;
            background-color: #b3e5fc;
            width: 100%;
            max-width: 800px;
        }
    </style>
</head>
<body>
<h1>Registro de Huella</h1>
<div class="info">
    <p>Nombre: <?php echo htmlspecialchars($empleado['nombre']); ?></p>
    <p>Apellido: <?php echo htmlspecialchars($empleado['apellido']); ?></p>
    <p>Legajo: <?php echo htmlspecialchars($empleado['legajo']); ?></p>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Desde</th>
            <th>Hasta</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($registros_huella)) {
            foreach ($registros_huella as $fecha => $registros) {
                echo "<tr class='fecha-header'><td colspan='4'>Fecha: " . htmlspecialchars($fecha) . "</td></tr>";
                foreach ($registros as $registro) {
                    echo "<tr>";
                    echo "<td></td>"; // Celda vacía para alinear con la cabecera de fecha
                    echo "<td>Presente</td>"; // Puedes ajustar la descripción según tus necesidades
                    echo "<td>" . htmlspecialchars(date('H:i', strtotime($registro['hora_ingreso']))) . "</td>";
                    echo "<td>" . htmlspecialchars(date('H:i', strtotime($registro['hora_salida']))) . "</td>";
                    echo "</tr>";
                }
            }
        } else {
            // Si no hay registros, mostrar un mensaje
            echo "<tr>";
            echo "<td colspan='4' style='text-align: center;'>No hay registros de huella para este empleado.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Sección de Liquidación -->
<div id="liquidacionResult">
    <h3>Liquidación</h3>
    <p>Total Horas Trabajadas: <?php echo $total_hours; ?> horas y <?php echo $total_minutes; ?> minutos.</p>
</div>

<!-- Botones adicionales -->
<div>
    <button class="btn" id="liquidacionBtn">Liquidación</button>
   
</div>

<a href="huella.php" class="btn">Volver</a>

<!-- Scripts para manejar los botones -->
<script>
    document.getElementById('liquidacionBtn').addEventListener('click', function() {
        var liquidacionDiv = document.getElementById('liquidacionResult');
        if (liquidacionDiv.style.display === 'none' || liquidacionDiv.style.display === '') {
            liquidacionDiv.style.display = 'block';
        } else {
            liquidacionDiv.style.display = 'none';
        }
    });

    document.getElementById('editarTurnoBtn').addEventListener('click', function() {
        // Aquí implementa la lógica para editar el turno (horas extras)
        alert('Funcionalidad para editar turno aún no implementada.');
    });

    document.getElementById('eliminarBtn').addEventListener('click', function() {
        // Aquí implementa la lógica para eliminar el registro
        alert('Funcionalidad para eliminar registro aún no implementada.');
    });
</script>
</body>
</html>
