# Panduan Instalasi Database Toko Sembako Online

## Struktur Database

Database toko sembako online terdiri dari beberapa tabel utama:

1. `users` - Menyimpan data pengguna (admin dan customer)
2. `kategori` - Menyimpan data kategori produk
3. `produk` - Menyimpan data produk sembako
4. `keranjang` - Menyimpan data keranjang belanja
5. `pesanan` - Menyimpan data pesanan
6. `detail_pesanan` - Menyimpan detail item dalam pesanan

## Langkah Instalasi

1. Buat database baru dengan nama `db_sembako`:

```sql
CREATE DATABASE db_sembako;
```

2. Import struktur database dari file `db_sembako.sql`:

```bash
mysql -u root -p db_sembako < db_sembako.sql
```

3. Import data awal (seeder) dari file `migrations/seeder.sql`:

```bash
mysql -u root -p db_sembako < migrations/seeder.sql
```

## Akun Default

### Admin

- Username: admin
- Password: password

### Customer

1. Customer 1

   - Username: customer1
   - Password: password

2. Customer 2

   - Username: customer2
   - Password: password

3. Customer 3
   - Username: customer3
   - Password: password

## Catatan

- Pastikan MySQL/MariaDB sudah terinstall dan berjalan
- Sesuaikan username dan password database di file `config/database.php`
- Password default untuk semua akun adalah: `password`
- Folder `uploads` harus memiliki permission yang sesuai untuk menyimpan gambar produk
