<?php
// Iniciar sesión
session_start();

// Configuración de conexión (Azure SQL Database)
$serverName = "tcp:paas-server1.database.windows.net,1433"; // Reemplaza TU_SERVIDOR
$connectionOptions = array(
    "Database" => "paas-db",                                // Nombre de tu BD en Azure SQL
    "Uid" => "azureuser",                         // Usuario SQL (formato completo)
    "PWD" => "azure123$",                         // Contraseña del usuario
    "Encrypt" => true,                                       // Cifrado (recomendado en Azure)
    "TrustServerCertificate" => false                        // No confiar en certificados autofirmados
);

// Conectar
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("❌ Error al conectar a la base de datos: " . print_r(sqlsrv_errors(), true));
}

// Insertar mensaje si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $mensaje = $_POST["mensaje"];
    $fecha = date('Y-m-d H:i:s');

    $sql = "INSERT INTO mensajes (nombre, correo, mensaje, fecha) VALUES (?, ?, ?, ?)";
    $params = array($nombre, $correo, $mensaje, $fecha);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("❌ Error al insertar mensaje: " . print_r(sqlsrv_errors(), true));
    } else {
        echo "✅ Mensaje enviado correctamente.";
    }
}
?>

<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Formulario de Contacto</h1>
    <form method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Mensaje:</label><br>
        <textarea name="mensaje" rows="5" required></textarea><br><br>

        <button type="submit" name="guardar">Enviar</button>
    </form>
</body>
</html>
