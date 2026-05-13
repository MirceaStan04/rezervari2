-- Curata evenimente duplicate/test
DELETE FROM rezervari WHERE id_eveniment IN (4,5,6,7,8,9,10,11,13,14);
DELETE FROM cereri_evenimente WHERE id_eveniment_creat IN (4,5,6,7,8,9,10,11,13,14);
DELETE FROM evenimente WHERE id_eveniment IN (4,5,6,7,8,9,10,11,13,14);

-- Evenimente noi pe toate locatiile (toate in viitor)
INSERT INTO evenimente (titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, capacitate, id_locatie) VALUES
('Concert Simfonic - Beethoven',
 'Orchestra Filarmonicii de Stat Sibiu interpreteaza Simfonia nr. 5 si Simfonia nr. 9 Coral. Un concert de referinta pentru iubitorii muzicii clasice.',
 '2026-05-24', '19:30', '22:00', 80.00, 400, 6),

('Festival de Film Independent',
 'Trei zile dedicate cinematografiei independente romanesti si internationale. Proiectii, dezbateri si intalniri cu regizorii.',
 '2026-05-30', '10:00', '23:00', 40.00, 250, 1),

('Conferinta Tech Sibiu 2026',
 'Editia anuala a conferintei de tehnologie din Sibiu. Speakeri din industrie, workshopuri practice si networking.',
 '2026-06-06', '09:00', '18:00', 150.00, 200, 1),

('Piata Mare Live - Concert in aer liber',
 'Concert gratuit de muzica folk si acustica in inima Sibiului. Artisti locali si invitati speciali pe scena.',
 '2026-06-14', '18:00', '22:30', 0.00, 3000, 5),

('Expozitie Brukenthal - Arta Secolului XIX',
 'Vernisaj si tur ghidat al noii expozitii temporare. Colectia include lucrari de patrimoniu din secolul XIX. Locuri limitate.',
 '2026-06-20', '11:00', '14:00', 25.00, 80, 3),

('Workshop Fotografie Urbana',
 'Atelier practic de fotografie pe strazile Sibiului medieval. Explorezi Piata Mica cu un fotograf profesionist.',
 '2026-06-27', '10:00', '16:00', 120.00, 20, 8),

('Picnic Cultural - Parcul Sub Arini',
 'Zi de activitati culturale in aer liber: lecturi publice, muzica live si ateliere pentru copii. Intrare libera.',
 '2026-07-05', '11:00', '20:00', 0.00, 2000, 7),

('Spectacol de Teatru - O Scrisoare Pierduta',
 'Regia moderna a clasicului lui Caragiale. Spectacol complet in limba romana, cu trupa Centrului Cultural Ion Besoiu.',
 '2026-07-12', '19:00', '21:30', 60.00, 160, 4),

('Seara Jazz la Turnul Sfatului',
 'Concert de jazz in aer liber, in Piata Mica. Trio cu influente romanesti si internationale, atmosfera de neuitat.',
 '2026-07-19', '20:00', '23:00', 35.00, 600, 8),

('Conferinta Patrimoniul Sibian',
 'Specialisti in arhitectura si restaurare prezinta proiectele de conservare a centrului istoric UNESCO al Sibiului.',
 '2026-07-25', '10:00', '17:00', 50.00, 180, 4),

('Concert Rock Simfonic',
 'O fuziune intre muzica rock si orchestra simfonica. Piese clasice ale rock-ului mondial reinterpretate cu instrumente clasice.',
 '2026-08-08', '20:00', '23:00', 90.00, 280, 1),

('Targ Mestesugaresc - Piata Mare',
 'Editia de vara a targului traditional. Mestesugari din toata Transilvania isi expun produsele handmade. Acces liber.',
 '2026-08-15', '09:00', '20:00', 0.00, 5000, 5);
