<?php
// Запускаем сессию, чтобы получить доступ к данным входа
session_start();
if (!isset($_SESSION["roll"]) || $_SESSION["roll"] != "1") {
    header("Location: login2.php");
    exit;
}
require_once('zoneconf.php');

$teade = "";

// Kustutamine
/** @var mysqli $conn */
// Если в GET-параметре есть ?delete=ID, удаляем указанного полицейского
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    if ($conn->query("DELETE FROM politseinik WHERE id = $id")) {
        $teade = "<p style='color:green;'>Politseinik kustutatud.</p>";
    } else {
        $teade = "<p style='color:red;'>Viga kustutamisel: " . $conn->error . "</p>";
    }
}

// Lisamine
//  Обработка формы добавления полицейского
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["lisa"])) {
    $nimi = $_POST["nimi"];
    $pnimi = $_POST["pnimi"];
    $auaste = $_POST["auaste"];
    $isikukood = $_POST["isikukood"];
    $osakond_id = intval($_POST["osakond_id"]);

    $stmt = $conn->prepare("INSERT INTO politseinik (nimi, pnimi, auaste, isikukood, osakond_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nimi, $pnimi, $auaste, $isikukood, $osakond_id);
    if ($stmt->execute()) {
        $teade = "<p style='color:green;'>Politseinik lisatud edukalt!</p>";
    } else {
        $teade = "<p style='color:red;'>Viga lisamisel: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Загружаем список всех участков (osakonnad), чтобы показать в выпадающем списке
$osakonnad = $conn->query("SELECT id, nimi FROM politseiosakond");

// Загружаем всех полицейских вместе с их отделами для отображения в таблице
$politseinikud = $conn->query("SELECT p.id, p.nimi, p.pnimi, p.auaste, p.isikukood, o.nimi AS osakond
FROM politseinik p
LEFT JOIN politseiosakond o ON p.osakond_id = o.id");
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Politseinike haldus</title>
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
<h2>Politseinike haldus (admin)</h2>

<?= $teade ?>

<table>
    <tr>
        <th>Eesnimi</th>
        <th>Perekonnanimi</th>
        <th>Auaste</th>
        <th>Isikukood</th>
        <th>Osakond</th>
        <th>Tegevus</th>
    </tr>
    <?php while ($r = $politseinikud->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r["nimi"]) ?></td>
            <td><?= htmlspecialchars($r["pnimi"]) ?></td>
            <td><?= htmlspecialchars($r["auaste"]) ?></td>
            <td><?= htmlspecialchars($r["isikukood"]) ?></td>
            <td><?= htmlspecialchars($r["osakond"]) ?></td>
            <td><a href="?delete=<?= $r["id"] ?>" onclick="return confirm('Kustuta?')">Kustuta</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>Lisa uus politseinik</h3>
<form method="post">
    Eesnimi: <input type="text" name="nimi" required><br>
    Perekonnanimi: <input type="text" name="pnimi" required><br>
    Auaste: <input type="text" name="auaste"><br>
    Isikukood: <input type="text" name="isikukood" maxlength="11" pattern="\d{11}" title="Täpselt 11 numbrit" required><br>
    Osakond:
    <select name="osakond_id">
        <?php while ($o = $osakonnad->fetch_assoc()): ?>
            <option value="<?= $o["id"] ?>"><?= htmlspecialchars($o["nimi"]) ?></option>
        <?php endwhile; ?>
    </select><br><br>
    <input type="submit" name="lisa" value="Lisa">
<button onclick="location.href='kasutajahaldus.php'">Kasutajate haldus</button>
<button onclick="location.href='kuritegevusHaldus.php'">Kuritegevus haldus</button>
</form>

<br>
<a href="logout.php">Logi välja</a>
<footer class="footer">
    <a href="https://www.politsei.ee/" target="_blank">
        <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
    </a>
</footer>

</body>
</html>
