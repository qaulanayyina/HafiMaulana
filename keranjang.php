<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data keranjang
$query = "SELECT k.*, p.nama as nama_produk, p.harga, p.stok, p.gambar 
          FROM keranjang k
          JOIN produk p ON k.produk_id = p.id
          WHERE k.user_id = $user_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Bump Alkahfi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'config/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Keranjang Belanja</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Gambar</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($row = mysqli_fetch_assoc($result)): 
                            $subtotal = $row['harga'] * $row['jumlah'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                <td>
                                    <img src="uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>" 
                                         style="max-width: 100px;">
                                </td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <form action="keranjang_aksi.php" method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="keranjang_id" value="<?= $row['id'] ?>">
                                        <input type="number" name="jumlah" value="<?= $row['jumlah'] ?>" 
                                               min="1" max="<?= $row['stok'] ?>" class="form-control" style="width: 80px;">
                                        <button type="submit" name="update_keranjang" class="btn btn-sm btn-primary ms-2">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                <td>
                                    <form action="keranjang_aksi.php" method="POST" class="d-inline">
                                        <input type="hidden" name="keranjang_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="hapus_keranjang" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr class="table-primary">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Lanjut Belanja</a>
                <a href="checkout.php" class="btn btn-primary">Checkout</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Keranjang belanja Anda kosong. 
                <a href="index.php" class="alert-link">Klik di sini untuk belanja</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 