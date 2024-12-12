<?php
// Incluir el archivo de configuración de la base de datos
include('../config/database.php');

// Verificar si se envió el formulario con la selección de producto
if (isset($_POST['producto_id'])) {
    $producto_id = $_POST['producto_id'];

    // Obtener los datos del producto a editar
    $sql = "SELECT * FROM productos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verificar si el formulario fue enviado para actualizar el producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre_producto'])) {
    // Obtener los datos del formulario
    $producto_id = $_POST['producto_id'];
    $nombre_producto = $_POST['nombre_producto'];
    $referencia = $_POST['referencia'];
    $precio = $_POST['precio'];
    $peso = $_POST['peso'];
    $categoria = $_POST['categoria'];
    $stock = $_POST['stock'];

    try {
        $params = [
            ':nombre_producto' => $nombre_producto,
            ':referencia' => $referencia,
            ':precio' => $precio,
            ':peso' => $peso,
            ':categoria' => $categoria,
            ':stock' => $stock,
            ':id' => $producto_id
        ];

        $sql_update = "UPDATE productos SET 
                      nombre_producto = :nombre_producto, 
                      referencia = :referencia, 
                      precio = :precio, 
                      peso = :peso, 
                      categoria = :categoria, 
                      stock = :stock";

        // Procesar nueva imagen si se subió una
        if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen = $_FILES['nueva_imagen'];
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (in_array($imagen['type'], $tipos_permitidos)) {
                $nombre_imagen = time() . '_' . basename($imagen['name']);
                $directorio_destino = '../imagenes/';
                $ruta_destino = $directorio_destino . $nombre_imagen;
                
                if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                    // Eliminar imagen anterior
                    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = :id");
                    $stmt->execute([':id' => $producto_id]);
                    $imagen_anterior = $stmt->fetchColumn();
                    
                    if ($imagen_anterior && file_exists('../' . $imagen_anterior)) {
                        unlink('../' . $imagen_anterior);
                    }

                    // Agregar la nueva imagen al UPDATE
                    $sql_update .= ", imagen = :imagen";
                    $params[':imagen'] = 'imagenes/' . $nombre_imagen;
                }
            }
        }

        $sql_update .= " WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute($params);

        // Recargar los datos del producto después de la actualización
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        $mensaje = "Producto actualizado exitosamente.";
        $tipo_mensaje = "success";
    } catch (PDOException $e) {
        $mensaje = "Error al actualizar el producto: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener todos los productos para el select
$sql = "SELECT id, nombre_producto FROM productos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../assets/style-crear.css">
</head>
<body>
    <header>
        <h1>Editar Producto</h1>
    </header>

    <main>
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="select-form">
            <label for="producto_id">Selecciona un Producto para Editar:</label>
            <select name="producto_id" id="producto_id" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $prod): ?>
                    <option value="<?= $prod['id'] ?>" <?= (isset($producto) && $producto['id'] == $prod['id']) ? 'selected' : '' ?>>
                        <?= $prod['nombre_producto'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Seleccionar Producto</button>
        </form>

        <?php if (isset($producto)): ?>
            <form method="POST" action="" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">

                <div class="form-group">
                    <label for="nombre_producto">Nombre del Producto:</label>
                    <input type="text" id="nombre_producto" name="nombre_producto" 
                           value="<?= $producto['nombre_producto'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="referencia">Referencia:</label>
                    <input type="text" id="referencia" name="referencia" 
                           value="<?= $producto['referencia'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" 
                           value="<?= $producto['precio'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="peso">Peso:</label>
                    <input type="number" id="peso" name="peso" 
                           value="<?= $producto['peso'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <input type="text" id="categoria" name="categoria" 
                           value="<?= $producto['categoria'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" 
                           value="<?= $producto['stock'] ?>" required>
                </div>

                <div class="form-group image-group">
                    <label>Imagen Actual:</label>
                    <img src="../<?= $producto['imagen'] ?>" alt="Imagen actual" 
                         id="preview-imagen" class="preview-image">
                    
                    <label for="nueva_imagen">Nueva Imagen:</label>
                    <input type="file" id="nueva_imagen" name="nueva_imagen" accept="image/*">
                </div>

                <div class="button-group">
                    <button type="submit">Actualizar Producto</button>
                    <a href="index.php" class="btn-back">Volver</a>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <script>
        // Vista previa de la imagen
        document.getElementById('nueva_imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-imagen').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
