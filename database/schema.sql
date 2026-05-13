-- ============================================================
-- Crimson Reserve – Schema PostgreSQL
-- Ruleaza intr-o baza de date goala: CREATE DATABASE licenta;
-- ============================================================

-- Utilizatori (clienti, organizatori, administratori)
CREATE TABLE IF NOT EXISTS utilizatori (
    id_utilizator   SERIAL       PRIMARY KEY,
    nume            VARCHAR(120) NOT NULL,
    email           VARCHAR(180) NOT NULL UNIQUE,
    parola          VARCHAR(255) NOT NULL,
    rol             VARCHAR(30)  NOT NULL DEFAULT 'client',
    data_creare     DATE         NOT NULL DEFAULT CURRENT_DATE
);

-- Locatii disponibile pentru evenimente
CREATE TABLE IF NOT EXISTS locatii (
    id_locatie    SERIAL       PRIMARY KEY,
    nume_locatie  VARCHAR(160) NOT NULL,
    adresa        VARCHAR(255) NOT NULL,
    capacitate    INT          NOT NULL DEFAULT 100,
    poza          VARCHAR(500)
);

-- Evenimente publicate (create direct sau auto-create la aprobarea unei cereri)
CREATE TABLE IF NOT EXISTS evenimente (
    id_eveniment    SERIAL         PRIMARY KEY,
    titlu           VARCHAR(200)   NOT NULL,
    descriere       TEXT,
    data_eveniment  DATE           NOT NULL,
    ora_inceput     TIME           NOT NULL,
    ora_sfarsit     TIME           NOT NULL,
    pret            NUMERIC(10,2)  NOT NULL DEFAULT 0,
    capacitate      INT            NOT NULL DEFAULT 100,
    id_locatie      INT            NOT NULL REFERENCES locatii(id_locatie) ON DELETE RESTRICT
);

-- Inscrieri ale clientilor la evenimente
CREATE TABLE IF NOT EXISTS rezervari (
    id_rezervare    SERIAL      PRIMARY KEY,
    id_utilizator   INT         NOT NULL REFERENCES utilizatori(id_utilizator) ON DELETE CASCADE,
    id_eveniment    INT         NOT NULL REFERENCES evenimente(id_eveniment)   ON DELETE CASCADE,
    numar_locuri    INT         NOT NULL DEFAULT 1,
    data_rezervare  DATE        NOT NULL DEFAULT CURRENT_DATE,
    status          VARCHAR(30) NOT NULL DEFAULT 'in_asteptare'
);

-- Cereri de eveniment trimise de organizatori (asteapta aprobare admin)
CREATE TABLE IF NOT EXISTS cereri_evenimente (
    id_cerere            SERIAL         PRIMARY KEY,
    id_organizator       INT            NOT NULL REFERENCES utilizatori(id_utilizator) ON DELETE CASCADE,
    id_locatie           INT            NOT NULL REFERENCES locatii(id_locatie)        ON DELETE RESTRICT,
    titlu                VARCHAR(200)   NOT NULL,
    descriere            TEXT,
    data_eveniment       DATE           NOT NULL,
    ora_inceput          TIME           NOT NULL,
    ora_sfarsit          TIME           NOT NULL,
    pret                 NUMERIC(10,2)  NOT NULL DEFAULT 0,
    capacitate_estimata  INT            NOT NULL DEFAULT 100,
    status               VARCHAR(30)    NOT NULL DEFAULT 'in_asteptare',
    id_eveniment_creat   INT            REFERENCES evenimente(id_eveniment) ON DELETE SET NULL,
    created_at           TIMESTAMP      NOT NULL DEFAULT NOW(),
    updated_at           TIMESTAMP      NOT NULL DEFAULT NOW()
);

-- ============================================================
-- Upgrade pentru instalatii existente (fara coloana capacitate):
-- ALTER TABLE evenimente ADD COLUMN IF NOT EXISTS capacitate INT NOT NULL DEFAULT 100;
-- ============================================================


-- ============================================================
-- Date de test
-- Parola pentru toti utilizatorii demo: password
-- Hash bcrypt generat cu PASSWORD_BCRYPT (cost 10)
-- ============================================================

INSERT INTO utilizatori (nume, email, parola, rol) VALUES
    ('Administrator',   'admin@crimson.ro',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJDNTbR', 'admin'),
    ('Organizator Demo','org@crimson.ro',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJDNTbR', 'organizator'),
    ('Client Demo',     'client@crimson.ro',
     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJDNTbR', 'client');

INSERT INTO locatii (nume_locatie, adresa, capacitate) VALUES
    ('Sala Thalia',                       'Str. Papiu Ilarian 2, Sibiu',      280),
    ('Filarmonica de Stat Sibiu',         'B-dul Corneliu Coposu 2, Sibiu',   450),
    ('Casa de Cultura a Sindicatelor',    'Str. Unirii 20, Sibiu',            320),
    ('Centrul Cultural Ion Besoiu',       'Str. Mitropoliei 33, Sibiu',       180);

INSERT INTO evenimente (titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, capacitate, id_locatie) VALUES
    ('Conferinta Tech Sibiu 2026',
     'Editia anuala a conferintei de tehnologie din Sibiu. Speakeri din industrie, workshopuri practice si networking cu profesionisti din domeniu.',
     '2026-06-15', '09:00', '18:00', 150.00, 200, 1),
    ('Concert Simfonic – Beethoven',
     'Orchestra Filarmonicii de Stat Sibiu prezinta lucrarile lui Ludwig van Beethoven: Simfonia nr. 5 si Simfonia nr. 9, Coral.',
     '2026-06-28', '19:30', '22:00', 80.00, 400, 2),
    ('Festival de Film Independent',
     'Trei zile dedicate cinematografiei independente din Romania si din lume. Proiectii, dezbateri si intalniri cu regizorii.',
     '2026-07-10', '10:00', '23:00', 50.00, 250, 3),
    ('Workshop UX/UI Design',
     'Atelier practic de design de interfete si experienta utilizatorului. Locuri limitate – inscriere obligatorie.',
     '2026-07-22', '10:00', '17:00', 200.00, 30, 4);
