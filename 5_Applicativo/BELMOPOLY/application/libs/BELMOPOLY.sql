DROP schema BELMOPOLY;

-- Crea il database
CREATE DATABASE IF NOT EXISTS BELMOPOLY;
USE BELMOPOLY;

-- Tabella utenti
CREATE TABLE IF NOT EXISTS utente (
    id INT(11) NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    data_iscrizione DATE NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (email),
    UNIQUE KEY (username)
);

-- Tabella partita
CREATE TABLE IF NOT EXISTS partita (
    id INT(11) NOT NULL AUTO_INCREMENT,
    turno_player INT(11) NOT NULL,
    unique_key VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (unique_key)
);

-- Tabella fa_parte
CREATE TABLE IF NOT EXISTS fa_parte (
    utente_id INT(11) NOT NULL,
    partita_id INT(11) NOT NULL,
    capo_partita TINYINT(1) DEFAULT NULL,
    richiesta TINYINT(1) DEFAULT NULL,
    utente_prigione TINYINT(1) DEFAULT 0,
    posizione_pedina INT DEFAULT 0,
    room boolean DEFAULT true, 
    PRIMARY KEY (utente_id, partita_id),
    FOREIGN KEY (utente_id) REFERENCES utente(id),
    FOREIGN KEY (partita_id) REFERENCES partita(id)
);

-- Tabella amici
CREATE TABLE IF NOT EXISTS amico (
    mandante INT(11) NOT NULL,
    ricevente INT(11) NOT NULL,
    richiesta TINYINT(1) DEFAULT 0,
    PRIMARY KEY (mandante, ricevente),
    FOREIGN KEY (mandante) REFERENCES utente(id),
    FOREIGN KEY (ricevente) REFERENCES utente(id)
);

INSERT INTO utente (email, username, password, data_iscrizione) VALUES
('utente1@gmail.com', 'utente1', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente2@gmail.com', 'utente2', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente3@gmail.com', 'utente3', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente4@gmail.com', 'utente4', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente5@gmail.com', 'utente5', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente6@gmail.com', 'utente6', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente7@gmail.com', 'utente7', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente8@gmail.com', 'utente8', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente9@gmail.com', 'utente9', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE()),
('utente10@gmail.com', 'utente10', '$2y$10$rwe.xst3iEr8FZ7Ma5v8ouhXYvGcAne2M8rjGZv6fQNq/S/SJpo62', CURDATE());
