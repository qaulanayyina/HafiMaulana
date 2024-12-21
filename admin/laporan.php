<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Filter tanggal
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d');

// Statistik penjualan
$query_statistik = "SELECT 
    COUNT(*) as total_pesanan,
    SUM(total_harga) as total_pendapatan,
    COUNT(DISTINCT user_id) as total_customer
    FROM pesanan 
    WHERE status != 'pending'
    AND DATE(created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai'";
$result_statistik = mysqli_query($conn, $query_statistik);
$statistik = mysqli_fetch_assoc($result_statistik);

// Produk terlaris
$query_produk_terlaris = "SELECT p.nama_produk, k.nama_kategori,
    SUM(dp.jumlah) as total_terjual,
    SUM(dp.jumlah * dp.harga) as total_pendapatan
    FROM detail_pesanan dp
    JOIN pesanan ps ON dp.pesanan_id = ps.id
    JOIN produk p ON dp.produk_id = p.id
    JOIN kategori k ON p.kategori_id = k.id
    WHERE ps.status != 'pending'
    AND DATE(ps.created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    GROUP BY dp.produk_id
    ORDER BY total_terjual DESC
    LIMIT 10";
$result_produk_terlaris = mysqli_query($conn, $query_produk_terlaris);

// Kategori terlaris
$query_kategori_terlaris = "SELECT k.nama_kategori,
    COUNT(DISTINCT ps.id) as total_pesanan,
    SUM(dp.jumlah) as total_terjual,
    SUM(dp.jumlah * dp.harga) as total_pendapatan
    FROM detail_pesanan dp
    JOIN pesanan ps ON dp.pesanan_id = ps.id
    JOIN produk p ON dp.produk_id = p.id
    JOIN kategori k ON p.kategori_id = k.id
    WHERE ps.status != 'pending'
    AND DATE(ps.created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    GROUP BY k.id
    ORDER BY total_terjual DESC";
$result_kategori_terlaris = mysqli_query($conn, $query_kategori_terlaris);

// Penjualan per hari
$query_penjualan_harian = "SELECT 
    DATE(created_at) as tanggal,
    COUNT(*) as total_pesanan,
    SUM(total_harga) as total_pendapatan
    FROM pesanan
    WHERE status != 'pending'
    AND DATE(created_at) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    GROUP BY DATE(created_at)
    ORDER BY tanggal DESC";
$result_penjualan_harian = mysqli_query($conn, $query_penjualan_harian);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Admin Toko Sembako</title>
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
                        <a class="nav-link" href="index.php">Dashboard</a>
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
                        <a class="nav-link active" href="laporan.php">Laporan</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Laporan Penjualan</h2>
            <form method="GET" class="d-flex gap-2">
                <input type="date" class="form-control" name="tgl_mulai" value="<?php echo $tgl_mulai; ?>" required>
                <input type="date" class="form-control" name="tgl_selesai" value="<?php echo $tgl_selesai; ?>" required>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pesanan</h5>
                        <h2 class="mb-0"><?php echo number_format($statistik['total_pesanan']); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <h2 class="mb-0">Rp <?php echo number_format($statistik['total_pendapatan'], 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Customer</h5>
                        <h2 class="mb-0"><?php echo number_format($statistik['total_customer']); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Produk Terlaris -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Produk Terlaris</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Terjual</th>
                                        <th>Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($produk = mysqli_fetch_assoc($result_produk_terlaris)) : ?>
                                        <tr>
                                            <td><?php echo $produk['nama_produk']; ?></td>
                                            <td><?php echo $produk['nama_kategori']; ?></td>
                                            <td><?php echo number_format($produk['total_terjual']); ?></td>
                                            <td>Rp <?php echo number_format($produk['total_pendapatan'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Terlaris -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kategori Terlaris</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Pesanan</th>
                                        <th>Terjual</th>
                                        <th>Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($kategori = mysqli_fetch_assoc($result_kategori_terlaris)) : ?>
                                        <tr>
                                            <td><?php echo $kategori['nama_kategori']; ?></td>
                                            <td><?php echo number_format($kategori['total_pesanan']); ?></td>
                                            <td><?php echo number_format($kategori['total_terjual']); ?></td>
                                            <td>Rp <?php echo number_format($kategori['total_pendapatan'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penjualan Harian -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Penjualan Harian</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Total Pesanan</th>
                                        <th>Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($penjualan = mysqli_fetch_assoc($result_penjualan_harian)) : ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($penjualan['tanggal'])); ?></td>
                                            <td><?php echo number_format($penjualan['total_pesanan']); ?></td>
                                            <td>Rp <?php echo number_format($penjualan['total_pendapatan'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 