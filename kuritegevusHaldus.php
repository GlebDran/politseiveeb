<?php
require_once("abifunktsioonid.php");
// Запуск сессии для проверки роли пользователя
session_start();

$teade = "";
if (isset($_SESSION["teade"])) {
    $teade = $_SESSION["teade"];
    unset($_SESSION["teade"]);
}

if (!isset($_SESSION["roll"]) || $_SESSION["roll"] != "1") {
    header("Location: login2.php");
    exit;
}

// Kustutamine
// Если передан GET-параметр ?delete=ID, удаляем преступление и связи
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $yhendus->query("DELETE FROM kuriteo_kurjategija WHERE kuritegevus_id=$id");
    $yhendus->query("DELETE FROM kuritegevus WHERE id=$id");
    $teade = "<p style='color:green;'>Kuritegu kustutatud.</p>";
}

// Lisamine
// Обработка формы добавления нового преступления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["lisa"])) {
    if (
        (empty($_POST["kurjategija_id"]) || $_POST["kurjategija_id"] === "") &&
        empty(trim($_POST["uus_kurjategija"]))
    ) {
        $teade = "<p style='color:red;'>Vali kurjategija või sisesta uus nimi!</p>";
    } else {
        $tyyp = $_POST["kuriteg_tyyp"];
        $kirjeldus = $_POST["kirjeldus"];
        $kuupaev = $_POST["kuupaev"];
        $asukoht = $_POST["asukoht"];
        $politseinik_id = intval($_POST["politseinik_id"]);
        // Если указан новый преступник — добавляем в таблицу
if (!empty($_POST["uus_kurjategija"])) {
            $nimi = trim($_POST["uus_kurjategija"]);
            $stmt3 = $yhendus->prepare("INSERT INTO kurjategija (nimi, pnimi) VALUES (?, '')");
            $stmt3->bind_param("s", $nimi);
            $stmt3->execute();
            $kurjategija_id = $stmt3->insert_id;
            $stmt3->close();
        } else {
            $kurjategija_id = intval($_POST["kurjategija_id"]);
        }

        $stmt = $yhendus->prepare("INSERT INTO kuritegevus (kuriteg_tyyp, kirjeldus, kuupaev, asukoht, politseinik_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $tyyp, $kirjeldus, $kuupaev, $asukoht, $politseinik_id);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();

        $stmt_check = $yhendus->prepare("SELECT 1 FROM kuriteo_kurjategija WHERE kuritegevus_id = ? AND kurjategija_id = ?");
        $stmt_check->bind_param("ii", $new_id, $kurjategija_id);
        $stmt_check->execute();
        $stmt_check->store_result();

if ($stmt_check->num_rows == 0) {
    // Только если нет — добавляем
    $stmt2 = $yhendus->prepare("INSERT INTO kuriteo_kurjategija (kuritegevus_id, kurjategija_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $new_id, $kurjategija_id);
    $stmt2->execute();
    $stmt2->close();
}

$stmt_check->close();

        $_SESSION["teade"] = "<p style='color:green;'>Kuritegu lisatud edukalt!</p>";
        header("Location: kuritegevusHaldus.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kuritegude haldus</title>
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
<h2>Kuritegude haldus</h2>
<?= $teade ?>

<table>
    <tr>
        <th>Tüüp</th>
        <th>Kirjeldus</th>
        <th>Kuupäev</th>
        <th>Asukoht</th>
        <th>Politseinik</th>
        <th>Kurjategija</th>
        <th>Kustuta</th>
    </tr>
    <?php
    $kask = $yhendus->query("
        SELECT 
            k.id, 
            k.kuriteg_tyyp, 
            k.kirjeldus, 
            k.kuupaev, 
            k.asukoht,
            p.nimi AS politseinik_nimi, 
            p.pnimi AS politseinik_pnimi,
            GROUP_CONCAT(CONCAT(kg.nimi, ' ', kg.pnimi) SEPARATOR ', ') AS kurjategijad
        FROM kuritegevus k
        // Присоединяем данные полицейских p ON k.politseinik_id = p.id
        // Присоединяем таблицу связей преступление-преступник kk ON k.id = kk.kuritegevus_id
        // Присоединяем имена преступников kg ON kk.kurjategija_id = kg.id
        GROUP BY k.id
    ");
    while ($r = $kask->fetch_assoc()):
    ?>
        <tr>
            <td><?= htmlspecialchars($r["kuriteg_tyyp"]) ?></td>
            <td><?= htmlspecialchars($r["kirjeldus"]) ?></td>
            <td><?= htmlspecialchars($r["kuupaev"]) ?></td>
            <td><?= htmlspecialchars($r["asukoht"]) ?></td>
            <td><?= htmlspecialchars($r["politseinik_nimi"] . " " . $r["politseinik_pnimi"]) ?></td>
            <td><?= htmlspecialchars($r["kurjategijad"]) ?></td>
            <td><a href="?delete=<?= $r["id"] ?>" onclick="return confirm('Kustuta kuritegu?')">Kustuta</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>Lisa uus kuritegu</h3>
<form method="post">
    Kuriteo tüüp: <input type="text" name="kuriteg_tyyp" required><br>
    Kirjeldus: <textarea name="kirjeldus" required></textarea><br>
    Kuupäev: <input type="date" name="kuupaev" required><br>
    Asukoht: <input type="text" name="asukoht"><br>

    Politseinik:
    <select name="politseinik_id">
        <?php
        $politseinikud = $yhendus->query("SELECT id, nimi, pnimi FROM politseinik");
        while ($r = $politseinikud->fetch_assoc()) {
            echo "<option value='{$r["id"]}'>" . htmlspecialchars($r["nimi"]) . " " . htmlspecialchars($r["pnimi"]) . "</option>";
        }
        ?>
    </select><br>

    <label for="kurjategija_id">Kurjategija:</label>
    <select name="kurjategija_id">
        <option value="">Vali olemasolev</option>
        <?php
        $kurjategijad = $yhendus->query("SELECT id, nimi, pnimi FROM kurjategija");
        while ($r = $kurjategijad->fetch_assoc()) {
            echo "<option value='{$r["id"]}'>" . htmlspecialchars($r["nimi"]) . " " . htmlspecialchars($r["pnimi"]) . "</option>";
        }
        ?>
    </select><br>

    Või sisesta uus kurjategija nimi: <input type="text" name="uus_kurjategija"><br><br>

    <input type="submit" name="lisa" value="Lisa kuritegu">
</form>

<br>
<button onclick="location.href='admin.php'">← Tagasi admini lehele</button>
<footer class="footer">
    <a href="https://www.politsei.ee/" target="_blank">
        <img src="logofooter.png" alt="Politsei ja Piirivalveamet logo">
    </a>
</footer>

</body>
</html>
