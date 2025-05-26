<?php
// Запускаем сессию, чтобы очистить её данные
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: login2.php');
    exit();
}
if(isset($_POST['logout'])){
    // Удаляем все переменные сессии и завершаем её
session_destroy();
    header('Location: admin.php');
    exit();
}
?>