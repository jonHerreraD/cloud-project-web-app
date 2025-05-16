<?php
// Iniciar sesión para mantener los datos entre requests
session_start();

// Inicializar array de mensajes si no existe
if (!isset($_SESSION['mensajes'])) {
    $_SESSION['mensajes'] = [];
}

// Guardar nuevo mensaje
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $mensaje = $_POST["mensaje"];
    
    // Crear nuevo mensaje con fecha actual
    $nuevoMensaje = [
        'nombre' => $nombre,
        'correo' => $correo,
        'mensaje' => $mensaje,
        'fecha' => date('Y-m-d H:i:s')
    ];
    
    // Agregar al array de mensajes
    array_unshift($_SESSION['mensajes'], $nuevoMensaje);
}

// Obtener nombres únicos para el combo box
$nombres = [];
foreach ($_SESSION['mensajes'] as $mensaje) {
    if (!in_array($mensaje['nombre'], $nombres)) {
        $nombres[] = $mensaje['nombre'];
    }
}
sort($nombres);

// Consultar mensajes filtrados por nombre
$mensajesFiltrados = [];
if (isset($_GET['filtro_nombre']) && $_GET['filtro_nombre'] !== '') {
    $nombreFiltro = $_GET['filtro_nombre'];
    foreach ($_SESSION['mensajes'] as $mensaje) {
        if ($mensaje['nombre'] === $nombreFiltro) {
            $mensajesFiltrados[] = $mensaje;
        }
    }
} else {
    $mensajesFiltrados = $_SESSION['mensajes'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulario con Combo Box</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Formulario de Contacto</h1>

    <form method="post">
        <input type="hidden" name="guardar" value="1">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Correo:</label>
        <input type="email" name="correo" required>

        <label>Mensaje:</label>
        <textarea name="mensaje" required></textarea>

        <button type="submit">Enviar</button>
    </form>

    <h2>Filtrar Mensajes por Nombre</h2>
    <form method="get">
        <label>Selecciona un nombre:</label>
        <select name="filtro_nombre">
            <option value="">-- Todos --</option>
            <?php foreach ($nombres as $n): ?>
                <option value="<?= htmlspecialchars($n) ?>" <?= (isset($_GET['filtro_nombre']) && $_GET['filtro_nombre'] == $n) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($n) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Consultar</button>
    </form>

    <?php if (!empty($mensajesFiltrados)): ?>
        <h3>
            <?= isset($_GET['filtro_nombre']) && $_GET['filtro_nombre'] !== '' 
                ? 'Mensajes de: ' . htmlspecialchars($_GET['filtro_nombre']) 
                : 'Todos los mensajes' ?>
        </h3>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Mensaje</th>
                <th>Fecha</th>
            </tr>
            <?php foreach ($mensajesFiltrados as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= htmlspecialchars($m['correo']) ?></td>
                    <td><?= htmlspecialchars($m['mensaje']) ?></td>
                    <td><?= $m['fecha'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif (isset($_GET['filtro_nombre'])): ?>
        <p>No se encontraron mensajes para este nombre.</p>
    <?php endif; ?>
</div>

</body>
</html>