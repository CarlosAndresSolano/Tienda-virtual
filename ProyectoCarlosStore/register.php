<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $celular = trim($_POST['celular']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $rol = 'usuario'; 

    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = "El email ya está registrado. Por favor, usa otro.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombres, apellidos, celular, email, contrasena, rol, fecha_registro) 
                                    VALUES (:nombre, :apellido, :celular, :email, :password, :rol, NOW())");
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'celular' => $celular,
                'email' => $email,
                'password' => $password,
                'rol' => $rol
            ]);
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Ha ocurrido un error, por favor inténtalo más tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Hardware Store Nuts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav-back">
        <a href="index.php">Volver al inicio</a>
    </div>
    <header>
        <h1>Registro</h1>
    </header>
    <main class="main-container">
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
            
            <label for="celular">Celular:</label>
            <input type="text" id="celular" name="celular" required>
            
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </main>
</body>
</html>
