<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pesanan
$query = "SELECT p.*, COUNT(dp.id) as total_item 
          FROM pesanan p 
          LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
          WHERE p.user_id = $user_id 
          GROUP BY p.id
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Toko Sembako</title>
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
        <h2 class="mb-4">Riwayat Pesanan</h2>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0) : ?>
            <div class="row">
                <?php while ($pesanan = mysqli_fetch_assoc($result)) : ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Pesanan #<?php echo $pesanan['id']; ?></h5>
                                <span class="badge bg-<?php 
                                    echo match($pesanan['status']) {
                                        'pending' => 'warning',
                                        'dibayar' => 'info',
                                        'diproses' => 'primary',
                                        'dikirim' => 'info',
                                        'selesai' => 'success',
                                        default => 'secondary'
                                    };
                                ?>"><?php echo ucfirst($pesanan['status']); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Tanggal Pesanan:</small>
                                    <div><?php echo date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Total Item:</small>
                                    <div><?php echo $pesanan['total_item']; ?> item</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Total Harga:</small>
                                    <div>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Alamat Pengiriman:</small>
                                    <div><?php echo $pesanan['alamat_pengiriman']; ?></div>
                                </div>
                                
                                <?php
                                // Ambil detail pesanan
                                $pesanan_id = $pesanan['id'];
                                $query_detail = "SELECT dp.*, p.nama_produk 
                                               FROM detail_pesanan dp
                                               JOIN produk p ON dp.produk_id = p.id
                                               WHERE dp.pesanan_id = $pesanan_id";
                                $result_detail = mysqli_query($conn, $query_detail);
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Jumlah</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($detail = mysqli_fetch_assoc($result_detail)) : ?>
                                                <tr>
                                                    <td><?php echo $detail['nama_produk']; ?></td>
                                                    <td><?php echo $detail['jumlah']; ?></td>
                                                    <td>Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($detail['jumlah'] * $detail['harga'], 0, ',', '.'); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info">
                Anda belum memiliki riwayat pesanan. <a href="index.php">Belanja sekarang</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Toko Sembako Online</h5>
                    <p>Menyediakan berbagai macam kebutuhan pokok dengan harga terjangkau.</p>
                </div>
                <div class="col-md-6">
                    <h5>Kontak Kami</h5>
                    <p>
                        <i class="bi bi-telephone"></i> +62 123 4567 890<br>
                        <i class="bi bi-envelope"></i> info@tokosembako.com<br>
                        <i class="bi bi-geo-alt"></i> Jl. Srosodiningrat.Jepara, Indonesia
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> Toko Sembako Online. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 