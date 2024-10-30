<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['imagen']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFilePath)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO productos (titulo, descripcion, precio, imagen) VALUES (:titulo, :descripcion, :precio, :imagen)");
                    $stmt->execute([
                        'titulo' => $titulo,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'imagen' => $imageFileName
                    ]);
                    $success = "Producto agregado exitosamente";
                } catch (PDOException $e) {
                    $error = "Error al agregar el producto: " . $e->getMessage();
                }
            } else {
                $error = "Hubo un error al subir la imagen.";
            }
        } else {
            $error = "Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.";
        }
    } else {
        $error = "Por favor, sube una imagen del producto.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Hardware Store Nuts</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 10px;
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Nuevo Producto</h1>
        <?php if (isset($success)): ?>
            <p class="message"><?= htmlspecialchars($success) ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form method="POST" action="agregar_producto.php" enctype="multipart/form-data">
            <label for="titulo">Título del Producto:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="imagen">Imagen del Producto:</label>
            <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.gif,.webp" required>

            <button type="submit">Agregar Producto</button>
        </form>
        
        <a href="index.php">Volver al inicio</a>
    </div>
</body>
</html>
