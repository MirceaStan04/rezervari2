-- ============================================================
-- Migrare: adauga poze la locatiile existente + locatii noi
-- Ruleaza o singura data pe instalatii existente.
-- ============================================================

-- Actualizare poze locatii existente (id 1-4)
UPDATE locatii SET poza = 'assets/img/locatii/sala-thalia.jpg'
    WHERE id_locatie = 1 AND (poza IS NULL OR poza = '');

UPDATE locatii SET poza = 'assets/img/locatii/filarmonica.jpg'
    WHERE id_locatie = 2 AND (poza IS NULL OR poza = '');

UPDATE locatii SET poza = 'assets/img/locatii/piata-mica.jpg'
    WHERE id_locatie = 3 AND (poza IS NULL OR poza = '');

UPDATE locatii SET poza = 'assets/img/locatii/turnul-sfatului.jpg'
    WHERE id_locatie = 4 AND (poza IS NULL OR poza = '');

-- Locatii noi
INSERT INTO locatii (nume_locatie, adresa, capacitate, poza) VALUES
    ('Piata Mare Sibiu',
     'Piata Mare 1, Sibiu',
     5000,
     'assets/img/locatii/piata-mare.jpg'),

    ('Muzeul National Brukenthal',
     'Piata Mare 4-5, Sibiu',
     200,
     'assets/img/locatii/brukenthal.jpg'),

    ('Parcul Sub Arini',
     'Str. Armata Romana 1, Sibiu',
     3000,
     'assets/img/locatii/sub-arini.jpg'),

    ('Zona Turnul Sfatului – Piata Mica',
     'Piata Mica 1, Sibiu',
     800,
     'assets/img/locatii/piata-mica.jpg');
