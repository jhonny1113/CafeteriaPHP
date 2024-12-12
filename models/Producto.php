<?php
class Producto {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerProductos() {
        try {
            $sql = "SELECT TOP 100 id, nombre_producto, referencia, precio, peso, categoria, stock, imagen, fecha_creacion 
                    FROM productos 
                    ORDER BY nombre_producto";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }

    public function comprarProducto($producto_id, $cantidad_vendida) {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT id, nombre_producto, precio, stock 
                   FROM productos WITH (UPDLOCK) 
                   WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto && $producto['stock'] >= $cantidad_vendida) {
                $nuevo_stock = $producto['stock'] - $cantidad_vendida;
                
                $sql_update = "UPDATE productos 
                              SET stock = :stock 
                              WHERE id = :id";
                $stmt_update = $this->conn->prepare($sql_update);
                $stmt_update->execute([
                    ':stock' => $nuevo_stock,
                    ':id' => $producto_id
                ]);

                $sql_insert_venta = "INSERT INTO ventas 
                                    (producto_id, cantidad_vendida, fecha_venta) 
                                    VALUES (:producto_id, :cantidad, GETDATE())";
                $stmt_insert_venta = $this->conn->prepare($sql_insert_venta);
                $stmt_insert_venta->execute([
                    ':producto_id' => $producto_id,
                    ':cantidad' => $cantidad_vendida
                ]);

                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al procesar la compra: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerProductoConMasStock() {
        try {
            $sql = "SELECT TOP 1 nombre_producto, stock 
                    FROM productos 
                    ORDER BY stock DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el producto con más stock: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerProductoMasVendido() {
        try {
            $sql = "SELECT TOP 1 p.nombre_producto, SUM(v.cantidad_vendida) as total_vendido
                    FROM productos p
                    INNER JOIN ventas v ON p.id = v.producto_id
                    GROUP BY p.nombre_producto
                    ORDER BY total_vendido DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el producto más vendido: " . $e->getMessage());
            return null;
        }
    }

    public function subirImagen($imagen) {
        try {
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($imagen['type'], $tipos_permitidos)) {
                throw new Exception('Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF.');
            }

            if ($imagen['size'] > 5 * 1024 * 1024) {
                throw new Exception('El archivo es demasiado grande. Máximo 5MB permitido.');
            }

            $nombre_imagen = time() . '_' . basename($imagen['name']);
            $directorio_destino = '../imagenes/';
            
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $ruta_completa = $directorio_destino . $nombre_imagen;
            
            if (move_uploaded_file($imagen['tmp_name'], $ruta_completa)) {
                return 'imagenes/' . $nombre_imagen;
            } else {
                throw new Exception('Error al mover el archivo subido.');
            }
        } catch (Exception $e) {
            error_log("Error al subir imagen: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarRutaImagen($producto_id, $ruta_imagen) {
        try {
            $sql = "UPDATE productos 
                    SET imagen = :ruta_imagen 
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':ruta_imagen' => $ruta_imagen,
                ':id' => $producto_id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar la ruta de la imagen: " . $e->getMessage());
            return false;
        }
    }
}
?>
