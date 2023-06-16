<?php
session_start(); // Iniciar la sesión PHP

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["uname"];
    $password = $_POST["psw"];

    function hacerSolicitudSoap($username, $password)
    {
        $wsdl = "http://localhost:55811/Service1.svc?wsdl";

        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );
        $context = stream_context_create($opts);

        $client = new SoapClient(
            $wsdl,
            array(
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE
            )
        );

        try {

            $params = array(
                'nombre' => $username,
                'contrasena' => $password
            );

            $response = $client->AutenticarUsuario($params);
            if ($response->AutenticarUsuarioResult == "Error: usuario o contraseña inválidos") {
                return null;
            } else {
                return $response;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return null;
    }

    // Llama a la función hacerSolicitudSoap para realizar la solicitud SOAP
    $token = hacerSolicitudSoap($username, $password);

    // Almacena el token JWT en la sesión PHP si la autenticación fue exitosa
    if ($token) {
        $_SESSION["jwt"] = $token;

        // Redirige al usuario a la página de usuario si la autenticación fue exitosa
        header("Location: usuario.php");
        exit(); // Salir del script después de la redirección
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login Page</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="container">
            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" required>

            <button type="submit">Login</button>
        </div>
    </form>
</body>

</html>