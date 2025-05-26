<?php
require_once("abifunktsioonid.php");
// Запускаем сессию, чтобы получить доступ к роли
session_start();

if (!isset($_SESSION["roll"]) || $_SESSION["roll"] != "1") {
    header("Location: login2.php");
    exit;
}

$teade = "";

// Määrame admini rolli
if (isset($_GET["teeadmin"])) {
    $id = intval($_GET["teeadmin"]);
    $stmt = $yhendus->prepare("UPDATE kasutajad1 SET roll='1' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $teade = "Kasutajale määrati admini roll.";
}

// Eemaldame admini rolli
if (isset($_GET["eemaldaadmin"])) {
    $id = intval($_GET["eemaldaadmin"]);
    $stmt = $yhendus->prepare("UPDATE kasutajad1 SET roll='0' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $teade = "Admini roll eemaldati.";
}

// Kuvame kõik kasutajad
$kasutajad = $yhendus->query("// Запрос: получить всех пользователей и их роли
SELECT id, kasutajanimi, roll FROM kasutajad1");
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kasutajate haldus</title>
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
<h2>Kasutajate haldus</h2>

<p style="color:green;"><?= $teade ?></p>

<table>
    <tr>
        <th>Kasutajanimi</th>
        <th>Roll</th>
        <th>Muuda rolli</th>
    </tr>

    <?php while ($r = $kasutajad->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r["kasutajanimi"]) ?></td>
            <td><?= htmlspecialchars($r["roll"]) ?></td>
            <td>
                <?php if ($r["roll"] === "0"): ?>
                    <a href="?teeadmin=<?= $r["id"] ?>">Määra admin</a>
                <?php elseif ($r["roll"] === "1" && $r["kasutajanimi"] !== $_SESSION["kasutajanimi"]): ?>
                    <a href="?eemaldaadmin=<?= $r["id"] ?>">Eemalda admin</a>
                <?php else: ?>
                    <!-- iseennast ei saa eemaldada -->
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<button onclick="location.href='admin.php'">← Tagasi admini pealehele</button>
<footer class="footer">
    <a href="https://www.politsei.ee/" target="_blank">
        <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
    </a>
</footer>

</body>
</html>
