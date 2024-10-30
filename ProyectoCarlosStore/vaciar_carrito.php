<?php
session_start();
unset($_SESSION['cart']);
$_SESSION['mensaje'] = "El carrito ha sido vaciado.";
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
