<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Iniciar la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $id = intval($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $dni = $conn->real_escape_string($_POST['dni']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $edad = intval($_POST['edad']);
    $n_contrato = $conn->real_escape_string($_POST['n_contrato']);
    $inicio_contrato = $conn->real_escape_string($_POST['inicio_contrato']);
    $fin_contrato = $conn->real_escape_string($_POST['fin_contrato']);
    $hora_ingreso = $conn->real_escape_string($_POST['hora_ingreso']);
    $hora_salida = $conn->real_escape_string($_POST['hora_salida']);
    $departamento = $conn->real_escape_string($_POST['departamento']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $tipo_contrato = intval($_POST['contrato']); // Obtener el tipo de contrato seleccionado

    // Obtener el nombre del usuario que realizó la modificación desde la sesión
    $usuario_modificador = isset($_SESSION['username']) ? $conn->real_escape_string($_SESSION['username']) : 'Desconocido';
    $fecha_modificacion = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Actualizar el campo `id_tipo_contrato` en la consulta SQL para actualizar empleados
        $sqlEmpleado = "
            UPDATE formulario_empleado SET
                nombre = '$nombre', 
                apellido = '$apellido', 
                DNI = '$dni',
                telefono = '$telefono', 
                direccion = '$direccion', 
                edad = $edad,
                n_contrato = '$n_contrato', 
                inicio_contrato = '$inicio_contrato',
                fin_contrato = '$fin_contrato',
                id_depa = (SELECT id_depa FROM departamento WHERE descripcion = '$departamento' LIMIT 1),
                id_cargo = (SELECT id_cargo FROM cargo WHERE descripcion = '$cargo' LIMIT 1),
                id_tipo_contrato = $tipo_contrato,
                usr_mod = '$usuario_modificador', 
                fecha_mod = '$fecha_modificacion'
            WHERE id_formu = $id
        ";

        if (!$conn->query($sqlEmpleado)) {
            throw new Exception("Error al actualizar los datos de empleado: " . $conn->error);
        }

        // Obtener el ID de la hora desde formulario_empleado
        $sqlHoraId = "SELECT id_hora FROM formulario_empleado WHERE id_formu = $id";
        $resultHoraId = $conn->query($sqlHoraId);

        if (!$resultHoraId) {
            throw new Exception("Error al obtener el ID de la hora: " . $conn->error);
        }

        $id_hora = $resultHoraId->fetch_assoc()['id_hora'];

        // Insertar o actualizar el registro de hora
        if ($id_hora === null) {
            $sqlHora = "INSERT INTO hora (hora_ingreso, hora_salida) VALUES ('$hora_ingreso', '$hora_salida')";
            if (!$conn->query($sqlHora)) {
                throw new Exception("Error al insertar los datos de hora: " . $conn->error);
            }
        } else {
            $sqlHora = "UPDATE hora SET hora_ingreso = '$hora_ingreso', hora_salida = '$hora_salida' WHERE id_hora = $id_hora";
            if (!$conn->query($sqlHora)) {
                throw new Exception("Error al actualizar los datos de hora: " . $conn->error);
            }
        }

        // Confirmar la transacción
        $conn->commit();

        // Redirigir a la página de confirmación o a la lista de empleados
        header("Location: gestionar_datos.php?msg=success");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "<p class='message text-danger'>Error al actualizar los datos: " . $e->getMessage() . "</p>";
    }

    $conn->close();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = new mysqli("localhost", "root", "", "Municipalidad");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener los datos del empleado incluyendo id_tipo_contrato
    $sql = "
        SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato,
               h.hora_ingreso, h.hora_salida, d.descripcion AS departamento, c.descripcion AS cargo, f.id_tipo_contrato
        FROM formulario_empleado f
        LEFT JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE f.id_formu = $id
    ";

    $result = $conn->query($sql);
    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    $data = $result->fetch_assoc();

    // Consultar todos los departamentos
    $sqlDepartamentos = "SELECT descripcion FROM departamento";
    $resultDepartamentos = $conn->query($sqlDepartamentos);
    if (!$resultDepartamentos) {
        die("Error en la consulta de departamentos: " . $conn->error);
    }
    $departamentos = $resultDepartamentos->fetch_all(MYSQLI_ASSOC);

    // Consultar todos los cargos
    $sqlCargos = "SELECT descripcion FROM cargo";
    $resultCargos = $conn->query($sqlCargos);
    if (!$resultCargos) {
        die("Error en la consulta de cargos: " . $conn->error);
    }
    $cargos = $resultCargos->fetch_all(MYSQLI_ASSOC);

    // Consultar todos los contratos
    $sqlContratos = "SELECT id_tipo_contrato, descripcion FROM contrato";
    $resultContratos = $conn->query($sqlContratos);
    if (!$resultContratos) {
        die("Error en la consulta de contratos: " . $conn->error);
    }
    $contratos = $resultContratos->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    if ($data):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Empleado</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --blanco: #ffffff;
            --celeste-claro: #e0f7fa;
            --celeste: #5dade2;
            --celeste-oscuro: #008ba3;
            --texto-primario: #004d40;
            --texto-secundario: #008ba3;
            --error: #f44336;
            --azul-oscuro: #0d47a1;
            --azul-claro: #42a5f5;
            --celeste-claro: #e3f2fd;
            --celeste-primario: #AED6F1;
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

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--celeste-claro); /* Fondo celeste claro */
            color: var(--texto-primario); /* Color del texto en celeste oscuro */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: var(--blanco);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar {
            padding: 15px 30px;
        }

        .navbar-brand img {
            height: 60px;
        }

        .navbar-nav .nav-link {
            color: var(--texto-primario) !important;
            margin-left: 20px;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: var(--celeste-oscuro) !important;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
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

        .back-button {
            display: inline-flex;
            align-items: center;
            background-color: var(--celeste); /* Color celeste */
            color: var(--blanco);
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .back-button:hover {
            background-color: var(--celeste-oscuro); /* Color celeste oscuro al pasar el mouse */
            transform: translateY(-2px);
        }

        .back-button:before {
            content: '\2190'; /* Código Unicode para la flecha hacia la izquierda */
            margin-right: 8px; /* Espacio entre la flecha y el texto */
            font-size: 1.2em;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center; /* Centrar horizontalmente */
            align-items: center;    /* Centrar verticalmente */
            width: 100%;
            padding: 100px 20px 20px 20px; /* Espaciado para evitar solapamiento con el header */
        }

        .form-container {
            background-color: var(--blanco); /* Fondo blanco para el formulario */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 700px;
        }

        h1 {
            color: var(--celeste); /* Color del título en celeste medio */
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: var(--texto-secundario); /* Color del texto de las etiquetas */
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 20px 0;
            border: 1px solid var(--celeste-oscuro); /* Borde celeste medio */
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        select:focus {
            border-color: var(--celeste); /* Color del borde en foco */
            box-shadow: 0 0 5px var(--celeste);
            outline: none;
        }

        .btn-submit {
            background-color: var(--celeste); /* Botón de color celeste */
            color: var(--blanco);
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--celeste-oscuro); /* Color celeste oscuro al pasar el mouse */
            transform: scale(1.02);
        }

        .message {
            color: var(--error); /* Color de mensaje de error */
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }

            .header-right {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-container {
                padding: 20px;
                margin: 0 10px;
            }

            main {
                padding: 80px 10px 10px 10px; /* Ajuste para pantallas pequeñas */
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="Logo de la Municipalidad">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Invitado'); ?></span>
                </div>
                <a href="gestionar_datos.php" class="back-button">Volver</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h1>Actualizar Empleado</h1>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
                <div class="alert alert-success" role="alert">
                    Los datos del empleado se han actualizado correctamente.
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['id_formu']) ?>">
                <div class="row">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" 
                               value="<?= htmlspecialchars($data['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" name="apellido" id="apellido" class="form-control" 
                               value="<?= htmlspecialchars($data['apellido']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="dni" class="form-label">DNI:</label>
                        <input type="number" name="dni" id="dni" class="form-control" 
                               value="<?= htmlspecialchars($data['DNI']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" 
                               value="<?= htmlspecialchars($data['telefono']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="edad" class="form-label">Edad:</label>
                        <input type="number" name="edad" id="edad" class="form-control" 
                               value="<?= htmlspecialchars($data['edad']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" 
                           value="<?= htmlspecialchars($data['direccion']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="n_contrato" class="form-label">Número de Contrato:</label>
                        <input type="text" name="n_contrato" id="n_contrato" class="form-control" 
                               value="<?= htmlspecialchars($data['n_contrato']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inicio_contrato" class="form-label">Inicio del Contrato:</label>
                        <input type="date" name="inicio_contrato" id="inicio_contrato" class="form-control" 
                               value="<?= htmlspecialchars($data['inicio_contrato']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fin_contrato" class="form-label">Fin del Contrato:</label>
                        <input type="date" name="fin_contrato" id="fin_contrato" class="form-control" 
                               value="<?= htmlspecialchars($data['fin_contrato']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="hora_ingreso" class="form-label">Hora de Ingreso:</label>
                        <input type="time" name="hora_ingreso" id="hora_ingreso" class="form-control" 
                               value="<?= htmlspecialchars($data['hora_ingreso']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="hora_salida" class="form-label">Hora de Salida:</label>
                        <input type="time" name="hora_salida" id="hora_salida" class="form-control" 
                               value="<?= htmlspecialchars($data['hora_salida']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="departamento" class="form-label">Departamento:</label>
                        <select name="departamento" id="departamento" class="form-select" required>
                            <option value="">Seleccione un departamento</option>
                            <?php foreach ($departamentos as $depa): ?>
                                <option value="<?= htmlspecialchars($depa['descripcion']) ?>" 
                                    <?= ($data['departamento'] === $depa['descripcion']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($depa['descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="cargo" class="form-label">Cargo:</label>
                        <select name="cargo" id="cargo" class="form-select" required>
                            <option value="">Seleccione un cargo</option>
                            <?php foreach ($cargos as $car): ?>
                                <option value="<?= htmlspecialchars($car['descripcion']) ?>" 
                                    <?= ($data['cargo'] === $car['descripcion']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($car['descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Campo Tipo de Contrato -->
                <div class="row">
                    <div class="col-md-6">
                        <label for="contrato" class="form-label">Tipo de Contrato:</label>
                        <select name="contrato" id="contrato" class="form-select" required>
                            <option value="">Seleccione un tipo de contrato</option>
                            <?php foreach ($contratos as $contrato): ?>
                                <option value="<?= htmlspecialchars($contrato['id_tipo_contrato']); ?>" 
                                    <?= ($data['id_tipo_contrato'] == $contrato['id_tipo_contrato']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($contrato['descripcion']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Actualizar</button>
            </form>
        </div>
    </main>

    <!-- Bootstrap JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+3oFVEEw0Qe5gs1M3V4yMgp9Fh1K+" 
            crossorigin="anonymous"></script>
</body>
</html>
<?php
    endif;
} else {
    echo "<p class='message text-danger text-center'>No se encontró el ID del empleado.</p>";
}
?>

