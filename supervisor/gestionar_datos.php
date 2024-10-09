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
    session_destroy(); 
    header("Location: ../index.php"); 
    exit();
}

// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar eliminación en la base de datos
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_formu = intval($_GET['id']);
    
    // Obtener el nombre de usuario que está eliminando
    $usuario_baja = isset($_SESSION['username']) ? $_SESSION['username'] : 'Desconocido';
    
    // Obtener la fecha actual
    $fecha_baja = date('Y-m-d H:i:s');
    
    // Actualizar el registro en la base de datos
    $update_sql = "UPDATE formulario_empleado SET usr_baja = ?, fecha_baja = ? WHERE id_formu = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssi', $usuario_baja, $fecha_baja, $id_formu);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro.']);
    }
    $stmt->close();
    exit;
}
$sql = "SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, 
               f.n_contrato, f.inicio_contrato, f.fin_contrato, f.legajo,
               h.hora_ingreso, h.hora_salida, d.descripcion AS departamento, 
               c.descripcion AS cargo, t.descripcion AS tipo_contrato
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        JOIN contrato t ON f.id_tipo_contrato = t.id_tipo_contrato
        WHERE f.usr_baja IS NULL"; // Mostrar solo empleados que no han sido eliminados

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
    <title>Gestión de Empleados</title>
    <style>
        h1 {
            color: #004d40; /* Verde más oscuro para el título */
            text-align: center;
            margin-top: 80px;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            color: #004d40;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            padding-top: 20px;
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
       

        table {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            font-size: 0.85rem;
        }
        th{
            background-color: #00796b;
            color: #ffffff;
            font-size: 15px;
            
        }
        td {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 13px;
        }
        th, td {
            padding: 7px;
        }
        tr:hover {
            background-color: #b2dfdb; /* Verde claro */
        }
        tr:nth-child(even) {
            background-color: #ffffff;
        }

        .btn {
            background-color: #00796b;
            color: #ffffff;
            padding: 10px 11px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #004d40;
            
        }

        .btn-delete {
            background-color: #d32f2f;
            color: #ffffff;
        }

        .btn-delete:hover {
            background-color: #c62828;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

       


    </style>
    <script>
        function confirmDeletion() {
            return confirm("¿Estás seguro de que quieres borrar esto?");
        }

        function deleteRow(button, id) {
            if (confirmDeletion()) {
                fetch(`gestionar_datos.php?action=delete&id=${id}`, {
                    method: 'GET',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = button.closest('tr');
                        row.style.display = 'none';
                    } else {
                        alert("Error al eliminar el registro: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error en la solicitud: " + error);
                });
            }
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

<h1>Listado de Empleados</h1>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Edad</th>
            <th>Contrato</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Legajo</th>
            <th>Hora<br>Ingreso</br></th>
            <th>Hora<br>salida</br></th>
            <th>Depa.</th>
            <th>Cargo</th>
            <th>Tipo Contrato</th> 
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                <td><?php echo htmlspecialchars($row['DNI']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['edad']); ?></td>
                <td><?php echo htmlspecialchars($row['n_contrato']); ?></td>
                <td><?php echo htmlspecialchars($row['inicio_contrato']); ?></td>
                <td><?php echo htmlspecialchars($row['fin_contrato']); ?></td>
                <td><?php echo htmlspecialchars($row['legajo']); ?></td>
                <td><?php echo htmlspecialchars($row['hora_ingreso']); ?></td>
                <td><?php echo htmlspecialchars($row['hora_salida']); ?></td>
                <td><?php echo htmlspecialchars($row['departamento']); ?></td>
                <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                <td><?php echo htmlspecialchars($row['tipo_contrato']); ?></td> 
                <td class="action-buttons">
                    <a href="editar.formu.php?id=<?php echo $row['id_formu']; ?>" class="btn">Editar</a>

                    <button onclick="deleteRow(this, <?php echo $row['id_formu']; ?>);" class="btn btn-delete">Eliminar</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script>
    function toggleNav() {
        const dropdown = document.querySelector('.dropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
</script>
</body>
</html>
