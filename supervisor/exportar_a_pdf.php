<?php
require('fpdf/fpdf.php');

// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del formulario del empleado
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta para obtener los datos del empleado
$sql = "SELECT f.nombre, f.apellido, f.DNI, f.telefono, f.direccion, f.edad, f.n_contrato, f.inicio_contrato, f.fin_contrato, 
               CONCAT(h.hora_ingreso, ' - ', h.hora_salida) AS horario, d.descripcion AS departamento, c.descripcion AS cargo 
        FROM formulario_empleado f
        JOIN hora h ON f.id_hora = h.id_hora
        JOIN departamento d ON f.id_depa = d.id_depa
        JOIN cargo c ON f.id_cargo = c.id_cargo
        WHERE f.id_formu = $id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Crear un nuevo documento PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Agregar el logo
    $pdf->Image('https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png', 10, 8, 33); // Ajusta la posición y el tamaño según sea necesario
    $pdf->Ln(20); // Espacio después del logo

    // Establecer fuente para el título
    $pdf->SetFont('Arial', 'B', 16);

    // Título
    $pdf->Cell(0, 10, 'Datos del Empleado', 0, 1, 'C');

    // Espacio
    $pdf->Ln(10);

    // Datos del empleado
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Nombre: ', 1);
    $pdf->Cell(0, 10, $row['nombre'], 1, 1);
    
    $pdf->Cell(50, 10, 'Apellido: ', 1);
    $pdf->Cell(0, 10, $row['apellido'], 1, 1);
    
    $pdf->Cell(50, 10, 'DNI: ', 1);
    $pdf->Cell(0, 10, $row['DNI'], 1, 1);
    
    $pdf->Cell(50, 10, 'Telefono: ', 1);
    $pdf->Cell(0, 10, $row['telefono'], 1, 1);
    
    $pdf->Cell(50, 10, 'Direccion: ', 1);
    $pdf->Cell(0, 10, $row['direccion'], 1, 1);
    
    $pdf->Cell(50, 10, 'Edad: ', 1);
    $pdf->Cell(0, 10, $row['edad'], 1, 1);
    
    $pdf->Cell(50, 10, 'Numero de Contrato: ', 1);
    $pdf->Cell(0, 10, $row['n_contrato'], 1, 1);
    
    $pdf->Cell(50, 10, 'Inicio de Contrato: ', 1);
    $pdf->Cell(0, 10, $row['inicio_contrato'], 1, 1);
    
    $pdf->Cell(50, 10, 'Fin de Contrato: ', 1);
    $pdf->Cell(0, 10, $row['fin_contrato'], 1, 1);
    
    $pdf->Cell(50, 10, 'Horario: ', 1);
    $pdf->Cell(0, 10, $row['horario'], 1, 1);
    
    $pdf->Cell(50, 10, 'Departamento: ', 1);
    $pdf->Cell(0, 10, $row['departamento'], 1, 1);
    
    $pdf->Cell(50, 10, 'Cargo: ', 1);
    $pdf->Cell(0, 10, $row['cargo'], 1, 1);

    // Generar el PDF
    $pdf->Output('D', 'Empleado_'.$row['nombre'].'_'.$row['apellido'].'.pdf');
} else {
    echo "No se encontraron datos para este empleado.";
}

$conn->close();
?>