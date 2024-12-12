<?php
// Conexión a la base de datos
include('../config/database.php');

// Obtener todos los productos
$sql = "SELECT * FROM productos";
$stmt = $conn->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
</head>
<body>
    <h1>Productos</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Referencia</th>
                <th>Precio</th>
                <th>Peso</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo $producto['id']; ?></td>
                    <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                    <td><?php echo htmlspecialchars($producto['referencia']); ?></td>
                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                    <td><?php echo htmlspecialchars($producto['peso']); ?></td>
                    <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $producto['id']; ?>">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
