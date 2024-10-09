<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Empleado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php
    session_start();
    
    // Verificar si el usuario está iniciando sesión
    if (isset($_POST['login'])) {
        $_SESSION['loggedin'] = true; 
        $_SESSION['username'] = 'nombre_usuario'; 
    }
    
   
    // Verificar si el usuario está iniciando sesión
    if (isset($_POST['login'])) {
        // Aquí deberías validar las credenciales del usuario
        $_SESSION['loggedin'] = true; // Establecer la sesión como iniciada
    }
?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            color: #01579b;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        h1 {
            color: #0288d1;
            text-align: center;
            margin-bottom: 60px;
            padding-top: 50px;
        }
        form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #0288d1;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #0288d1;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .pdf-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .delete-button {
            display: none;
            margin-left: 10px;
            color: red;
            cursor: pointer;
        }
        .pdf-link {
            display: none;
            margin-left: 10px;
            color: #0288d1;
        }
        header {
            background-color: #ffffff; 
            color: #01579b; 
            padding: 10px 20px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 100vw; 
            box-sizing: border-box; 
            border-bottom: 1px solid #b0bec5; 
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
        }

        nav a {
            color: #01579b; 
            text-decoration: none;
            font-weight: bold;
            margin-left: 20px; 
            padding: 10px; 
        }

        nav a:hover {
            background-color: #e0f2f1; 
            border-radius: 5px;
        }
        .user-info i {
            color: #007bff; 
            margin-right: 5px; 
            font-size: 2.5em; 
        }
    </style>
    <script>
        let pdfFileURL = '';

        function previewPDF(input) {
            const file = input.files[0];
            const deleteButton = document.getElementById('delete-button');
            const pdfLink = document.getElementById('pdf-link');

            if (file && file.type === 'application/pdf') {
                pdfFileURL = URL.createObjectURL(file);
                pdfLink.href = pdfFileURL;
                pdfLink.style.display = "inline";
                pdfLink.innerText = "Ver PDF";
                deleteButton.style.display = "inline";
            } else {
                pdfLink.style.display = "none";
                deleteButton.style.display = "none";
            }
        }

        function deletePDF() {
            const input = document.getElementById('n_contrato');
            input.value = ''; // Limpiar el input
            pdfFileURL = ''; // Reiniciar la URL
            document.getElementById('delete-button').style.display = "none";
            document.getElementById('pdf-link').style.display = "none";
        }
    </script>
</head>

<body>
    <h1>Formulario de Empleado</h1>

    <?php
  
    
    // Inicializar variables
    $uploadedPdf = '';

    // Conectar a la base de datos
    $conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consultar departamentos
    $sqlDepartamentos = "SELECT id_depa, descripcion FROM departamento";
    $resultDepartamentos = $conn->query($sqlDepartamentos);
    $departamentos = $resultDepartamentos->fetch_all(MYSQLI_ASSOC);

    // Consultar cargos
    $sqlCargos = "SELECT id_cargo, descripcion FROM cargo";
    $resultCargos = $conn->query($sqlCargos);
    $cargos = $resultCargos->fetch_all(MYSQLI_ASSOC);

    // Consultar tipos de contrato
    $sqlContratos = "SELECT id_tipo_contrato, descripcion FROM contrato";
    $resultContratos = $conn->query($sqlContratos);
    $contratos = $resultContratos->fetch_all(MYSQLI_ASSOC);

    // Procesar datos del formulario si se envió
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Manejar el archivo PDF
        $target_dir = "uploads/"; // Asegúrate de que este directorio existe y es escribible
        $target_file = $target_dir . basename($_FILES["n_contrato"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es un PDF
        if ($fileType != "pdf") {
            echo "<p style='color: red;'>Error: Solo se permiten archivos PDF.</p>";
            $uploadOk = 0;
        }

        // Verificar si se puede subir el archivo
        //if ($uploadOk = 1) {
            //if (move_uploaded_file($_FILES["n_contrato"]["tmp_name"], $target_file)) {
                //echo "<p style='color: green;'>El archivo ". htmlspecialchars(basename($_FILES["n_contrato"]["name"])). " ha sido subido.</p>";
                //$uploadedPdf = $target_file; // Guardar la ruta del PDF subido

                // Procesar otros datos del formulario
                $nombre = $_POST['nombre'];
                $apellido = $_POST['apellido'];
                $dni = $_POST['dni'];
                $telefono = $_POST['telefono'];
                $direccion = $_POST['direccion'];
                $edad = $_POST['edad'];
                $inicio_contrato = $_POST['inicio_contrato'];
                $fin_contrato = $_POST['fin_contrato'];
                $hora_ingreso = $_POST['hora_ingreso'];
                $hora_salida = $_POST['hora_salida'];
                $legajo = $_POST['legajo'];
                $archivo = file_get_contents($_FILES['n_contrato']['tmp_name']);
                $nombre_archivo = $_FILES['n_contrato']['name'];
                $departamento = $_POST['departamento'];
                $cargo = $_POST['cargo'];
                $tipo_contrato = $_POST['tipo_contrato'];
                $archivo_base64 = base64_encode($archivo);

                // Insertar datos de la hora
                $sql_hora = "INSERT INTO hora (hora_ingreso, hora_salida) VALUES ('$hora_ingreso', '$hora_salida')";

                

                if ($conn->query($sql_hora) === TRUE) {
                    $id_hora = $conn->insert_id; // Obtener el id_hora generado

                    // Insertar datos en la base de datos
                    $sql_empleado = "INSERT INTO formulario_empleado (nombre, apellido, DNI, telefono, direccion, edad, n_contrato, inicio_contrato, fin_contrato, legajo, id_hora, id_depa, id_cargo,  id_tipo_contrato, archivo)
                                     VALUES ('$nombre', '$apellido', '$dni', '$telefono', '$direccion', $edad, '$nombre_archivo', '$inicio_contrato', '$fin_contrato', '$legajo', '$id_hora', '$departamento', '$cargo', '$tipo_contrato', '$archivo_base64')";

                    

                   
                    if ($conn->query($sql_empleado) === TRUE) {
                        echo "<p style='color: green;'>Registro exitoso.</p>";
                    } else {
                        echo "Error: " . $sql_empleado . "<br>" . $conn->error;
                    }
                //} else {
                //    echo "Error: " . $sql_hora . "<br>" . $conn->error;
               // }
            //} else {
            //    echo "<p style='color: red;'>Lo siento, hubo un error al subir su archivo.</p>";
            //}
        }
    }
    ?>

    <header>
    <div class="logo">
        <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
    </div>
        <nav>
        <a href="../index.php" class="logout-btn">Cerrar Sesión</a>
            <div class="user-info">
                
                <?php if (isset($_SESSION['username'])): ?>
                     <span class="user-info">
                     <i class="fas fa-user"></i> 
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                 <?php endif; ?>
            </div>
        </nav>
    </header>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>

        <label for="dni">DNI:</label>
        <input type="number" id="dni" name="dni" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" required>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" required>

        <label for="inicio_contrato">Inicio de Contrato:</label>
        <input type="date" id="inicio_contrato" name="inicio_contrato" required>

        <label for="fin_contrato">Fin de Contrato:</label>
        <input type="date" id="fin_contrato" name="fin_contrato" required>

        <label for="hora_ingreso">Hora de Ingreso:</label>
        <input type="time" id="hora_ingreso" name="hora_ingreso" required>

        <label for="hora_salida">Hora de Salida:</label>
        <input type="time" id="hora_salida" name="hora_salida" required>

        <label for="legajo">Legajo:</label>
        <input type="text" id="legajo" name="legajo" required>

        <label for="n_contrato">Número de Contrato (PDF):</label>
        <input type="file" id="n_contrato" name="n_contrato" accept=".pdf" onchange="previewPDF(this)" required>
        <div class="pdf-container">
            <a id="pdf-link" class="pdf-link" target="_blank"></a>
            <button type="button" id="delete-button" class="delete-button" onclick="deletePDF()">Eliminar</button>
        </div>

        <label for="departamento">Departamento:</label>
        <select id="departamento" name="departamento" required>
            <?php foreach ($departamentos as $departamento): ?>
                <option value="<?php echo $departamento['id_depa']; ?>">
                    <?php echo $departamento['descripcion']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="cargo">Cargo:</label>
        <select id="cargo" name="cargo" required>
            <?php foreach ($cargos as $cargo): ?>
                <option value="<?php echo $cargo['id_cargo']; ?>">
                    <?php echo $cargo['descripcion']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tipo_contrato">Tipo de Contrato:</label>
        <select id="tipo_contrato" name="tipo_contrato" required>
            <?php foreach ($contratos as $contrato): ?>
                <option value="<?php echo $contrato['id_tipo_contrato']; ?>">
                    <?php echo $contrato['descripcion']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Enviar">
    </form>
</body>
</html>