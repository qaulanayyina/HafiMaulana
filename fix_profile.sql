-- Cek apakah kolom foto_profil sudah ada
SET @exist := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'db_sembako'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'foto_profil'
);

-- Tambah kolom jika belum ada
SET @query := IF(
    @exist = 0,
    'ALTER TABLE users ADD COLUMN foto_profil VARCHAR(255) DEFAULT NULL AFTER no_telp',
    'SELECT "Column already exists"'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 