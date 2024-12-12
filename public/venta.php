<?php
// Incluir el archivo de configuración de la base de datos
include('../config/database.php'); // Asegúrate de que esta ruta sea correcta

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $producto_id = $_POST['producto_id'];
    $cantidad_vendida = $_POST['cantidad_vendida'];

    try {
        // Obtener los datos del producto para verificar el stock
        $sql_check = "SELECT id, stock, nombre_producto FROM productos WHERE id = :producto_id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':producto_id' => $producto_id]);
        $producto = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Verificar si hay suficiente stock
            if ($producto['stock'] >= $cantidad_vendida) {
                // Actualizar el stock del producto
                $nuevo_stock = $producto['stock'] - $cantidad_vendida;
                $sql_update = "UPDATE productos SET stock = :stock WHERE id = :id";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->execute([
                    ':stock' => $nuevo_stock,
                    ':id' => $producto['id']
                ]);

                // Registrar la venta en la tabla de ventas
                $sql_insert = "INSERT INTO ventas (producto_id, cantidad, fecha_venta) 
                               VALUES (:producto_id, :cantidad_vendida, NOW())";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->execute([
                    ':producto_id' => $producto_id,
                    ':cantidad_vendida' => $cantidad_vendida
                ]);

                echo "Venta realizada exitosamente. Stock actualizado.";
            } else {
                echo "No hay suficiente stock disponible para realizar la venta.";
            }
        } else {
            echo "Producto no encontrado.";
        }
    } catch (PDOException $e) {
        echo "Error al procesar la venta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Venta</title>
</head>
<body>
    <header>
        <h1>Realizar Venta</h1>
    </header>

    <main>
        <form method="POST" action="">
            <label for="producto_id">Seleccionar Producto:</label>
            <select name="producto_id" id="producto_id" required>
                <option value="">Seleccione un producto</option>
                <?php
                    // Obtener todos los productos para mostrarlos en el select
                    $sql = "SELECT id, nombre_producto FROM productos";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Listar los productos en el select
                    foreach ($productos as $producto) {
                        echo "<option value=\"{$producto['id']}\">{$producto['nombre_producto']}</option>";
                    }
                ?>
            </select>
            <br><br>

            <label for="cantidad_vendida">Cantidad a Vender:</label>
            <input type="number" id="cantidad_vendida" name="cantidad_vendida" required><br><br>

            <button type="submit">Realizar Venta</button>
        </form>

        <!-- Botón de "Volver Atrás" -->
        <br><br>
        <button type="button" onclick="history.back()">Volver Atrás</button>
    </main>
</body>
</html>
