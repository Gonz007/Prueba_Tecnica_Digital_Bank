<?php
session_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$soapOptions = array(
    'soap_version' => SOAP_1_1,
);
//La url del SoapClient se debe modificar segun la capa de negocio sea (Por sis e quiere eejecutar en otra maquina) 
$client = new SoapClient('http://localhost:55811/Service1.svc?wsdl', $soapOptions);
$result = $client->LeerUsuarios();
$usuarios = $result->LeerUsuariosResult->Usuario;
$regsPorPagina = 10;
$pagina = isset($_GET['p']) ? $_GET['p'] : 1;
$primerRegistro = ($pagina - 1) * $regsPorPagina;
$rangoInicio = isset($_POST['inicio']) ? $_POST['inicio'] - 1 : 0;
$rangoFin = isset($_POST['fin']) ? $_POST['fin'] - 1 : 10;

if (isset($_POST['descargar'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nombre');
    $sheet->setCellValue('B1', 'Fecha de Nacimiento');
    $sheet->setCellValue('C1', 'Sexo');

    $row = 2;
    for ($i = $rangoInicio; $i <= $rangoFin; $i++) {
        if (isset($usuarios[$i])) {
            $usuario = $usuarios[$i];
            $sheet->setCellValue('A' . $row, $usuario->Nombre);
            $sheet->setCellValue('B' . $row, $usuario->FechaDeNacimiento);
            $sheet->setCellValue('C' . $row, $usuario->Sexo);
            $row++;
        }
    }
    $writer = new Xlsx($spreadsheet);
    $archivo = 'usuarios.xlsx';
    $writer->save($archivo);
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($archivo));
    flush(); // Flush system output buffer
    readfile($archivo);
    exit;
} elseif (isset($_POST['descargar_txt'])) {
    $contenido = "Nombre\tFecha de Nacimiento\tSexo\n";
    for ($i = $rangoInicio; $i <= $rangoFin; $i++) {
        if (isset($usuarios[$i])) {
            $usuario = $usuarios[$i];
            $contenido .= $usuario->Nombre . "\t" . $usuario->FechaDeNacimiento . "\t" . $usuario->Sexo . "\n";
        }
    }
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="usuarios.txt"');
    header('Content-Length: ' . strlen($contenido));
    echo $contenido;
    exit;
} else {
    echo "<a href='usuario gestiona.php?action=agregar'>Agregar Usuario</a>";

    echo "<table border='3'>";
    echo "<tr><th>Nombre</th><th>Fecha de Nacimiento</th><th>Sexo</th><th>Acciones</th></tr>";
    for ($i = $primerRegistro; $i < $primerRegistro + $regsPorPagina; $i++) {
        if (isset($usuarios[$i])) {
            $usuario = $usuarios[$i];
            echo "<tr>";
            echo "<td>" . $usuario->Nombre . "</td>";
            echo "<td>" . $usuario->FechaDeNacimiento . "</td>";
            echo "<td>" . $usuario->Sexo . "</td>";
            echo "<td>";
            echo "<a href='usuario gestiona.php?action=editar&id=" . $usuario->Id . "'>Editar</a> ";
            echo "<a href='usuario gestiona.php?action=eliminar&id=" . $usuario->Id . "'>Eliminar</a>";
            echo "</td>";
            echo "</tr>";
        }
    }
    echo "";
    echo "</table>";

    $totalPaginas = ceil(count($usuarios) / $regsPorPagina);
    for ($p = 1; $p <= $totalPaginas; $p++) {
        echo "<a href='?p=$p'>$p</a> ";
    }
    // Formulario para seleccionar el rango de registros
    echo '<form class="download-form" action="" method="post">';
    echo '<label>Desde: <input type="number" name="inicio" min="1" max="' . count($usuarios) . '" value="1"></label>';
    echo '<label>Hasta: <input type="number" name="fin" min="1" max="' . count($usuarios) . '" value="10"></label>';
    echo '<div class="buttons">';
    echo '<input type="submit" name="descargar" value="Descargar Excel" class="blue-button">';
    echo '<input type="submit" name="descargar_txt" value="Descargar TXT" class="blue-button">';
    echo '</div>';
    echo '</form>';
}
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-J268VRCENC"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-J268VRCENC');
</script>
<title>Usuario</title>
<link rel="stylesheet" type="text/css" href="styles.css">