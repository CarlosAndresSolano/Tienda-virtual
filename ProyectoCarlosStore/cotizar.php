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
    die("Error en la consulta: " . $e->getMessage());
}

$total = 0;
$total_con_descuento = 0;
$tipo_cliente = '';
$descuento = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo_cliente = $_POST['tipo_cliente'];
    $productos_json = $_POST['productos_json'] ?? '[]';
    $selected_products = json_decode($productos_json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error al decodificar JSON de productos.");
    }

    $grouped_products = [];

    foreach ($selected_products as $product) {
        if (isset($product['id']) && isset($product['cantidad']) && is_numeric($product['cantidad']) && $product['cantidad'] > 0) {
            $id = $product['id'];
            $cantidad = (int)$product['cantidad'];

            if (!isset($grouped_products[$id])) {
                $grouped_products[$id] = 0; 
            }
            $grouped_products[$id] += $cantidad; 
        } else {
            echo "<p>Error: Cada producto seleccionado debe tener un 'id' y una 'cantidad' válida.</p>";
            exit; 
        }
    }

    
    foreach ($grouped_products as $id => $cantidad) {
        foreach ($productos as $producto) {
            if ($producto['id'] == $id) {
                $subtotal = $producto['precio'] * $cantidad; 
                $total += $subtotal;
                break; 
            }
        }
    }

    switch ($tipo_cliente) {
        case 'Permanente':
            $descuento = 0.10;
            break;
        case 'Periódico':
            $descuento = 0.05; 
            break;
        case 'Casual':
            $descuento = 0.02; 
            break;
        case 'Nuevo':
            $descuento = 0; 
            break;
    }

    
    $total_con_descuento = $total * (1 - $descuento);
 
    if ($total_con_descuento > 100000) {
        $total_con_descuento *= 0.98; 
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones - Hardware Store Nuts</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .nav-back {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .nav-back a {
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-transform: uppercase;
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
        }

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
        .submenu:hover #carrito {
            display: block;
        }
        #carrito {
            display: none;
            position: absolute;
            top: 120%;
            right: 0;
            width: 400px;
            background-color: rgba(255, 255, 255, 0.97);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .cotizacion-container {
            max-width: 700px;
            margin: 80px auto;
            background-color: #f5f1eb;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .cotizacion-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group select, .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .product-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .product-selector select, .product-selector input[type="number"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }

        .add-product {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        
        .btn-cotizar {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-cotizar:hover {
            background-color: #0056b3;
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
        <h1>Cotización de productos</h1>
    </header>

    <main class="cotizacion-container">
        <h2>Cotización de productos</h2>
        <form method="POST" action="" onsubmit="return prepareProductsData()">
            <div class="form-group">
                <label for="tipo_cliente">Tipo de Cliente</label>
                <select name="tipo_cliente" id="tipo_cliente" required>
                    <option value="Permanente">Permanente</option>
                    <option value="Periódico">Periódico</option>
                    <option value="Casual">Casual</option>
                    <option value="Nuevo">Nuevo</option>
                </select>
            </div>

            <div id="product-rows">
                <div class="product-selector">
                    <select name="product_id" required>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['titulo']) ?> - $<?= number_format($producto['precio'], 2) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="product_quantity" min="1" placeholder="Cantidad" required>
                </div>
            </div>
            
            <button type="button" class="add-product" onclick="addProductRow()">+ Agregar producto</button>

            <input type="hidden" name="productos_json" id="productos_json">

            <button type="submit" class="btn-cotizar">Generar cotización</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="total">
                <p><strong>Total sin Descuento:</strong> $<?= number_format($total, 2) ?></p>
                <p><strong>Total con Descuento:</strong> $<?= number_format($total_con_descuento, 2) ?></p>
            </div>
        <?php endif; ?>
    </main>

    <script>

        const selectedProducts = [];

        function addProductRow() {
            const container = document.getElementById('product-rows');
            const productRow = document.createElement('div');
            productRow.className = 'product-selector';
            productRow.innerHTML = `
                <select name="product_id" required>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['titulo']) ?> - $<?= number_format($producto['precio'], 2) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="product_quantity" min="1" placeholder="Cantidad" required>
            `;
            container.appendChild(productRow);
        }

        function prepareProductsData() {
            selectedProducts.length = 0; 

            const rows = document.querySelectorAll('.product-selector');
            rows.forEach(row => {
                const productId = row.querySelector('select[name="product_id"]').value;
                const productQuantity = row.querySelector('input[name="product_quantity"]').value;

                if (productId && productQuantity > 0) {
                    selectedProducts.push({
                        id: productId,
                        cantidad: parseInt(productQuantity)
                    });
                }
            });

            document.getElementById('productos_json').value = JSON.stringify(selectedProducts);
            return true; 
        }
    </script>
</body>
</html>
