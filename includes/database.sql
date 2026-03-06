-- Grappenschrijver Database Schema

CREATE DATABASE IF NOT EXISTS grappenschrijver;
USE grappenschrijver;

-- Tabel voor grappen
CREATE TABLE jokes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    joke_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_hidden BOOLEAN DEFAULT FALSE,
    session_id VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel voor woordenlijst
CREATE TABLE words (
    id INT PRIMARY KEY AUTO_INCREMENT,
    word VARCHAR(255) NOT NULL UNIQUE,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel voor sessies (om gebruiker stappen bij te houden)
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    current_step INT DEFAULT 1,
    with_explanation BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel voor betekenissen per woord per sessie
CREATE TABLE meanings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255),
    word_id INT,
    meaning TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel voor associaties per betekenis
CREATE TABLE associations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meaning_id INT,
    association TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meaning_id) REFERENCES meanings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel voor grappen per sessie
CREATE TABLE session_jokes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255),
    word_id INT,
    joke_text TEXT NOT NULL,
    is_hidden BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Voorbeeldwoorden
INSERT INTO words (word, category) VALUES
('Giraffe', 'Dieren'),
('Paraplu', 'Voorwerpen'),
('Computer', 'Technologie'),
('Spaghetti', 'Voeding'),
('Trompet', 'Muziek'),
('Melkpack', 'Huishouden'),
('Krokodil', 'Dieren'),
('Totem', 'Cultuur'),
('Astronaut', 'Beroep'),
('Wasmachine', 'Apparaten');
