-- Skapa en ny databas som heter film_bibliotek
-- Använd teckenuppsättningen utf8mb4 för att stödja alla Unicode-tecken inklusive emojis
CREATE DATABASE film_bibliotek CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; 

-- Välj att använda databasen film_bibliotek för alla följande operationer
USE film_bibliotek;

-- Skapa tabellen 'kategorier' som innehåller filmtitlar kategorier
CREATE TABLE kategorier (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- Unikt ID för varje kategori, autoökande
    namn VARCHAR(50) NOT NULL             -- Namn på kategorin, max 50 tecken, får inte vara tomt
);

-- Skapa tabellen 'filmer' som lagrar information om filmer
CREATE TABLE filmer (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- Unikt ID för varje film, autoökande
    titel VARCHAR(100) NOT NULL,          -- Filmens titel, max 100 tecken, får inte vara tomt
    regissor VARCHAR(100) NOT NULL,       -- Regissörens namn, max 100 tecken, får inte vara tomt
    ar INT NOT NULL,                      -- Produktionsår som ett heltal, får inte vara tomt
    kategori_id INT NOT NULL,             -- Referens till kategori via kategoriens ID
    FOREIGN KEY (kategori_id) REFERENCES kategorier(id)  -- Skapar en relation mellan filmer och kategorier
);

-- Lägg till några exempel på kategorier i tabellen 'kategorier'
INSERT INTO kategorier (namn) VALUES
('Thriller'),
('Romantic'),
('Swedish'),
('Animated'),
('Comedy');
