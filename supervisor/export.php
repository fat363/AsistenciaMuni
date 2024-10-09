<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "c6bd_ipet363", "bd_ipet363", "c6municipalidad2024");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['export'])) {
    $id_formu = $conn->real_escape_string($_POST['id_formu']);
    $export_type = $_POST['export'];
    
    // Obtener los datos del empleado
    $sql = "SELECT * FROM formulario_empleado WHERE id_formu = '$id_formu'";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    
    // Generar el archivo según el tipo de exportación solicitado
    switch ($export_type) {
        case 'excel':
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=empleado_$id_formu.xls");
            echo implode("\t", array_keys($data)) . "\n";
            echo implode("\t", array_values($data));
            break;
        case 'pdf':
            require('fpdf.php');
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            foreach ($data as $key => $value) {
                $pdf->Cell(0, 10, "$key: $value", 0, 1);
            }
            $pdf->Output('D', "empleado_$id_formu.pdf");
            break;
        case 'csv':
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=empleado_$id_formu.csv");
            echo implode(",", array_keys($data)) . "\n";
            echo implode(",", array_values($data));
            break;
    }
    exit();
}

$conn->close();
?>
