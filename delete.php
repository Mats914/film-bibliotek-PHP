<?php

// Inkludera databasanslutningen från db.php
require_once 'db.php';

// Kontrollera att en film-id skickats med i URL:en via GET
if (!isset($_GET['id'])) {
    // Om inget id finns, avsluta skriptet med ett meddelande
    die("Ingen film vald.");
}

// Hämta filmens id från GET-parametern och gör om till heltal för säkerhet
$id = (int) $_GET['id'];

// Förbered ett SQL-kommando för att ta bort filmen med angivet id
$stmt = $pdo->prepare("DELETE FROM filmer WHERE id = ?");

// Kör SQL-kommandot med det säkra id:t som parameter
$stmt->execute([$id]);

// Efter borttagning, skicka tillbaka användaren till startsidan (filmlistan)
header("Location: index.php");
exit();

?>
