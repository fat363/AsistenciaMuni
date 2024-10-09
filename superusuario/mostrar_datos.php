
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

// Verificar si se ha realizado una búsqueda
$whereClause = "1"; // Condición por defecto para mostrar todos los registros
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

$sql = "SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato,f.legajo, f.usr_baja, f.fecha_baja, f.usr_mod, f.fecha_mod,
               CONCAT(h.hora_ingreso, ' - ', h.hora_salida) AS horario, d.descripcion AS departamento, c.descripcion AS cargo, f.archivo
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE $whereClause";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlaces a CSS y Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Mostrar Datos de Empleados</title>
    <style>
        :root {
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

        /* Nuevo Contenedor para el Menú y la Información del Usuario */
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
            border: 1px solid var(--azul-oscuro);
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
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid var(--azul-secundario);
            border-radius: 5px;
            width: 100%;
            max-width: 400px;
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
            width: 100%;
            max-width: 1200px;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid var(--azul-oscuro);
            font-size: 11.5px;
        }

        th {
            background-color: var(--azul-secundario);
            color: var(--blanco);
        }

        tr:nth-child(even) {
            background-color: var(--celeste-claro);
        }

        tr:hover {
            background-color: var(--celeste-primario);
            color: var(--negro);
        }

        .export-btn {
            background-color: var(--azul-claro);
            color: var(--blanco);
            padding: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
        }

        .export-btn:hover {
            background-color: var(--azul-primario);
        }

        .export-menu {
            display: none;
            position: absolute;
            background-color: var(--azul-secundario);
            border: 1px solid var(--azul-primario);
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            margin-top: 10px;
        }

        .export-menu a {
            display: block;
            padding: 10px;
            color: var(--blanco);
            text-decoration: none;
            font-size: 14px;
        }

        .export-menu a:hover {
            background-color: var(--azul-secundario);
        }

        /* Responsivo: Ajustes para pantallas pequeñas */
        @media (max-width: 768px) {
            .user-info {
                display: none; /* Ocultar información del usuario en pantallas pequeñas */
            }
        }
        .btn {
            background-color: var(--azul-secundario);
            color: var(--blanco);
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--azul-secundario);
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
<div class="container">
    <h1>Datos de Empleados</h1>

    <!-- Formulario de búsqueda -->
    <form id="search-form" method="post">
        <input type="text" id="search" name="search" placeholder="Buscar por nombre o apellido">
        <input type="text" id="departamento" name="departamento" placeholder="Buscar por departamento">
        <input type="text" id="cargo" name="cargo" placeholder="Buscar por cargo">
        <input type="submit" value="Buscar">
        <button type="button" onclick="window.location.href='mostrar_datos.php'">Mostrar Todos</button>
    </form>

    <table border="1">
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
            <th>Horario</th>
            <th>Dep.</th>
            <th>Cargo</th>
            <th></th>
            <th></th>
        </tr>
        <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_formu'];
        echo "<tr>
                <td>" . htmlspecialchars($row['nombre']) . "</td>
                <td>" . htmlspecialchars($row['apellido']) . "</td>
                <td>" . htmlspecialchars($row['DNI']) . "</td>
                <td>" . htmlspecialchars($row['telefono']) . "</td>
                <td>" . htmlspecialchars($row['direccion']) . "</td>
                <td>" . htmlspecialchars($row['edad']) . "</td>
                <td>" . htmlspecialchars($row['n_contrato']) . "</td>
                <td>" . htmlspecialchars($row['inicio_contrato']) . "</td>
                <td>" . htmlspecialchars($row['fin_contrato']) . "</td>
                <td>" . htmlspecialchars($row['legajo']) . "</td>
                <td>" . htmlspecialchars($row['usr_baja']) . "</td>
                <td>" . htmlspecialchars($row['fecha_baja']) . "</td>
                <td>" . htmlspecialchars($row['usr_mod']) . "</td>
                <td>" . htmlspecialchars($row['fecha_mod']) . "</td>
                <td>" . htmlspecialchars($row['horario']) . "</td>
                <td>" . htmlspecialchars($row['departamento']) . "</td>
                <td>" . htmlspecialchars($row['cargo']) . "</td>
                <td>
                    <button class='export-btn' onclick='toggleExportMenu($id)'>Exportar</button>
                    <div id='export-menu-$id' class='export-menu'>
                        <a href='exportar_a_pdf.php?id=$id'>PDF</a>
                        <a href='exportar_a_excel.php?id=$id'>Excel</a>
                        <a href='exportar_a_csv.php?id=$id'>CSV</a>
                    </div>
                </td>
                <td>
                    <button class='export-btn' onclick='openFile(\"" . htmlspecialchars($row["archivo"]) . "\")'>Ver contrato</button>
                </td>
              </tr>";
    }
}
else {
            echo "<tr><td colspan='13' class='text-center'>No se encontraron resultados</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</div>

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

function toggleMenu() {
    const menu = document.getElementById('nav-menu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

function toggleExportMenu(id) {
    const menu = document.getElementById(`export-menu-${id}`);
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}
</script>
</body>
</html>
