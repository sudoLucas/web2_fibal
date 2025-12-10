<?php
session_start();
$_SESSION['carrito'] = array();
header('Location: carrito.php');
exit();
?>