<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['usuario_id'];
$message = "";

try {
    $stmt = $pdo->prepare("SELECT nombres, apellidos, celular, email FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, celular = ?, email = ? WHERE id = ?");
        $stmt->execute([$nombres, $apellidos, $celular, $email, $user_id]);
        $message = "Información actualizada correctamente.";
        $user['nombres'] = $nombres;
        $user['apellidos'] = $apellidos;
        $user['celular'] = $celular;
        $user['email'] = $email;
    } catch (PDOException $e) {
        $message = "Error al actualizar la información: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        session_destroy();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $message = "Error al eliminar la cuenta: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Hardware Store Nuts</title>
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

        .profile-container {
            max-width: 600px;
            margin: 80px auto;
            background-color: #f5f1eb;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-update {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-update:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-delete:hover {
            background-color: #e60000;
        }

        .message {
            text-align: center;
            font-size: 14px;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="nav-back">
        <a href="index.php">Volver al inicio</a>
    </div>
    <header>
        <h1>Perfil de Usuario</h1>
    </header>

    <main class="profile-container">
        <h2>Editar Perfil</h2>
    
        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" id="nombres" name="nombres" value="<?= htmlspecialchars($user['nombres']) ?>" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($user['apellidos']) ?>" required>
            </div>
            <div class="form-group">
                <label for="celular">Celular</label>
                <input type="tel" id="celular" name="celular" value="<?= htmlspecialchars($user['celular']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" name="update" class="btn-update">Actualizar Información</button>
        </form>

        <form method="POST" action="">
            <button type="submit" name="delete" class="btn-delete">Eliminar Cuenta</button>
        </form>
    </main>
</body>
</html>
