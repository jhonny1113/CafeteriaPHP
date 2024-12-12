<?php
include('../config/database.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Primero obtener la información de la imagen
        $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $imagen = $stmt->fetchColumn();

        // Eliminar el producto
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Si hay una imagen asociada, eliminarla
        if ($imagen && file_exists('../' . $imagen)) {
            unlink('../' . $imagen);
        }

        header("Location: index.php?mensaje=Producto eliminado exitosamente&tipo=success");
    } catch (PDOException $e) {
        header("Location: index.php?mensaje=Error al eliminar el producto&tipo=error");
    }
} else {
    header("Location: index.php?mensaje=ID de producto no especificado&tipo=error");
}
exit();
