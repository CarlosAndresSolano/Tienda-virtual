<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$carrito_count = array_sum(array_column($_SESSION['cart'], 'cantidad'));

try {
    
    $stmt = $pdo->query("SELECT id, titulo, descripcion, precio, imagen FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Hardware Store Nuts</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .submenu {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
        }

        #img-carrito {
            width: 30px;
            height: auto;
            cursor: pointer;
        }

        #carrito-count {
            background-color: #ff5733;
            color: #fff;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.8em;
            position: absolute;
            top: -5px;
            right: -5px;
            font-weight: bold;
        }

        #carrito {
    width: 500px; 
    max-height: 400px; 
    overflow-y: auto; 
    background-color: rgba(255, 255, 255, 0.97);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    transition: all 0.3s ease;
}


     
        #carrito h2 {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 15px;
        }

       
        #carrito table {
            width: 100%;
            border-collapse: collapse;
        }

        #carrito th, #carrito td {
            padding: 10px;
            text-align: left;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }

        #carrito th {
            font-weight: bold;
            color: #555;
            background-color: #f7f7f7;
        }

        #carrito img {
            width: 40px;
            height: auto;
            border-radius: 5px;
        }

       
        #vaciar-carrito {
            display: block;
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: #ff6b6b;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #vaciar-carrito:hover {
            background-color: #e65c5c;
        }
        .btn-pagar {
    display: block;
    margin-top: 15px;
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    text-align: center;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-pagar:hover {
    background-color: #0056b3; 
}

    </style>
</head>
<body>
    <div class="nav-back">
        <a href="index.php">Volver al inicio</a>
    </div>
    <div>
    <ul>
        <li class="submenu">
            <img src="imagenes/car.svg" id="img-carrito" alt="carrito" />
            <span id="carrito-count"><?= $carrito_count ?></span> 
            <div id="carrito">
                <h2>Carrito</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <tr>
                                    <td><img src="uploads/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['titulo']) ?>" style="width: 50px; height: auto;"></td>
                                    <td><?= htmlspecialchars($item['titulo']) ?></td>
                                    <td>$<?= number_format($item['precio'], 2) ?></td>
                                    <td><?= $item['cantidad'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">El carrito está vacío.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <form method="POST" action="vaciar_carrito.php">
                    <button type="submit" id="vaciar-carrito" class="btn-2" <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>Vaciar carrito</button>
                </form>
                <form method="POST" action="pagar.php">
                    <button type="submit" class="btn-pagar" <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>Proceder a Pagar</button>
                </form>
            </div>
        </li>
    </ul>
</div>

    <header>
        <h1>Productos</h1>
    </header>
    
    <main class="catalogo-container main-container">
        <?php foreach ($productos as $producto): ?>
            <div class="product-card">
                <img src="uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['titulo']) ?>">
                <h3><?= htmlspecialchars($producto['titulo']) ?></h3>
                <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                <p class="precio">$ <?= number_format($producto['precio'], 2) ?></p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= $producto['id'] ?>">
                    <button type="submit" class="btn-agregar">Agregar al carrito</button>
                </form>
            </div>
        <?php endforeach; ?>
    </main>

    <script src="script.js" defer></script>
</body>
</html>
