-- Membuat database
CREATE DATABASE IF NOT EXISTS db_sembako;
USE db_sembako;

-- Tabel users
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

-- Tabel kategori
CREATE TABLE kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL
);

-- Tabel produk
CREATE TABLE produk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kategori_id INT,
    nama_produk VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
);

-- Tabel keranjang
CREATE TABLE keranjang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    produk_id INT,
    jumlah INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- Tabel pesanan
CREATE TABLE pesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'dibayar', 'diproses', 'dikirim', 'selesai') DEFAULT 'pending',
    alamat_pengiriman TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel detail_pesanan
CREATE TABLE detail_pesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pesanan_id INT,
    produk_id INT,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- Insert data admin default
INSERT INTO users (username, password, nama_lengkap, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert kategori default
INSERT INTO kategori (nama_kategori) VALUES 
('Beras'),
('Minyak Goreng'),
('Gula'),
('Telur'),
('Tepung'),
('Mie Instan'),
('Bumbu Dapur'),
('Minuman'); 