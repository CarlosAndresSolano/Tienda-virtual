<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$carrito_count = array_sum(array_column($_SESSION['cart'], 'cantidad'));

$isAdmin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HARDWARE STORE NUTS</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header class="header">
        <div class="menu containerMenu">
            <a href="index.php" class="logo">
                <img src="imagenes/logo.png" alt="HARDWARE STORE NUTS" style="width: 80px; height: auto;">
            </a>

            <input type="checkbox" id="menu" />
            <label for="menu">
                <img src="imagenes/menu.png" class="menu-icono" alt="menu" />
            </label>
            <nav class="navbar">
                <ul>
                    <li><a href="catalogo.php">Catálogo</a></li>
                    <li><a href="cotizar.php">Cotizar</a></li>
                    <li><a href="quienes_somos.php">Quiénes somos</a></li>
                    <?php if (isset($_SESSION['usuario_nombre'])): ?>
                        <li class="submenu-usuario">
                            <a href="#"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></a>
                            <ul class="submenu-opciones">
                                <li><a href="perfil.php">Perfil</a></li>
                                <?php if ($isAdmin): ?>
                                    <li><a href="agregar_producto.php">Agregar productos</a></li>
                                <?php endif; ?>
                                <li><a href="logout.php">Cerrar sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>

                        <li><a href="login.php">Inicio de sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

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

        </div>

        <div class="header-contend containerHeader">
            <div class="header-img">
                <img src="imagenes/cable.jpg" alt="" />
            </div>
            <div class="header-txt">
                <h1>Ofertas especiales</h1>
                <p>Marcas garantizadas</p>
                <a href="catalogo.php" class="btn-1">Información</a>
            </div>
        </div>
    </header>

    <section class="icon-1">
      <div class="icon-img">
        <img src="imagenes/camion.svg" alt="" />
      </div>
      <div class="icon-txt">
        <h3>Envios a toda colombia</h3>
        <p>Entregamos en el menor tiempo posible</p>
      </div>

      <div class="icon-img">
        <img src="imagenes/usuario.png" alt="" />
      </div>
      <div class="icon-txt">
        <h3>Registrate</h3>
        <p>Obten cupones de descuento</p>
      </div>

      <div class="icon-img">
        <img src="imagenes/pse.jpg" alt="" />
      </div>
      <div class="icon-txt">
        <h3>Pago en linea</h3>
        <p></p>
      </div>
    </section>

    <script src="script.js" defer></script>
</body>
</html>