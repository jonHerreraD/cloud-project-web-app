<?php
// Iniciar sesión
session_start();

// Configuración de conexión (Azure SQL Database)
$serverName = "tcp:paas-server1.database.windows.net,1433";
$connectionOptions = array(
    "Database" => "paas-db",
    "Uid" => "azureuser",
    "PWD" => "azure123$",
    "Encrypt" => true,
    "TrustServerCertificate" => false
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
        echo "✅ Mensaje enviado correctamente.<br><br>";
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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
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

    <!-- Mostrar registros guardados -->
    <h2>Mensajes Registrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Mensaje</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Consulta de mensajes
            $query = "SELECT id, nombre, correo, mensaje, fecha FROM mensajes ORDER BY fecha DESC";
            $result = sqlsrv_query($conn, $query);

            if ($result === false) {
                echo "<tr><td colspan='5'>❌ Error al obtener mensajes.</td></tr>";
            } else {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["mensaje"]) . "</td>";
                    echo "<td>" . $row["fecha"]->format('Y-m-d H:i') . "</td>";
                    echo "</tr>";
                }
            }

            // Cerrar conexión
            sqlsrv_close($conn);
            ?>
        </tbody>
    </table>
</body>
</html>
