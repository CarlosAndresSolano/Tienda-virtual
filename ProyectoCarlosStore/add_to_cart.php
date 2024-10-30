<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, titulo, precio, imagen FROM productos WHERE id = ?");
        $stmt->execute([$product_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $producto['id']) {
                    $item['cantidad']++; 
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $producto['cantidad'] = 1; 
                $_SESSION['cart'][] = $producto; 
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

header("Location: catalogo.php");
exit();
