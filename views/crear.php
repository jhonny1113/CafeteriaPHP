<?php
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_producto = $_POST['nombre_producto'];
    $referencia = $_POST['referencia'];
    $precio = $_POST['precio'];
    $peso = $_POST['peso'];
    $categoria = $_POST['categoria'];
    $stock = $_POST['stock'];
    $imagen = $_FILES['imagen'];

    try {
        // Procesar la imagen
        $ruta_bd = 'imagenes/default_image.jpg'; // Valor por defecto

        if ($imagen['error'] === UPLOAD_ERR_OK) {
            // Validar tipo de archivo
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($imagen['type'], $tipos_permitidos)) {
                throw new Exception('Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF.');
            }

            // Validar tamaño
            if ($imagen['size'] > 5 * 1024 * 1024) {
                throw new Exception('El archivo es demasiado grande. Máximo 5MB permitido.');
            }

            $nombre_imagen = time() . '_' . basename($imagen['name']);
            $directorio_destino = '../imagenes/';
            
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $ruta_destino = $directorio_destino . $nombre_imagen;
            
            if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                $ruta_bd = 'imagenes/' . $nombre_imagen;
            }
        }

        // Verificar producto existente
        $sql_check = "SELECT id, stock FROM productos WHERE referencia = :referencia";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':referencia' => $referencia]);
        $producto_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($producto_existente) {
            // Actualizar stock
            $nuevo_stock = $producto_existente['stock'] + $stock;
            $sql_update = "UPDATE productos 
                          SET stock = :stock 
                          WHERE id = :id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                ':stock' => $nuevo_stock, 
                ':id' => $producto_existente['id']
            ]);
            echo "<script>alert('Stock actualizado exitosamente.');</script>";
        } else {
            // Insertar nuevo producto
            $sql_insert = "INSERT INTO productos 
                (nombre_producto, referencia, precio, peso, categoria, stock, imagen, fecha_creacion) 
                VALUES (:nombre_producto, :referencia, :precio, :peso, :categoria, :stock, :imagen, GETDATE())";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->execute([
                ':nombre_producto' => $nombre_producto,
                ':referencia' => $referencia,
                ':precio' => $precio,
                ':peso' => $peso,
                ':categoria' => $categoria,
                ':stock' => $stock,
                ':imagen' => $ruta_bd
            ]);
            echo "<script>alert('Producto creado exitosamente.');</script>";
        }
        
        // Redireccionar al index
        echo "<script>window.location.href='index.php';</script>";
        
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="../assets/style-crear.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Crear Producto</h1>
    </header>

    <main>
        <form method="POST" action="" enctype="multipart/form-data" class="producto-form">
            <div class="form-group">
                <label for="nombre_producto">Nombre del Producto:</label>
                <input type="text" id="nombre_producto" name="nombre_producto" required>
            </div>

            <div class="form-group">
                <label for="referencia">Referencia:</label>
                <input type="text" id="referencia" name="referencia" required>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" required>
            </div>

            <div class="form-group">
                <label for="peso">Peso:</label>
                <input type="number" id="peso" name="peso" required>
            </div>

            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen del Producto:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-primary">Registrar Producto</button>
                <a href="index.php" class="btn-secondary">Volver Atrás</a>
            </div>
        </form>
    </main>
</body>
</html>
