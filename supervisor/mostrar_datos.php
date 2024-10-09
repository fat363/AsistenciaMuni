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

// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha realizado una búsqueda o se ha solicitado "Mostrar Todo"
$whereClause = "1"; // Condición por defecto para mostrar todos los registros
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['show_all'])) {
    $search = !empty($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
    $departamento = !empty($_POST['departamento']) ? $conn->real_escape_string($_POST['departamento']) : '';
    $cargo = !empty($_POST['cargo']) ? $conn->real_escape_string($_POST['cargo']) : '';
    
    $conditions = [];
    
    // Agregar condiciones de búsqueda
    if (!empty($search)) {
        $conditions[] = "(f.nombre LIKE '%$search%' OR f.apellido LIKE '%$search%')";
    }
    
    if (!empty($departamento)) {
        $conditions[] = "d.descripcion LIKE '%$departamento%'";
    }
    
    if (!empty($cargo)) {
        $conditions[] = "c.descripcion LIKE '%$cargo%'";
    }
    
    // Construir la cláusula WHERE
    if (count($conditions) > 0) {
        $whereClause = implode(' AND ', $conditions);
    }
}

$sql = "SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato,f.legajo,
               CONCAT(h.hora_ingreso, ' - ', h.hora_salida) AS horario, d.descripcion AS departamento, c.descripcion AS cargo,
               t.descripcion AS tipo_contrato, f.archivo
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        JOIN contrato t ON f.id_tipo_contrato = t.id_tipo_contrato
        WHERE $whereClause";


$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Datos de Empleados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; /* Fondo verde claro */
            color: #004d40; /* Color del texto en verde oscuro */
            margin: 0;
            padding: 0;
            
        }
        header {
            background-color: #ffffff; /* Fondo blanco */
            color: #004d40; /* Color del texto en verde oscuro */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 100vw;
            box-sizing: border-box;
            border-bottom: 1px solid #80cbc4; /* Línea inferior en verde suave */
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
        .container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: auto;
        }
        h1 {
            color: #004d40; /* Verde más oscuro para el título */
            text-align: center;
            margin-top: 80px;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
      
        form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        input[type="text"] {
            padding: 8px;
            border: 1px solid #388e3c; /* Verde medio */
            border-radius: 5px;
            width: 100%;
            max-width: 400px;
        }
        input[type="submit"], button {
            background-color: #00796b; /* Verde oscuro para el botón */
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #004d40; /* Verde más oscuro al pasar el ratón */
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
        td {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 13px;
        }
        th{
            background-color: #00796b;
            color: #ffffff;
            font-size: 12px;
            
        }
        th, td {
            padding: 6px;
            
            
        }
        tr:hover {
            background-color: #b2dfdb; /* Verde claro */
        }
        tr:nth-child(even) {
            background-color: #ffffff; /* Verde más claro alternativo */
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

        button {
            background-color: #e53935; /* Rojo */
            color: #ffffff;
            padding: 5px 10px; /* Botón más pequeño */
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 10px; /* Fuente más pequeña */
        }
        button:hover {
            background-color: #d32f2f; /* Rojo más oscuro al pasar el ratón */
        }
    
        .btn-mostrar-todo {
            background-color: #00796b; /* Color verde oscuro */
            color: #ffffff; /* Texto blanco */
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-mostrar-todo:hover {
            background-color: #004d40; /* Cambia a un tono más oscuro al pasar el ratón */
        }
        .export-btn {
            background-color: #00796b; /* Botón verde oscuro */
            color: #ffffff;
            padding: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            margin-top: 5px;
        }
        .export-btn:hover {
            background-color: #004d40; /* Botón verde más oscuro al pasar el ratón */
        }
        .export-menu {
            display: none;
            position: absolute;
            background-color: #ffffff;
            border: 1px solid #00796b; /* Borde verde oscuro */
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            margin-top: 10px;
        }
        .export-menu a {
            display: block;
            padding: 10px;
            color: #00796b; /* Color del texto en verde oscuro */
            text-decoration: none;
            font-size: 14px;
        }
        .export-menu a:hover {
            background-color: #f1f8f6; /* Fondo claro al pasar el ratón */
        }

    </style>
</head>
<script>

function openFile(base64Data) {
    // Tipo de archivo, por ejemplo, 'application/pdf' si es un PDF
    const mimeType = 'application/pdf';  // Cambia el tipo MIME según el archivo que estés usando
    const fileName = 'contrato.pdf';     // Nombre sugerido del archivo
    const base64 = `data:${mimeType};base64,${base64Data}`;
    
    // Crear un enlace temporal para abrir el archivo
    const link = document.createElement('a');
    link.href = base64;
    link.download = fileName;  // Solo si deseas que se descargue en lugar de abrirse
    link.target = '_blank';    // Abre en una nueva pestaña

    // Simular un clic en el enlace
    link.click();
}

    function toggleNav() {
        const dropdown = document.querySelector('.dropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    function toggleMenu(id) {
            var menu = document.getElementById('export-menu-' + id);
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        window.onclick = function(event) {
            if (!event.target.matches('.export-btn')) {
                var dropdowns = document.getElementsByClassName('export-menu');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }
</script>
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
<div class="container">
    <h1>Datos de Empleados</h1>

    <!-- Formulario de búsqueda con filtro -->
    <form method="POST">
        <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
        <input type="text" name="departamento" placeholder="Departamento" value="<?php echo isset($departamento) ? htmlspecialchars($departamento) : ''; ?>">
        <input type="text" name="cargo" placeholder="Cargo" value="<?php echo isset($cargo) ? htmlspecialchars($cargo) : ''; ?>">
        <button type="submit" name="show_all" value="1" class="btn-mostrar-todo">Mostrar Todo</button>
        <input type="submit" value="Buscar">
    </form>

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
            <th>Inicio Contrato</th>
            <th>Fin Contrato</th>
            <th>Legajo</th>
            <th>Horario</th>
            <th>Departamento</th>
            <th>Cargo</th>
            <th>Tipo de Contrato</th> 
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
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
                    <td><?php echo htmlspecialchars($row['horario']); ?></td>
                    <td><?php echo htmlspecialchars($row['departamento']); ?></td>
                    <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                    <td><?php echo htmlspecialchars($row['tipo_contrato']); ?></td>         
                    <td>    
    <button class='export-btn' onclick='toggleMenu(<?php echo $row["id_formu"]; ?>)'>Exportar</button>
    <div id='export-menu-<?php echo $row["id_formu"]; ?>' class='export-menu'>
        <a href='exportar_a_pdf.php?id=<?php echo $row["id_formu"]; ?>'>PDF</br></a>
        <a href='exportar_a_excel.php?id=<?php echo $row["id_formu"]; ?>'>Excel</a>
        <a href='exportar_a_csv.php?id=<?php echo $row["id_formu"]; ?>'>CSV</a>
    </div>
</td>
<td><button class='export-btn' onclick='openFile("<?php echo $row["archivo"]; ?>")'>Ver contrato</button></td>

                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="14">No se encontraron resultados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

</body>
</html>
