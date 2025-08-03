<?php
// Ladda in miljövariabler från en .env-fil med hjälp av composer's autoloader och biblioteket vlucas/phpdotenv
require_once __DIR__ . '/vendor/autoload.php';

// Importera Dotenv-klassen från vlucas/phpdotenv
use Dotenv\Dotenv;

// Skapa en instans av Dotenv som laddar miljövariabler från nuvarande mapp
$dotenv = Dotenv::createImmutable(__DIR__);
// Ladda miljövariablerna så att de blir tillgängliga via $_ENV
$dotenv->load();

// Hämta databasvärden från miljövariablerna, om de inte finns sätts standardvärden
$host   = $_ENV['DB_HOST'] ?? 'localhost';          // Databasvärd
$dbname = $_ENV['DB_NAME'] ?? 'film_bibliotek';     // Databasnamn
$user   = $_ENV['DB_USER'] ?? 'root';                // Databas användarnamn
$pass   = $_ENV['DB_PASS'] ?? '';                     // Databas lösenord

try {
    // Skapa en PDO-anslutning till databasen med UTF-8 teckenkodning
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    // Ställ in PDO att kasta undantag vid fel, vilket underlättar felsökning
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Skriv felmeddelandet i serverns loggfil för administratören
    error_log("Database connection failed: " . $e->getMessage());
    
    // Visa ett generiskt felmeddelande till användaren utan tekniska detaljer
    die("Databasanslutning misslyckades. Försök igen senare.");
}
?>
