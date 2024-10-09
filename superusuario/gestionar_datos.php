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

// Consultar los datos de los empleados que no han sido eliminados
$sql = "SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato, f.legajo, f.usr_baja, f.fecha_baja, f.usr_mod, f.fecha_mod,
               h.hora_ingreso, h.hora_salida, d.descripcion AS departamento, c.descripcion AS cargo ,co.descripcion AS contrato
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        JOIN contrato co ON f.id_tipo_contrato = co.id_tipo_contrato
        WHERE f.usr_baja IS NULL"; // Mostrar solo empleados que no han sido eliminados
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Gestión de Empleados</title>
    <style>
        /* Variables de colores basados en tonos azul y celeste */
        :root {
            --azul-primario: #007BFF;        /* Azul Primario */
            --azul-secundario: #5DADE2;      /* Azul Secundario */
            --celeste-primario: #AED6F1;     /* Celeste Primario */
            --celeste-secundario: #D6EAF8;   /* Celeste Secundario */
            --blanco: #ffffff;
            --gris-claro: #b0bec5;
            --negro: #000000;
            --azul-oscuro: #0d47a1;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--celeste-secundario);
            color: var(--azul-oscuro);
            margin: 0;
            padding: 0;
            padding-top: 80px; /* Espacio para el header fijo */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        header {
            background-color: var(--blanco);
            color: var(--azul-primario);
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

        h1 {
            color: var(--azul-primario);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: auto;
        }

        form {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        input[type="text"] {
            padding: 5px 5px;
            border: 2px solid var(--celeste-primario);
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
            font-size: 1rem;
        }

        input[type="submit"], button {
            background-color: var(--azul-secundario);
            color: var(--blanco);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover, button:hover {
            background-color: var(--celeste-primario);
        }

        table {
            width: 80%; /* Reducido al 80% del contenedor */
            max-width: 1000px; /* Máximo ancho de 1000px */
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: var(--celeste-secundario);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 5px; /* Espaciado dentro de las celdas */
            text-align: left;
            border-bottom: 1px solid var(---azul-secundario);
            font-size: 11.5px; /* Tamaño de fuente ligeramente reducido */
           
        }

        th {
            background-color: var(--azul-secundario);
            color: var(--blanco);
        }

        tr:nth-child(even) {
            background-color: var(--celeste-soso);
        }

        tr:hover {
            background-color: var(--celeste-primario);
            color: var(--negro);
        }
        .btn {
            background-color: var(--azul-primario);
            color: var(--blanco);
            padding: 4px 2px; /* Ajusté el padding para mejor visibilidad */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;

            transition: background-color 0.3s;
            /* Removido el margin-top negativo */
        }

        .btn:hover {
            background-color: var(--azul-secundario);
        }

        .btn-delete {
            background-color: #CC0033; /* Color para el botón de eliminar */
            color: var(--blanco);
            padding: 4px 2px; /* Consistencia en el padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-block; /* Para asegurarnos de que se comporte como un bloque */
        }

       

        .btn-delete:hover {
            background-color:   #FF0000  ; /* Hot Pink para hover */
        }

        .action-buttons {
            display: flex;
            flex-direction: column; /* Apila los botones verticalmente */
            gap: 5px; /* Espacio entre los botones */
        }


        /* Responsividad para pantallas pequeñas */
        @media (max-width: 768px) {
            .user-info {
                margin-left: 15px;
                font-size: 0.9rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            table {
                width: 100%; /* Ajustar a 100% en pantallas pequeñas */
            }

            th, td {
                padding: 10px 12px;
                font-size: 0.85rem;
            }

            input[type="text"] {
                max-width: 250px;
            }

            input[type="submit"], button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .btn, .btn-delete {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
        }
    </style>
    <script>
        function toggleMenu() {
            const menu = document.getElementById('nav-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function toggleExportMenu(id) {
            const menu = document.getElementById(`export-menu-${id}`);
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function confirmDeletion() {
            return confirm("¿Estás seguro de que quieres eliminar este registro?");
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

    <div class="container">
        <h1>Listado de Empleados</h1>

        <form id="search-form" method="post">
            <input type="text" id="search" name="search" placeholder="Buscar por nombre o apellido">
            <input type="text" id="departamento" name="departamento" placeholder="Buscar por departamento">
            <input type="text" id="cargo" name="cargo" placeholder="Buscar por cargo">
            <input type="submit" value="Buscar">
            <button type="button" onclick="window.location.href='mostrar_datos.php'">Mostrar Todos</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Tel.</th>
                    <th>Direc.</th>
                    <th>Edad</th>
                    <th>Contrato</th>
                    <th>Inicio<br>Contrato</br></th>
                    <th>Fin<br>Contrato</br></th>
                    <th>Legajo</th>
                    <th>Baja</th>
                    <th>Fecha<br>baja</br></th>
                    <th>Mod</th>
                    <th>Fecha<br>mod</br></th>
                    <th>Contrato</th>
                    <th>Hora<br>Ingreso</br></th>
                    <th>Hora<br>Salida</br></th>
                    <th>Dep.</th>
                    <th>Cargo</th>
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
                        <td><?php echo htmlspecialchars($row['usuario_baja']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_baja']); ?></td>
                        <td><?php echo htmlspecialchars($row['usr_mod']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_mod']); ?></td>
                        <td><?php echo htmlspecialchars($row['contrato']); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_ingreso']); ?></td>
                        <td><?php echo htmlspecialchars($row['hora_salida']); ?></td>
                        <td><?php echo htmlspecialchars($row['departamento']); ?></td>
                        <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                        <!-- Asegúrate de que esto esté dentro del bucle que muestra cada empleado -->
                        <td>
                            <div class="action-buttons">
                            <a href="editar.formu.php?id=<?php echo $row['id_formu']; ?>" class="btn">Editar</a>
                                <a href="#" class="btn-delete" onclick="deleteRow(this, <?php echo $row['id_formu']; ?>)">Eliminar</a>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
                <?php
                if ($result->num_rows == 0) {
                    echo "<tr><td colspan='14' style='text-align:center;'>No se encontraron resultados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
