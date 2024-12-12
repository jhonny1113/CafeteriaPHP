<?php
// controllers/productoController.php

// Incluir la conexión a la base de datos
include_once('../config/database.php');

// Obtener todos los productos
try {
    $sql = "SELECT * FROM productos ORDER BY nombre_producto";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener productos: " . $e->getMessage();
    $productos = [];
}

// Obtener el producto con más stock
try {
    $sql = "SELECT TOP 1 id, nombre_producto, stock 
            FROM productos 
            WHERE stock = (SELECT MAX(stock) FROM productos)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $producto_max_stock = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener producto con más stock: " . $e->getMessage();
    $producto_max_stock = null;
}

// Obtener el producto más vendido
try {
    $sql = "SELECT TOP 1 p.nombre_producto, SUM(v.cantidad_vendida) as total_vendido 
            FROM productos p 
            INNER JOIN ventas v ON p.id = v.producto_id 
            GROUP BY p.id, p.nombre_producto 
            ORDER BY total_vendido DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $producto_mas_vendido = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener producto más vendido: " . $e->getMessage();
    $producto_mas_vendido = null;
}

// Procesar la venta si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad_vendida = $_POST['cantidad_vendida'];

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Verificar stock disponible
        $sql_check = "SELECT stock FROM productos WITH (UPDLOCK) WHERE id = :id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':id' => $producto_id]);
        $producto = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($producto && $producto['stock'] >= $cantidad_vendida) {
            // Actualizar stock
            $sql_update = "UPDATE productos 
                          SET stock = stock - :cantidad 
                          WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                ':cantidad' => $cantidad_vendida,
                ':id' => $producto_id
            ]);

            // Registrar la venta
            $sql_venta = "INSERT INTO ventas (producto_id, cantidad_vendida, fecha_venta) 
                         VALUES (:producto_id, :cantidad, GETDATE())";
            $stmt_venta = $conn->prepare($sql_venta);
            $stmt_venta->execute([
                ':producto_id' => $producto_id,
                ':cantidad' => $cantidad_vendida
            ]);

            $conn->commit();
            echo "<script>alert('Venta realizada con éxito');</script>";
            echo "<script>window.location.href='index.php';</script>";
        } else {
            $conn->rollBack();
            echo "<script>alert('Stock insuficiente');</script>";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error al procesar la venta: " . $e->getMessage();
    }
}
?>
