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
    saldo INT(11) DEFAULT 1500,
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

USE belmopoly;

CREATE TABLE probabilita (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             descrizione TEXT NOT NULL,
                             incasso INT
);

CREATE TABLE imprevisti (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            descrizione TEXT NOT NULL,
                            perdita INT
);

INSERT INTO probabilita (descrizione, incasso) VALUES
                                                   ('Hackeri un conto aziendale e trovi un credito non registrato. Guadagni 250€.', 250),
                                                   ('I tuoi investimenti nel mercato finanziario vanno bene. Guadagni 300€.', 300),
                                                   ('Vinci un round in un’arena da combattimento clandestina. Guadagni 180€.', 180),
                                                   ('Andate fino a Datacube Matrix. Ritirando 250€', 250),
                                                   ('Un’IA segreta ti invia una transazione anonima. Guadagni 280€.', 280),
                                                   ('Riesci a vendere dati rubati sul dark web. Guadagni 220€.', 220),
                                                   ('Un’azienda sperimenta un nuovo impianto neurale su di te. Ricevi un biglietto di uscita gratis dal Virtual Lock', NULL),
                                                   ('Un dirigente corrotto ti paga per far sparire alcune prove digitali. Incassa 250€.', 250),
                                                   ('Cloni una firma digitale di un dirigente di alto livello. Accedi a fondi aziendali e ricevi 270€.', 270),
                                                   ('Un virus sovrascrive i tuoi debiti digitali. Guadagni 200€.', 200);

INSERT INTO imprevisti (descrizione, perdita) VALUES
                                                  ('Il tuo hardware si rompe improvvisamente. Riparazione obbligatoria, paga 150€', 150),
                                                  ('Il mercato finanziario crolla, i tuoi investimenti vanno in fumo. Perdi 320€.', 320),
                                                  ('Sei vittima di un attacco ransomware. Paga 200€ per sbloccare i tuoi dati.', 200),
                                                  ('Il tuo impianto neurale subisce un sovraccarico. Paga 250€ per le riparazioni.', 250),
                                                  ('Sei stato tracciato da un’AI di sorveglianza. Torna indietro di 6 caselle per nasconderti.', NULL),
                                                  ('Un virus si diffonde nei tuoi sistemi e devi resettare tutto. Perdi un turno.', NULL),
                                                  ('Un hacker ruba i tuoi dati bancari e svuota il tuo conto. Perdi 370€.', 370),
                                                  ('La polizia ti trova un chip illegale nel tuo sistema. Paga 300€ di multa.', 300),
                                                  ('I tuoi dati personali vengono venduti sul mercato nero. Perdi 175€.', 175),
                                                  ('Il tuo portafoglio digitale subisce un attacco di phishing sofisticato. Perdi 210€ a causa del furto.', 210);


CREATE TABLE proprietaSpeciali (
                                   id INT PRIMARY KEY,
                                   nome VARCHAR(255) NOT NULL,
                                   prezzo INT NOT NULL,
                                   affitto1 INT NOT NULL,
                                   affitto2 INT NOT NULL,
                                   affitto3 INT ,
                                   affitto4 INT
);

CREATE TABLE proprietaNormali (
                                  id INT PRIMARY KEY,
                                  nome VARCHAR(255) NOT NULL,
                                  prezzo INT NOT NULL,
                                  affitto INT NOT NULL,
                                  affittoCompleto INT NOT NULL,
                                  affittoCasa1 INT NOT NULL,
                                  affittoCasa2 INT NOT NULL,
                                  affittoCasa3 INT NOT NULL,
                                  affittoCasa4 INT NOT NULL,
                                  affittoAlbergo INT NOT NULL,
                                  costoCasa INT NOT NULL,
                                  costoAlbergo INT NOT NULL
);

INSERT INTO proprietaSpeciali VALUES
                                  (5, 'Cyber Station South', 200, 25, 50, 100, 200),
                                  (15, 'Cyber Station West', 200, 25, 50, 100, 200),
                                  (25, 'Cyber Station North', 200, 25, 50, 100, 200),
                                  (35, 'Cyber Station Est', 200, 25, 50, 100, 200),
                                  (12, 'Holo Company', 150, 50, 150, NULL, NULL),
                                  (28, 'Nano Company', 150, 50, 150, NULL, NULL);


INSERT INTO proprietaNormali VALUES
                                 (1, 'Pixel Street', 60, 2, 4, 10, 30, 90, 160, 250, 50, 50),
                                 (3, 'Pixel Park', 60, 4, 8, 20, 60, 180, 320, 450, 50, 50),

                                 (6, 'Neon District', 100, 6, 12, 30, 90, 270, 400, 550, 50, 50),
                                 (8, 'Neon Tower',    100, 6, 12, 30, 90, 270, 400, 550, 50, 50),
                                 (9, 'Neon Core',     120, 8, 16, 40, 100, 300, 450, 600, 50, 50),

                                 (11, 'Pixel Street', 140, 10, 20, 50, 150, 450, 625, 750, 100, 100),
                                 (13, 'Pixel Street', 140, 10, 20, 50, 150, 450, 625, 750, 100, 100),
                                 (14, 'Pixel Street', 160, 12, 24, 60, 180, 500, 700, 900, 100, 100),

                                 (16, 'Techno Bridge', 180, 14, 28, 70, 200, 550, 750, 950, 100, 100),
                                 (18, 'Techno Street', 180, 14, 28, 70, 200, 550, 750, 950, 100, 100),
                                 (19, 'Techno Factroy', 200, 16, 32, 80, 220, 600, 800, 1000, 100, 100),

                                 (21, 'Neural Sector', 220, 18, 36, 90, 250, 700, 875, 1050, 150, 150),
                                 (23, 'Neural Nexus', 220, 18, 36, 90, 250, 700, 875, 1050, 150, 150),
                                 (24, 'Neural Horizon', 200, 20, 40, 100, 300, 750, 925, 1100, 150, 150),

                                 (26, 'Datacube Network', 260, 22, 44, 110, 330, 800, 975, 1150, 150, 150),
                                 (27, 'Datacube Lab', 260, 22, 44, 110, 330, 800, 975, 1150, 150, 150),
                                 (29, 'Datacube Matrix', 280, 24, 48, 120, 360, 850, 1025, 1200, 150, 150),

                                 (31, 'Neural Plaza', 300, 26, 52, 130, 930, 900, 1100, 1275, 200, 200),
                                 (32, 'Fusion Plaza', 300, 26, 52, 130, 930, 900, 1100, 1275, 200, 200),
                                 (34, 'Zero Gravity Plaza', 320, 28, 56, 150, 450, 1000, 1200, 1400, 200, 200),

                                 (37, 'Megacity Plaza', 350, 35, 70, 175, 500, 1100, 1300, 1500, 200, 200),
                                 (39, 'Cybercore Plaza', 400, 50, 100, 200, 600, 1400, 1700, 2000, 200, 200);


