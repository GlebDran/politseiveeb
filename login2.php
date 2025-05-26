<?php
ob_start();
// Запускаем сессию — данные пользователя сохраняются в $_SESSION
session_start();
require_once('zoneconf.php');

// Обработка формы входа после нажатия кнопки "Logi sisse"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutajanimi = $_POST["kasutajanimi"];
    $parool = $_POST["parool"];

    // Подготавливаем SQL-запрос: находим пользователя по имени
$stmt = $conn->prepare("SELECT id, parool, roll FROM kasutajad1 WHERE kasutajanimi = ?");
    $stmt->bind_param("s", $kasutajanimi);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashedPassword, $roll);
        $stmt->fetch();

    // Сравниваем введённый пароль с захешированным
if (password_verify($parool, $hashedPassword)) {
        $_SESSION["kasutaja_id"] = $id;
        $_SESSION["kasutajanimi"] = $kasutajanimi;
        $_SESSION["roll"] = $roll;

        // Перенаправляем пользователя в зависимости от его роли
header("Location: " . ($roll == 1 ? "admin.php" : "politseivaade.php"));
        exit;
        } else {
            $teade = "Vale parool.";
        }
    } else {
        $teade = "Kasutajat ei leitud.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Logi sisse</title>
    <link rel="stylesheet" href="politsei.css">
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

<h2>Logi sisse</h2>
<?php if (!empty($teade)): ?>
    <p style="color:red;"><?= $teade ?></p>
<?php endif; ?>

<form method="post">
    Kasutajanimi: <input type="text" name="kasutajanimi" required><br>
    Parool: <input type="password" name="parool" required><br>
    <input type="submit" value="Logi sisse">
    <button type="button" onclick="location.href='registration.php'">Registreeru</button>
</form>

<footer class="footer">
    <a href="https://www.politsei.ee/" target="_blank">
        <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
    </a>
</footer>
</body>
</html>
