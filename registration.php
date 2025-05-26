<?php
// Запускаем сессию, чтобы можно было перенаправить после успешной регистрации
session_start();
require('zoneconf.php');


// Обработка формы регистрации после отправки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutajanimi = $_POST['kasutajanimi'];
    $parool = $_POST['parool'];
    $roll = '0'; // фиксировано

    // Проверяем, существует ли уже такой логин
    /** @var mysqli $conn */
    $stmt = $conn->prepare("SELECT id FROM kasutajad1 WHERE kasutajanimi = ?");
    $stmt->bind_param("s", $kasutajanimi);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Kasutajanimi on juba võetud.";
    } else {
        $hash = password_hash($parool, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("// Сохраняем нового пользователя в базу (kasutajanimi, parool, roll) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kasutajanimi, $hash, $roll);
        if ($stmt->execute()) {
            echo "Registreerimine õnnestus! <a href='login2.php'>Logi sisse</a>";
        } else {
            echo "Viga registreerimisel: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<header class="topbar">
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="politseilogo.png" alt="Politsei logo">
    </div>
</header>
<h2>Registreerimine</h2>
<link rel="stylesheet" href="politsei.css">
<form method="post">
    Kasutajanimi: <input type="text" name="kasutajanimi" required><br>
    Parool: <input type="password" name="parool" required><br>
    <input type="submit" value="Registreeri">
    <button onclick="location.href='login2.php'">← Tagasi</button>
</form>
<footer class="footer">
    <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
</footer>