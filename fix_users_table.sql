USE db_sembako;

-- Backup existing users
CREATE TABLE users_backup AS SELECT * FROM users;

-- Drop existing tables that depend on users
DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS keranjang;
DROP TABLE IF EXISTS users;

-- Recreate users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_telp VARCHAR(15),
    foto_profil VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Restore data from backup
INSERT INTO users (id, username, password, nama_lengkap, alamat, no_telp, role, created_at)
SELECT id, username, password, nama_lengkap, alamat, no_telp, role, created_at
FROM users_backup;

-- Recreate dependent tables
CREATE TABLE keranjang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    produk_id INT,
    jumlah INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

CREATE TABLE pesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'dibayar', 'diproses', 'dikirim', 'selesai') DEFAULT 'pending',
    alamat_pengiriman TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE detail_pesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pesanan_id INT,
    produk_id INT,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
); 