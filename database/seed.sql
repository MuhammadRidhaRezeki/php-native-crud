-- php-native-crud — seed data (import AFTER schema.sql)
-- 5 sample students so the list view is not empty on first run.

USE php_native_crud;

INSERT INTO students (nim, name, email, major, enrollment_year) VALUES
    ('202301001', 'Aisyah Rahma',  'aisyah.rahma@example.com',  'Informatics Engineering', 2023),
    ('202301002', 'Budi Santoso',  'budi.santoso@example.com',  'Informatics Engineering', 2023),
    ('202202015', 'Citra Lestari', 'citra.lestari@example.com', 'Information Systems',    2022),
    ('202402003', 'Dimas Pratama', 'dimas.pratama@example.com', 'Computer Engineering',   2024),
    ('202102008', 'Eka Putri',     'eka.putri@example.com',     'Informatics Engineering', 2021)
ON DUPLICATE KEY UPDATE name = VALUES(name);
