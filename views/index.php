<?php
include('../controllers/productoController.php');
include('../includes/header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sección Hero con tarjetas flotantes -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            
        </div>

        <!-- Tarjetas flotantes de estadísticas -->
        <div class="stats-cards-container">
            <div class="stats-card">
                <i class="fas fa-box"></i>
                <h3>Producto con más Stock</h3>
                <?php if ($producto_max_stock): ?>
                    <p><?php echo htmlspecialchars($producto_max_stock['nombre_producto']); ?></p>
                    <span class="stats-detail"><?php echo $producto_max_stock['stock']; ?> unidades</span>
                <?php else: ?>
                    <p>No hay productos en stock</p>
                <?php endif; ?>
            </div>
            
            <div class="stats-card">
                <i class="fas fa-crown"></i>
                <h3>Producto Más Vendido</h3>
                <?php if ($producto_mas_vendido): ?>
                    <p><?php echo htmlspecialchars($producto_mas_vendido['nombre_producto']); ?></p>
                    <span class="stats-detail"><?php echo $producto_mas_vendido['total_vendido']; ?> vendidos</span>
                <?php else: ?>
                    <p>Sin ventas registradas</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <main class="main-content">
        <!-- Botón para agregar nuevo producto -->
        <div class="add-product-button">
            <a href="crear.php" class="btn-add">Agregar Nuevo Producto</a>
        </div>

        <section class="productos-section">
            <h2 class="menu-title">Nuestro Menú</h2>
            <div class="productos-container">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto-card">
                        <?php
                        $imagen_path = $producto['imagen'];
                        if (empty($imagen_path) || !file_exists('../' . $imagen_path)) {
                            $imagen_path = 'imagenes/default_image.jpg';
                        }
                        ?>
                        <img src="../<?php echo $imagen_path; ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" 
                             class="producto-img">
                        
                        <div class="producto-info">
                            <h4 class="producto-title">
                                <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                            </h4>
                            
                            <p class="producto-price">
                                Precio: $<?php echo number_format($producto['precio'], 2); ?>
                            </p>
                            
                            <p class="producto-stock">
                                Stock: <?php echo $producto['stock']; ?>
                            </p>
                            
                            <p class="producto-referencia">
                                Ref: <?php echo htmlspecialchars($producto['referencia']); ?>
                            </p>
                            
                            <p class="producto-categoria">
                                Categoría: <?php echo htmlspecialchars($producto['categoria']); ?>
                            </p>
                            
                            <p class="producto-peso">
                                Peso: <?php echo $producto['peso']; ?> kg
                            </p>
                        </div>
                        
                        <form method="POST" action="" class="comprar-form">
                            <input type="hidden" name="producto_id" 
                                   value="<?php echo $producto['id']; ?>">
                            
                            <div class="form-group">
                                <label for="cantidad_vendida_<?php echo $producto['id']; ?>" 
                                       class="form-label">Cantidad:</label>
                                
                                <input type="number" 
                                       name="cantidad_vendida" 
                                       id="cantidad_vendida_<?php echo $producto['id']; ?>" 
                                       class="form-input" 
                                       min="1" 
                                       max="<?php echo $producto['stock']; ?>" 
                                       required>
                            </div>
                            
                            <button type="submit" name="comprar" class="btn-comprar">
                                Comprar
                            </button>
                        </form>
                        
                        <div class="producto-actions">
                            <button onclick="window.location.href='editar.php?id=<?php echo $producto['id']; ?>'" 
                                    class="btn-editar">Editar</button>
                            <button onclick="confirmarEliminacion(<?php echo $producto['id']; ?>)" 
                                    class="btn-eliminar">Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                window.location.href = `eliminar.php?id=${id}`;
            }
        }
    </script>
</body>
</html>
