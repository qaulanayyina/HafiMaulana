-- Mengisi data kategori
INSERT INTO kategori (nama_kategori) VALUES 
('Beras'),
('Minyak Goreng'),
('Gula'),
('Telur'),
('Tepung'),
('Mie Instan'),
('Bumbu Dapur'),
('Minuman'),
('Snack'),
('Kebutuhan Pokok Lainnya');

-- Mengisi data produk
INSERT INTO produk (kategori_id, nama_produk, deskripsi, harga, stok, gambar) VALUES
-- Kategori Beras
(1, 'Beras Pandan Wangi', 'Beras premium kualitas terbaik, tekstur pulen dan wangi', 75000, 100, NULL),
(1, 'Beras IR64', 'Beras medium kualitas bagus, cocok untuk konsumsi harian', 65000, 150, NULL),
(1, 'Beras Merah', 'Beras merah organik kaya serat dan vitamin', 85000, 50, NULL),
(1, 'Beras Ketan', 'Beras ketan putih untuk pembuatan kue tradisional', 25000, 80, NULL),
(1, 'Beras Hitam', 'Beras hitam organik kaya antioksidan', 95000, 30, NULL),

-- Kategori Minyak Goreng
(2, 'Minyak Goreng Bimoli', 'Minyak goreng kemasan 2L, kualitas premium', 35000, 80, NULL),
(2, 'Minyak Goreng Tropical', 'Minyak goreng kemasan 1L, hemat dan berkualitas', 18000, 100, NULL),
(2, 'Minyak Goreng Sania', 'Minyak goreng kemasan 2L, hasil penyaringan 3x', 32000, 75, NULL),
(2, 'Minyak Goreng Filma', 'Minyak goreng kemasan 5L, cocok untuk usaha', 78000, 40, NULL),
(2, 'Minyak Goreng Fortune', 'Minyak goreng kemasan 1L, non kolesterol', 20000, 90, NULL),

-- Kategori Gula
(3, 'Gula Pasir Putih', 'Gula pasir putih kemasan 1kg, kualitas premium', 15000, 200, NULL),
(3, 'Gula Merah', 'Gula merah asli kemasan 500g, dari nira kelapa', 12000, 100, NULL),
(3, 'Gula Aren', 'Gula aren murni kemasan 500g, manis alami', 20000, 50, NULL),
(3, 'Gula Pasir Kuning', 'Gula pasir kuning alami 1kg', 16000, 150, NULL),
(3, 'Gula Diet', 'Gula rendah kalori kemasan 500g', 35000, 30, NULL),

-- Kategori Telur
(4, 'Telur Ayam Negeri', 'Telur ayam negeri segar per kg', 25000, 100, NULL),
(4, 'Telur Ayam Kampung', 'Telur ayam kampung segar per kg', 45000, 50, NULL),
(4, 'Telur Bebek', 'Telur bebek segar per kg', 35000, 50, NULL),
(4, 'Telur Puyuh', 'Telur puyuh per pack isi 30 butir', 15000, 75, NULL),
(4, 'Telur Asin', 'Telur asin bebek per butir', 5000, 100, NULL),

-- Kategori Tepung
(5, 'Tepung Terigu Segitiga Biru', 'Tepung terigu protein sedang 1kg', 12000, 150, NULL),
(5, 'Tepung Terigu Cakra Kembar', 'Tepung terigu protein tinggi 1kg', 15000, 100, NULL),
(5, 'Tepung Beras Rose Brand', 'Tepung beras kemasan 500g', 8000, 100, NULL),
(5, 'Tepung Tapioka', 'Tepung tapioka kemasan 500g', 7000, 100, NULL),
(5, 'Tepung Sagu', 'Tepung sagu murni 500g', 10000, 80, NULL),

-- Kategori Mie Instan
(6, 'Indomie Goreng', 'Mie instan goreng per dus isi 40 pcs', 120000, 50, NULL),
(6, 'Indomie Soto', 'Mie instan kuah soto per dus isi 40 pcs', 115000, 45, NULL),
(6, 'Mie Sedaap Goreng', 'Mie instan goreng per dus isi 40 pcs', 118000, 50, NULL),
(6, 'Mie Sedaap Korea', 'Mie instan goreng korea per dus isi 40 pcs', 125000, 40, NULL),
(6, 'Sarimi Isi 2', 'Mie instan isi 2 per dus isi 30 pcs', 125000, 40, NULL),

-- Kategori Bumbu Dapur
(7, 'Merica Bubuk Ladaku', 'Merica bubuk kemasan 100g', 10000, 100, NULL),
(7, 'Ketumbar Bubuk Koepoe', 'Ketumbar bubuk kemasan 100g', 8000, 100, NULL),
(7, 'Bawang Putih', 'Bawang putih segar per kg', 35000, 50, NULL),
(7, 'Bawang Merah', 'Bawang merah segar per kg', 40000, 50, NULL),
(7, 'Cabai Merah', 'Cabai merah segar per kg', 50000, 30, NULL),
(7, 'Cabai Rawit', 'Cabai rawit segar per kg', 60000, 30, NULL),
(7, 'Lengkuas', 'Lengkuas segar per kg', 15000, 40, NULL),
(7, 'Jahe', 'Jahe segar per kg', 20000, 40, NULL),
(7, 'Kunyit', 'Kunyit segar per kg', 15000, 40, NULL),
(7, 'Sereh', 'Sereh segar per ikat', 5000, 50, NULL),

-- Kategori Minuman
(8, 'Teh Celup Sariwangi', 'Teh celup isi 25 sachet', 8000, 100, NULL),
(8, 'Teh Celup Sosro', 'Teh celup isi 30 sachet', 10000, 100, NULL),
(8, 'Kopi Kapal Api', 'Kopi bubuk kemasan 250g', 15000, 100, NULL),
(8, 'Kopi ABC', 'Kopi bubuk kemasan 200g', 13000, 100, NULL),
(8, 'Milo', 'Susu coklat bubuk kemasan 300g', 25000, 75, NULL),
(8, 'Dancow', 'Susu bubuk full cream 400g', 45000, 50, NULL),
(8, 'Sirup Marjan', 'Sirup rasa cocopandan 460ml', 20000, 60, NULL),
(8, 'Sirup ABC', 'Sirup rasa melon 460ml', 18000, 60, NULL),

-- Kategori Snack
(9, 'Chitato', 'Keripik kentang rasa original 100g', 10000, 100, NULL),
(9, 'Lays', 'Keripik kentang rasa rumput laut 100g', 10000, 100, NULL),
(9, 'Taro', 'Snack net rasa balado 100g', 9000, 100, NULL),
(9, 'Oreo', 'Biskuit sandwich coklat 137g', 8000, 150, NULL),
(9, 'Roma Malkist', 'Biskuit crackers 250g', 12000, 100, NULL),
(9, 'Good Time', 'Biskuit cookies coklat 72g', 8000, 100, NULL),

-- Kategori Kebutuhan Pokok Lainnya
(10, 'Garam Cap Kapal', 'Garam beryodium 500g', 5000, 200, NULL),
(10, 'Royco Ayam', 'Penyedap rasa ayam 100g', 5000, 150, NULL),
(10, 'Masako Sapi', 'Penyedap rasa sapi 100g', 5000, 150, NULL),
(10, 'Kecap Bango', 'Kecap manis 600ml', 25000, 100, NULL),
(10, 'Saos ABC', 'Saos sambal 340ml', 15000, 100, NULL),
(10, 'Terasi ABC', 'Terasi udang 200g', 8000, 100, NULL),
(10, 'Santan Kara', 'Santan instan 200ml', 7000, 150, NULL),
(10, 'Susu Kental Manis', 'Susu kental manis 370g', 12000, 100, NULL);

-- Mengisi data admin default
INSERT INTO users (username, password, nama_lengkap, alamat, no_telp, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'Jl. Admin No. 1', '08123456789', 'admin');

-- Mengisi data customer contoh
INSERT INTO users (username, password, nama_lengkap, alamat, no_telp, role) VALUES
('customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Customer Satu', 'Jl. Customer No. 1', '08111111111', 'customer'),
('customer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Customer Dua', 'Jl. Customer No. 2', '08222222222', 'customer'),
('customer3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Customer Tiga', 'Jl. Customer No. 3', '08333333333', 'customer');

-- Mengisi data pesanan contoh
INSERT INTO pesanan (user_id, total_harga, status, alamat_pengiriman, created_at) VALUES
(2, 150000, 'selesai', 'Jl. Customer No. 1', '2024-01-01 10:00:00'),
(3, 85000, 'dikirim', 'Jl. Customer No. 2', '2024-01-02 11:00:00'),
(4, 225000, 'diproses', 'Jl. Customer No. 3', '2024-01-03 12:00:00');

-- Mengisi data detail pesanan contoh
INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga) VALUES
(1, 1, 2, 75000), -- 2 Beras Pandan Wangi
(2, 4, 5, 17000), -- 5 Minyak Goreng
(3, 7, 15, 15000); -- 15 Gula Pasir 