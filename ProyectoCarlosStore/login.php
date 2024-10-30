<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT id, nombres, contrasena, rol FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombres'];
            $_SESSION['usuario_rol'] = $usuario['rol']; 
            header("Location: index.php");
            exit;
        } else {

            $error = "Email o contraseña incorrectos.";
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
    <title>Iniciar Sesión - Hardware Store Nuts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav-back">
        <a href="index.php">Volver al inicio</a>
    </div>
    <header>
        <h1>Iniciar Sesión</h1>
    </header>
    <main class="main-container">
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </main>
</body>
</html>
