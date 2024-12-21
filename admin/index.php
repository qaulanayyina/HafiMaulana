<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil statistik
$query_total_produk = "SELECT COUNT(*) as total FROM produk";
$result_total_produk = mysqli_query($conn, $query_total_produk);
$total_produk = mysqli_fetch_assoc($result_total_produk)['total'];

$query_total_pesanan = "SELECT COUNT(*) as total FROM pesanan";
$result_total_pesanan = mysqli_query($conn, $query_total_pesanan);
$total_pesanan = mysqli_fetch_assoc($result_total_pesanan)['total'];

$query_total_customer = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$result_total_customer = mysqli_query($conn, $query_total_customer);
$total_customer = mysqli_fetch_assoc($result_total_customer)['total'];

$query_pendapatan = "SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'pending'";
$result_pendapatan = mysqli_query($conn, $query_pendapatan);
$total_pendapatan = mysqli_fetch_assoc($result_pendapatan)['total'] ?? 0;

// Ambil pesanan terbaru
$query_pesanan_terbaru = "SELECT p.*, u.nama_lengkap 
                         FROM pesanan p 
                         JOIN users u ON p.user_id = u.id 
                         ORDER BY p.created_at DESC 
                         LIMIT 5";
$result_pesanan_terbaru = mysqli_query($conn, $query_pesanan_terbaru);

// Ambil produk dengan stok menipis (kurang dari 10)
$query_stok_menipis = "SELECT p.*, k.nama_kategori 
                       FROM produk p 
                       JOIN kategori k ON p.kategori_id = k.id 
                       WHERE p.stok < 10 
                       ORDER BY p.stok ASC";
$result_stok_menipis = mysqli_query($conn, $query_stok_menipis);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Toko Sembako</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Toko Sembako</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produk.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pesanan.php">Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kategori.php">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">Laporan</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <h2 class="mb-4">Dashboard</h2>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Produk</h5>
                        <h2 class="mb-0"><?php echo number_format($total_produk); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pesanan</h5>
                        <h2 class="mb-0"><?php echo number_format($total_pesanan); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Customer</h5>
                        <h2 class="mb-0"><?php echo number_format($total_customer); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <h2 class="mb-0">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pesanan Terbaru -->
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pesanan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan_terbaru)) : ?>
                                        <tr>
                                            <td>#<?php echo $pesanan['id']; ?></td>
                                            <td><?php echo $pesanan['nama_lengkap']; ?></td>
                                            <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                                            <td>
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
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="pesanan.php" class="btn btn-primary btn-sm">Lihat Semua Pesanan</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Stok Menipis</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($produk = mysqli_fetch_assoc($result_stok_menipis)) : ?>
                                        <tr>
                                            <td>
                                                <?php echo $produk['nama_produk']; ?><br>
                                                <small class="text-muted"><?php echo $produk['nama_kategori']; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo ($produk['stok'] == 0) ? 'danger' : 'warning'; ?>">
                                                    <?php echo $produk['stok']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="produk.php" class="btn btn-primary btn-sm">Kelola Produk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 