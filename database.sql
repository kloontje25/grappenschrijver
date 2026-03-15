-- ============================================================
-- GRAPPENDATABASE - SQL om de database aan te maken
-- Voer dit script uit in phpMyAdmin of via de MySQL terminal
-- ============================================================

-- Maak de database aan (als die nog niet bestaat)
CREATE DATABASE IF NOT EXISTS mbscvutw_grappenmaker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Gebruik deze database
USE mbscvutw_grappenmaker;


-- ------------------------------------------------------------
-- TABEL 1: grappen
-- Slaat elke grap op met een uniek ID, de tekst en de datum.
-- goedgekeurd = 0 → wacht op goedkeuring (niet zichtbaar op de site)
-- goedgekeurd = 1 → goedgekeurd door beheerder (zichtbaar op de site)
-- ------------------------------------------------------------
CREATE TABLE grappen (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tekst       TEXT        NOT NULL,
    datum       DATETIME    DEFAULT CURRENT_TIMESTAMP,
    goedgekeurd TINYINT(1)  DEFAULT 0  -- 0 = wachtend, 1 = goedgekeurd
);


-- ------------------------------------------------------------
-- TABEL 2: categorieen
-- Slaat unieke categorienamen op (bijv. "Dieren", "School").
-- ------------------------------------------------------------
CREATE TABLE categorieen (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL UNIQUE  -- UNIQUE: geen dubbele namen
);


-- ------------------------------------------------------------
-- TABEL 3: grap_categorie  (KOPPELTABEL)
--
-- Een grap kan meerdere categorieën hebben.
-- Een categorie kan bij meerdere grappen horen.
-- Dit heet een "many-to-many" relatie.
--
-- Voorbeeld:
--   Grap 1 → Categorie "Dieren" en "School"
--   Grap 2 → Categorie "Dieren"
--
-- De koppeltabel bevat dan de rijen:
--   (1, dieren_id), (1, school_id), (2, dieren_id)
-- ------------------------------------------------------------
CREATE TABLE grap_categorie (
    grap_id      INT NOT NULL,
    categorie_id INT NOT NULL,
    PRIMARY KEY (grap_id, categorie_id),  -- Combinatie is altijd uniek
    FOREIGN KEY (grap_id)      REFERENCES grappen(id)    ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categorieen(id) ON DELETE CASCADE
    -- ON DELETE CASCADE: als een grap wordt verwijderd,
    -- worden de koppelingen automatisch ook verwijderd.
);


-- ------------------------------------------------------------
-- TABEL 4: admins
--
-- Beheerdersaccounts. Maak een account aan via wachtwoord.php
-- (of zie onderaan dit bestand voor een voorbeeld-INSERT).
-- Het wachtwoord wordt NOOIT als leesbare tekst opgeslagen;
-- alleen de bcrypt-hash staat in de database.
-- ------------------------------------------------------------
CREATE TABLE admins (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    gebruikersnaam VARCHAR(50)  NOT NULL UNIQUE,
    wachtwoord     VARCHAR(255) NOT NULL  -- Bcrypt-hash, GEEN leesbaar wachtwoord
);


-- ------------------------------------------------------------
-- Voorbeeldcategorieën om mee te starten
-- ------------------------------------------------------------
INSERT INTO categorieen (naam) VALUES
    ('Dieren'),
    ('School'),
    ('Familie'),
    ('Sport'),
    ('Computers');


-- ------------------------------------------------------------
-- Een voorbeeldgrap om te testen (goedgekeurd = 1 → meteen zichtbaar)
-- ------------------------------------------------------------
INSERT INTO grappen (tekst, goedgekeurd) VALUES
    ('Waarom kunnen geesten zo slecht liegen?\nOmdat je dwars door ze heen kijkt!', 1);

-- Koppel de voorbeeldgrap aan categorie "Dieren" (id=1) en "School" (id=2)
INSERT INTO grap_categorie (grap_id, categorie_id) VALUES (1, 1), (1, 2);


-- ------------------------------------------------------------
-- Admin-account aanmaken
--
-- STAP 1: Open wachtwoord.php in de browser en genereer een hash.
-- STAP 2: Vervang HASH_HIER hieronder door de gekopieerde hash.
-- STAP 3: Voer deze INSERT uit in phpMyAdmin (tabblad SQL).
-- STAP 4: Verwijder wachtwoord.php van de server!
-- ------------------------------------------------------------
-- INSERT INTO admins (gebruikersnaam, wachtwoord) VALUES ('admin', 'HASH_HIER');
