<?php 
// Inkludera databasanslutningen
require_once 'db.php';

// Hantera POST-begäran för att lägga till en ny film
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    // Hämta och trimma inmatade värden från formuläret
    $titel = trim($_POST['titel'] ?? '');
    $regissor = trim($_POST['regissor'] ?? '');
    $ar = intval($_POST['ar'] ?? 0);
    $kategori_id = intval($_POST['kategori'] ?? 0);

    // Enkel validering av inmatning: kontrollera att alla fält är ifyllda och rimliga
    if ($titel && $regissor && $ar > 1800 && $kategori_id > 0) {
        // Förbered SQL för att lägga till filmen i databasen
        $stmt = $pdo->prepare("INSERT INTO filmer (titel, regissor, ar, kategori_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titel, $regissor, $ar, $kategori_id]);

        // Redirecta till startsidan för att undvika att formuläret skickas in flera gånger
        header("Location: index.php");
        exit();
    } else {
        // Om validering misslyckas, visa felmeddelande
        $error = "Vänligen fyll i alla fält korrekt.";
    }
}

// Hämta alla kategorier från databasen för att visa som radioknappar i formuläret
$stmt = $pdo->query("SELECT id, namn FROM kategorier ORDER BY namn");
$kategorier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hämta alla filmer med kategori-namn via en JOIN mellan tabellerna
$sql = "SELECT f.id, f.titel, f.regissor, f.ar, k.namn AS kategori FROM filmer f 
        INNER JOIN kategorier k ON f.kategori_id = k.id
        ORDER BY f.titel";
$stmt = $pdo->query($sql);
$filmer = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8" />
    <title>Film Bibliotek</title>
    <style>
        /* Enkel stil för tabellen och formuläret */
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        form { width: 90%; margin: 20px auto; }
        .error { color: red; margin: 10px; }
        .radio-group { margin-bottom: 10px; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Film Bibliotek</h1>

<!-- Visa felmeddelande om något är fel -->
<?php if (!empty($error)): ?>
    <p class="error"><?=htmlspecialchars($error)?></p>
<?php endif; ?>

<!-- Formulär för att lägga till en ny film -->
<form method="POST" action="index.php">
    <label for="titel">Titel:</label><br />
    <input type="text" id="titel" name="titel" required><br /><br />

    <label for="regissor">Regissör:</label><br />
    <input type="text" id="regissor" name="regissor" required><br /><br />

    <label for="ar">Produktionsår:</label><br />
    <input type="number" id="ar" name="ar" min="1888" max="<?=date("Y")?>" required><br /><br />

    <div class="radio-group">
        <label>Kategori:</label><br />
        <!-- Loopar genom kategorier och skapar radioknappar för varje -->
        <?php foreach ($kategorier as $kategori): ?>
            <input type="radio" id="kat<?=$kategori['id']?>" name="kategori" value="<?=$kategori['id']?>" required>
            <label for="kat<?=$kategori['id']?>"><?=htmlspecialchars($kategori['namn'])?></label><br />
        <?php endforeach; ?>
    </div>

    <button type="submit" name="add_movie">Lägg till film</button>
</form>

<!-- Tabell som visar alla filmer -->
<table>
    <thead>
        <tr>
            <th>Titel</th>
            <th>Regissör</th>
            <th>År</th>
            <th>Kategori</th>
            <th>Åtgärder</th>
        </tr>
    </thead>
    <tbody>
        <!-- Visa meddelande om inga filmer finns -->
        <?php if (count($filmer) === 0): ?>
            <tr><td colspan="5" style="text-align:center;">Inga filmer hittades.</td></tr>
        <?php else: ?>
            <!-- Loopa igenom alla filmer och visa dem i tabellen -->
            <?php foreach ($filmer as $film): ?>
                <tr>
                    <td><?=htmlspecialchars($film['titel'])?></td>
                    <td><?=htmlspecialchars($film['regissor'])?></td>
                    <td><?=htmlspecialchars($film['ar'])?></td>
                    <td><?=htmlspecialchars($film['kategori'])?></td>
                    <td>
                        <!-- Länkar för att redigera eller ta bort filmen -->
                        <a href="edit.php?id=<?=$film['id']?>">Edit</a> |
                        <a href="delete.php?id=<?=$film['id']?>" onclick="return confirm('Vill du verkligen ta bort filmen?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
