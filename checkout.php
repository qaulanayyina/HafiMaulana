<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$query_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Ambil data keranjang
$query_keranjang = "SELECT k.*, p.nama_produk, p.harga, p.stok 
                    FROM keranjang k
                    JOIN produk p ON k.produk_id = p.id
                    WHERE k.user_id = $user_id";
$result_keranjang = mysqli_query($conn, $query_keranjang);

// Hitung total harga
$total_harga = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result_keranjang)) {
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_harga += $subtotal;
    $items[] = $row;
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $alamat_pengiriman = mysqli_real_escape_string($conn, $_POST['alamat_pengiriman']);
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert ke tabel pesanan
        $query_pesanan = "INSERT INTO pesanan (user_id, total_harga, alamat_pengiriman) 
                         VALUES ($user_id, $total_harga, '$alamat_pengiriman')";
        mysqli_query($conn, $query_pesanan);
        $pesanan_id = mysqli_insert_id($conn);
        
        // Insert ke tabel detail_pesanan dan update stok
        foreach ($items as $item) {
            $produk_id = $item['produk_id'];
            $jumlah = $item['jumlah'];
            $harga = $item['harga'];
            
            $query_detail = "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga) 
                            VALUES ($pesanan_id, $produk_id, $jumlah, $harga)";
            mysqli_query($conn, $query_detail);
            
            // Update stok
            $query_update_stok = "UPDATE produk 
                                 SET stok = stok - $jumlah 
                                 WHERE id = $produk_id";
            mysqli_query($conn, $query_update_stok);
        }
        
        // Hapus keranjang
        $query_hapus_keranjang = "DELETE FROM keranjang WHERE user_id = $user_id";
        mysqli_query($conn, $query_hapus_keranjang);
        
        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['success'] = "Pesanan berhasil dibuat!";
        header("Location: pesanan.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = "Terjadi kesalahan saat memproses pesanan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Sembako</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Sembako</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="keranjang.php">
                            <i class="bi bi-cart"></i> Keranjang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="bi bi-person"></i> <?php echo $_SESSION['username']; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <h2 class="mb-4">Checkout</h2>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (count($items) > 0) : ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item) : ?>
                                            <tr>
                                                <td><?php echo $item['nama_produk']; ?></td>
                                                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                <td><?php echo $item['jumlah']; ?></td>
                                                <td>Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" value="<?php echo $user['nama_lengkap']; ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Pengiriman</label>
                                    <textarea class="form-control" id="alamat" name="alamat_pengiriman" rows="3" required><?php echo $user['alamat']; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">No. Telepon</label>
                                    <input type="tel" class="form-control" id="telepon" value="<?php echo $user['no_telp']; ?>" readonly>
                                </div>
                                <button type="submit" name="checkout" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-check"></i> Buat Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">
                Keranjang belanja Anda kosong. <a href="index.php">Belanja sekarang</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3 text-dark">TOKO SEMBAKO<br>BUMP ALKAHFI</h5>
                    <p class="text-secondary">Menyediakan berbagai macam kebutuhan pokok dengan harga terjangkau dan kualitas terjamin.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3 text-dark">Kontak Kami</h5>
                    <ul class="list-unstyled text-secondary">
                        <li><i class="bi bi-telephone me-2"></i> +62 123 4567 890</li>
                        <li><i class="bi bi-envelope me-2"></i> info@bumpalkahfi.com</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Jl. Srorodiningrat, jepara Indonesia</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3 text-dark">Jam Operasional</h5>
                    <ul class="list-unstyled text-secondary">
                        <li>Senin - Minggu: 24 Jam</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <small class="text-secondary">&copy; <?php echo date('Y'); ?> TOKO SEMBAKO BUMP ALKAHFI. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 