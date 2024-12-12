<?php
$serverName = "RENTPO25090TX\\SQLEXPRESS";  // Cambia a tu servidor
$connectionOptions = array(
    "Database" => "Bdatos",  // Nombre de la base de datos
    "Uid" => "sa",           // Usuario de SQL Server
    "PWD" => "Cnrc0l.2024"              // Contraseña del usuario
);

try {
    // Crear una nueva conexión PDO
    $conn = new PDO("sqlsrv:server=$serverName;Database=Bdatos", $connectionOptions['Uid'], $connectionOptions['PWD']);
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "";
} catch (PDOException $e) {
    echo " " . $e->getMessage();
}
?>
