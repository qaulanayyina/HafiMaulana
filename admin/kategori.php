<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Proses tambah kategori
if (isset($_POST['tambah'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Kategori berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan kategori!";
    }
    
    header("Location: kategori.php");
    exit();
}

// Proses edit kategori
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    
    $query = "UPDATE kategori SET nama_kategori = '$nama_kategori' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Kategori berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate kategori!";
    }
    
    header("Location: kategori.php");
    exit();
}

// Proses hapus kategori
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    
    // Cek apakah kategori masih digunakan di tabel produk
    $query_check = "SELECT COUNT(*) as total FROM produk WHERE kategori_id = $id";
    $result_check = mysqli_query($conn, $query_check);
    $row_check = mysqli_fetch_assoc($result_check);
    
    if ($row_check['total'] > 0) {
        $_SESSION['error'] = "Kategori tidak dapat dihapus karena masih digunakan oleh produk!";
    } else {
        $query = "DELETE FROM kategori WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus kategori!";
        }
    }
    
    header("Location: kategori.php");
    exit();
}

// Ambil data kategori
$query = "SELECT k.*, COUNT(p.id) as total_produk 
          FROM kategori k 
          LEFT JOIN produk p ON k.id = p.kategori_id 
          GROUP BY k.id 
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin Toko Sembako</title>
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
                        <a class="nav-link active" href="kategori.php">Kategori</a>
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
            <h2>Kelola Kategori</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        </div>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kategori</th>
                                <th>Total Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($kategori = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?php echo $kategori['nama_kategori']; ?></td>
                                    <td><?php echo $kategori['total_produk']; ?> produk</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $kategori['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?php echo $kategori['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit<?php echo $kategori['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Kategori</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $kategori['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Kategori</label>
                                                        <input type="text" class="form-control" name="nama_kategori" value="<?php echo $kategori['nama_kategori']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Hapus -->
                                <div class="modal fade" id="modalHapus<?php echo $kategori['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Kategori</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus kategori "<?php echo $kategori['nama_kategori']; ?>"?</p>
                                                <?php if ($kategori['total_produk'] > 0) : ?>
                                                    <div class="alert alert-warning">
                                                        Kategori ini memiliki <?php echo $kategori['total_produk']; ?> produk.
                                                        Anda perlu mengubah kategori produk-produk tersebut sebelum menghapus kategori ini.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $kategori['id']; ?>">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" name="nama_kategori" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 