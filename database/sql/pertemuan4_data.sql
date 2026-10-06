USE praktikum_web_2401020040;

INSERT INTO program_studi (nama_prodi) VALUES
    ('Teknik Elektro'),
    ('Teknik Pertambangan');

INSERT INTO mahasiswa
    (nim, nama, email, usia, program_studi_id)
VALUES
    ('2401020010', 'Fachrezi Bachri',
     'rezi@example.com', 20, 1),
    ('2401020019', 'Willy Hadipermana',
     'willy@example.com', 19, 2),
    ('2401020040', 'Muhammad Faiz',
     'cukip@example.com', 21, 2),
    ('2401020099', 'Data Sementara',
     'sementara@example.com', 18, 1);

UPDATE mahasiswa
SET email = 'muhammad.faiz@example.com'
WHERE nim = '2401020040';

DELETE FROM mahasiswa
WHERE nim = '2401020099';

SELECT m.nim, m.nama, m.email, m.usia,
       p.nama_prodi
FROM mahasiswa AS m
JOIN program_studi AS p
    ON p.id = m.program_studi_id
ORDER BY m.nim;
