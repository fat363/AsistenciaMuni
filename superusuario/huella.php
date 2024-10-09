<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está iniciando sesión
if (isset($_POST['login'])) {
    $_SESSION['loggedin'] = true; 
    $_SESSION['username'] = 'nombre_usuario'; // Cambia esto por el nombre real
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_POST['logout'])) {
    session_destroy(); 
    exit();
}

// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar los datos de los empleados que no han sido eliminados
$sql = "SELECT f.id_formu, f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato,
               h.hora_ingreso, h.hora_salida, d.descripcion AS departamento, c.descripcion AS cargo
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE f.usr_baja IS NULL"; // Mostrar solo empleados que no han sido eliminados
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Gestión de Empleados</title>
    <style>
          :root {
            --celeste-claro: #e0f7fa;
            --celeste: #81d4fa;
            --azul-claro: #b3e5fc;
            --azul: #0288d1;
            --azul-oscuro: #01579b;
            --azul-medio: #0277bd;
            --blanco: #ffffff;
            --gris-claro: #b0bec5;
            --hover-celeste: #81d4fa;
            --hover-azul: #01579b;
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
            background-color: #e1f5fe; /* Celeste claro */
            color: #003f8c; /* Azul oscuro */
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
            background-color: white;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            height: 60px;
        }

      

        h1 {
            color: #003f8c; /* Azul oscuro */
            text-align: center;
            margin-top: 100px; 
        }

        table {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #0288d1; /* Azul */
            font-size: 13px;
        }

        th {
            background-color: #0288d1; /* Azul */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #b3e5fc; /* Celeste medio */
        }

        .btn {
            background-color: #0288d1; /* Azul */
            color: #ffffff;
            padding: 10px 11px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
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
            background-color: var(--azul-claro); /* Color de fondo al pasar el ratón */
        }
         
        

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
            .user-info {
                display: none;
            }
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
            <th>Hora de Ingreso</th>
            <th>Hora de Salida</th>
            <th>Departamento</th>
            <th>Cargo</th>
            <th>Acciones</th>
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
                <td>
                    <a href="registro_huella.php?id=<?php echo $row['id_formu']; ?>" class="btn">Ver registro de huella</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script>
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
