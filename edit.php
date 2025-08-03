<?php 
// Inkludera databasanslutningen
require_once 'db.php';

// Kontrollera att 'id' finns i URL:en och att det är ett numeriskt värde
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ogiltigt film-ID.");
}

// Hämta filmens id från GET-parametern och konvertera till heltal för säkerhet
$id = (int) $_GET['id'];

// Hämta alla kategorier från databasen för att visa som radioknappar i formuläret
$stmt = $pdo->query("SELECT id, namn FROM kategorier ORDER BY namn");
$kategorier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hämta filmen som ska redigeras baserat på id
$stmt = $pdo->prepare("SELECT * FROM filmer WHERE id = ?");
$stmt->execute([$id]);
$film = $stmt->fetch(PDO::FETCH_ASSOC);

// Om filmen inte finns, avsluta med felmeddelande
if (!$film) {
    die("Filmen hittades inte.");
}

// Hantera formulärskick vid uppdatering av filmen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    // Hämta och trimma inmatade värden från formuläret
    $titel = trim($_POST['titel'] ?? '');
    $regissor = trim($_POST['regissor'] ?? '');
    $ar = intval($_POST['ar'] ?? 0);
    $kategori_id = intval($_POST['kategori'] ?? 0);

    // Kontrollera att alla fält är ifyllda korrekt och att år är rimligt (>1800)
    if ($titel && $regissor && $ar > 1800 && $kategori_id > 0) {
        // Förbered SQL för att uppdatera filmen i databasen
        $stmt = $pdo->prepare("UPDATE filmer SET titel = ?, regissor = ?, ar = ?, kategori_id = ? WHERE id = ?");
        $stmt->execute([$titel, $regissor, $ar, $kategori_id, $id]);
        
        // Skicka användaren tillbaka till startsidan efter uppdatering
        header("Location: index.php");
        exit();
    } else {
        // Om valideringen misslyckas, visa felmeddelande
        $error = "Vänligen fyll i alla fält korrekt.";
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <link rel="stylesheet" href="style.css" />

    <meta charset="UTF-8" />
    <title>Redigera film</title>
    <style>
        form { width: 90%; margin: 20px auto; }
        .error { color: red; margin: 10px; }
        .radio-group { margin-bottom: 10px; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Redigera film</h1>

<!-- Visa eventuellt felmeddelande -->
<?php if (!empty($error)): ?>
    <p class="error"><?=htmlspecialchars($error)?></p>
<?php endif; ?>

<!-- Formulär för att redigera film -->
<form method="POST" action="edit.php?id=<?=$id?>">
    <label for="titel">Titel:</label><br />
    <input type="text" id="titel" name="titel" value="<?=htmlspecialchars($film['titel'])?>" required><br /><br />

    <label for="regissor">Regissör:</label><br />
    <input type="text" id="regissor" name="regissor" value="<?=htmlspecialchars($film['regissor'])?>" required><br /><br />

    <label for="ar">Produktionsår:</label><br />
    <input type="number" id="ar" name="ar" min="1888" max="<?=date("Y")?>" value="<?=htmlspecialchars($film['ar'])?>" required><br /><br />

    <div class="radio-group">
        <label>Kategori:</label><br />
        <!-- Loopar igenom kategorier och skapar en radioknapp för varje -->
        <?php foreach ($kategorier as $kategori): ?>
            <input type="radio" id="kat<?=$kategori['id']?>" name="kategori" value="<?=$kategori['id']?>" 
                <?=($kategori['id'] == $film['kategori_id']) ? 'checked' : ''?> required>
            <label for="kat<?=$kategori['id']?>"><?=htmlspecialchars($kategori['namn'])?></label><br />
        <?php endforeach; ?>
    </div>

    <button type="submit" name="update_movie">Uppdatera film</button>
</form>

<p style="text-align:center; margin-top: 20px;">
    <a href="index.php">Tillbaka till filmbiblioteket</a>
</p>

</body>
</html>
