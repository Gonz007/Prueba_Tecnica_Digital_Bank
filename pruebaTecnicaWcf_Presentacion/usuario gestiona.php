<?php
session_start();
$client = new SoapClient('http://localhost:55811/Service1.svc?wsdl');
$action = $_GET['action'];
$errorMessage = null;

if ($action === 'editar' || $action === 'eliminar') {
    $id = $_GET['id'];
    $response = $client->LeerUsuarios();
    $usuarios = $response->LeerUsuariosResult->Usuario;
    $usuario = null;

    foreach ($usuarios as $usr) {
        if ($usr->Id == $id) {
            $usuario = $usr;
            break;
        }
    }

    if ($usuario == null) {
        $errorMessage = "Usuario no encontrado.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'agregar':
            if (empty($_POST['nombre']) || empty($_POST['fechaDeNacimiento']) || empty($_POST['sexo'])) {
                $errorMessage = "Por favor, no deje los campos vacíos.";
            } else {
                $nombre = $_POST['nombre'];
                $fechaDeNacimiento = new DateTime($_POST['fechaDeNacimiento']);
                $fechaDeNacimiento = $fechaDeNacimiento->format('Y-m-d\TH:i:s');
                $sexo = $_POST['sexo'];
                $client->InsertarUsuario(array('nombre' => $nombre, 'fechaDeNacimiento' => $fechaDeNacimiento, 'sexo' => $sexo));
                echo "<script>alert('Se ha agregado el nuevo registro.'); window.location.href = 'usuario.php';</script>";
            }
            break;
        case 'editar':
            if (empty($_POST['nombre']) || empty($_POST['fechaDeNacimiento']) || empty($_POST['sexo'])) {
                $errorMessage = "Por favor, no deje los campos vacíos.";
            } else {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $fechaDeNacimiento = new DateTime($_POST['fechaDeNacimiento']);
                $fechaDeNacimiento = $fechaDeNacimiento->format('Y-m-d\TH:i:s');
                $sexo = $_POST['sexo'];
                $client->ActualizarUsuario(array('id' => $id, 'nombre' => $nombre, 'fechaDeNacimiento' => $fechaDeNacimiento, 'sexo' => $sexo));
                echo "<script>alert('Se ha actualizado el registro.'); window.location.href = 'usuario.php';</script>";
            }
            break;
        case 'eliminar':
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $fechaDeNacimiento = new DateTime($_POST['fechaDeNacimiento']);
            $fechaDeNacimiento = $fechaDeNacimiento->format('Y-m-d\TH:i:s');
            $sexo = $_POST['sexo'];
            $client->EliminarUsuario(array('id' => $id, 'nombre' => $nombre, 'fechaDeNacimiento' => $fechaDeNacimiento, 'sexo' => $sexo));
            echo "<script>alert('Se ha eliminado el registro.'); window.location.href = 'usuario.php';</script>";
            exit(); // Después de eliminar, se debe salir del script para evitar que se ejecute más código innecesariamente.
    }
}

if ($errorMessage) {
    echo "<script>alert('$errorMessage');</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestión de Usuario</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <?php if ($action === 'agregar' || $action === 'editar'): ?>
        <h2>
            <?php echo ucfirst($action); ?> Usuario
        </h2>
        <form method="POST">
            <?php if ($action === 'editar'): ?>
                <input type="hidden" name="id" value="<?php echo $usuario->Id; ?>" />
            <?php endif; ?>
            <label>
                Nombre:
                <input type="text" name="nombre" value="<?php echo $action === 'editar' ? $usuario->Nombre : ''; ?>" />
            </label>
            <br />
            <label>
                Fecha de Nacimiento:
                <input type="date" name="fechaDeNacimiento"
                    value="<?php echo $action === 'editar' ? (new DateTime($usuario->FechaDeNacimiento))->format('Y-m-d') : ''; ?>" />
            </label>
            <br />
            <label>
                Sexo:
                <select name="sexo">
                    <option value="">Seleccionar...</option>
                    <option value="M" <?php echo ($action === 'editar' && $usuario->Sexo === 'M') ? 'selected' : ''; ?>>
                        Masculino</option>
                    <option value="F" <?php echo ($action === 'editar' && $usuario->Sexo === 'F') ? 'selected' : ''; ?>>Femenino
                    </option>
                </select>
            </label>
            <br />
            <button type="submit">Confirmar</button>
        </form>
    <?php elseif ($action === 'eliminar'): ?>
        <h2>Eliminar Usuario</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $usuario->Id; ?>" />
            <label>
                Nombre:
                <input type="text" name="nombre" value="<?php echo $usuario->Nombre; ?>" readonly />
            </label>
            <br />
            <label>
                Fecha de Nacimiento:
                <input type="date" name="fechaDeNacimiento"
                    value="<?php echo (new DateTime($usuario->FechaDeNacimiento))->format('Y-m-d'); ?>" readonly />
            </label>
            <br />
            <label>
                Sexo:
                <input type="text" name="sexo" value="<?php echo $usuario->Sexo === 'M' ? 'Masculino' : 'Femenino'; ?>"
                    readonly />
            </label>
            <br />
            <button type="submit">Confirmar</button>
        </form>
    <?php endif; ?>
    <a href="usuario.php"><button type="submit">volver</button></a>
</body>

</html>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-J268VRCENC"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-J268VRCENC');
</script>