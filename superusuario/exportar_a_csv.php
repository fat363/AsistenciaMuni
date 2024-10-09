<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del empleado
$sql = "SELECT f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato, 
               CONCAT(h.hora_ingreso, ' - ', h.hora_salida) AS horario, d.descripcion AS departamento, c.descripcion AS cargo 
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE f.id_formu = $id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die('Empleado no encontrado.');
}

$employee = $result->fetch_assoc();

// Crear archivo CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="Empleado_' . $id . '.csv"');
header('Cache-Control: max-age=0');

// Abrir el archivo para salida
$output = fopen('php://output', 'w');

// Escribir la cabecera del CSV
fputcsv($output, ['Nombre', 'Apellido', 'DNI', 'Teléfono', 'Dirección', 'Edad', 'Número de Contrato', 'Inicio de Contrato', 'Fin de Contrato', 'Horario', 'Departamento', 'Cargo']);

// Escribir los datos del empleado
fputcsv($output, [
    $employee['nombre'],
    $employee['apellido'],
    $employee['DNI'],
    $employee['telefono'],
    $employee['direccion'],
    $employee['edad'],
    $employee['n_contrato'],
    $employee['inicio_contrato'],
    $employee['fin_contrato'],
    $employee['horario'],
    $employee['departamento'],
    $employee['cargo']
]);

// Cerrar el archivo para salida
fclose($output);

$conn->close();