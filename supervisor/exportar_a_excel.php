<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los datos
$sql = "SELECT f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato, 
               CONCAT(h.hora_ingreso, ' - ', h.hora_salida) AS horario, d.descripcion AS departamento, c.descripcion AS cargo 
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE f.id_formu = ?"; // Este es el ID del empleado que se va a exportar

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetros
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt->bind_param("i", $id);

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Establecer el nombre del archivo
    $filename = 'Empleado_'.$row['nombre'].'_'.$row['apellido'].'.csv';

    // Configurar los encabezados para forzar la descarga del archivo CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Abrir un puntero de archivo a la salida estándar (php://output)
    $output = fopen('php://output', 'w');

    // Escribir la línea de encabezados
    fputcsv($output, array('Campo', 'Valor'));

    // Escribir los datos del empleado
    fputcsv($output, array('Nombre', $row['nombre']));
    fputcsv($output, array('Apellido', $row['apellido']));
    fputcsv($output, array('DNI', $row['DNI']));
    fputcsv($output, array('Teléfono', $row['telefono']));
    fputcsv($output, array('Dirección', $row['direccion']));
    fputcsv($output, array('Edad', $row['edad']));
    fputcsv($output, array('Número de Contrato', $row['n_contrato']));
    fputcsv($output, array('Inicio de Contrato', $row['inicio_contrato']));
    fputcsv($output, array('Fin de Contrato', $row['fin_contrato']));
    fputcsv($output, array('Horario', $row['horario']));
    fputcsv($output, array('Departamento', $row['departamento']));
    fputcsv($output, array('Cargo', $row['cargo']));

    // Cerrar el puntero de archivo
    fclose($output);
    exit;
} else {
    echo "No se encontraron datos para este empleado.";
}

$conn->close();
?>
