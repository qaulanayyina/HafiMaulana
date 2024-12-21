<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Proses update status pesanan
if (isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE pesanan SET status = '$status' WHERE id = $pesanan_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status pesanan berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status pesanan!";
    }
    
    header("Location: pesanan.php");
    exit();
}

// Filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where_clause = $status_filter ? "WHERE p.status = '$status_filter'" : "";

// Ambil data pesanan
$query = "SELECT p.*, u.nama_lengkap, u.no_telp,
          (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id = p.id) as total_item
          FROM pesanan p 
          JOIN users u ON p.user_id = u.id 
          $where_clause
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Toko Sembako</title>
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
                        <a class="nav-link active" href="pesanan.php">Pesanan</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Pesanan</h2>
            <div>
                <div class="btn-group">
                    <a href="pesanan.php" class="btn btn-outline-primary <?php echo !$status_filter ? 'active' : ''; ?>">Semua</a>
                    <a href="pesanan.php?status=pending" class="btn btn-outline-primary <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="pesanan.php?status=dibayar" class="btn btn-outline-primary <?php echo $status_filter === 'dibayar' ? 'active' : ''; ?>">Dibayar</a>
                    <a href="pesanan.php?status=diproses" class="btn btn-outline-primary <?php echo $status_filter === 'diproses' ? 'active' : ''; ?>">Diproses</a>
                    <a href="pesanan.php?status=dikirim" class="btn btn-outline-primary <?php echo $status_filter === 'dikirim' ? 'active' : ''; ?>">Dikirim</a>
                    <a href="pesanan.php?status=selesai" class="btn btn-outline-primary <?php echo $status_filter === 'selesai' ? 'active' : ''; ?>">Selesai</a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
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
                                    <small class="text-muted">Customer:</small>
                                    <div>
                                        <?php echo $pesanan['nama_lengkap']; ?><br>
                                        <small class="text-muted"><?php echo $pesanan['no_telp']; ?></small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Alamat Pengiriman:</small>
                                    <div><?php echo $pesanan['alamat_pengiriman']; ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Total Item:</small>
                                    <div><?php echo $pesanan['total_item']; ?> item</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Total Harga:</small>
                                    <div>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></div>
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

                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                    <div class="row g-2">
                                        <div class="col">
                                            <select class="form-select" name="status" required>
                                                <option value="pending" <?php echo $pesanan['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="dibayar" <?php echo $pesanan['status'] === 'dibayar' ? 'selected' : ''; ?>>Dibayar</option>
                                                <option value="diproses" <?php echo $pesanan['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                                <option value="dikirim" <?php echo $pesanan['status'] === 'dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                                <option value="selesai" <?php echo $pesanan['status'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info">
                Tidak ada pesanan yang ditemukan.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 