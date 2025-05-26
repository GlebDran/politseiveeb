<?php
require_once("abifunktsioonid.php");
// Запускаем сессию — получаем текущую роль пользователя
session_start();

// Разрешаем только вошедшим пользователям с ролью "kasutaja"
if (!isset($_SESSION["roll"]) || $_SESSION["roll"] != 0) {
    header("Location: login2.php");
    exit;
}

// Получаем список полицейских и их отделов
global $yhendus;
$kask = $yhendus->prepare("
    SELECT p.nimi, p.pnimi, p.auaste, p.isikukood, o.nimi AS osakond
    FROM politseinik p
    // Присоединяем данные о полицейском участке для вывода вместе с полицейским o ON p.osakond_id = o.id
");
$kask->execute();
$kask->bind_result($nimi, $pnimi, $auaste, $isikukood, $osakond);
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Politseinike vaade</title>
</head>
<body>
<header class="topbar">
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="politseilogo.png" alt="Politsei logo">
    </div>
</header>
<h2>Politseinike nimekiri</h2>
<link rel="stylesheet" href="politsei.css">
<table>
    <tr>
        <th>Eesnimi</th>
        <th>Perekonnanimi</th>
        <th>Auaste</th>
        <th>Isikukood</th>
        <th>Osakond</th>
    </tr>

    <?php while ($kask->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($nimi) ?></td>
            <td><?= htmlspecialchars($pnimi) ?></td>
            <td><?= htmlspecialchars($auaste) ?></td>
            <td><?= htmlspecialchars($isikukood) ?></td>
            <td><?= htmlspecialchars($osakond) ?></td>
        </tr>
    <?php endwhile; ?>

</table>

<br>
<a href="logout.php">Logi välja</a>
<footer class="footer">
    <a href="https://www.politsei.ee/" target="_blank">
        <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
    </a>
</footer>

</body>
</html>
