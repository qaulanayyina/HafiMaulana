<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Proses tambah produk
if (isset($_POST['tambah'])) {
    $kategori_id = $_POST['kategori_id'];
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $new_filename;
        }
    }
    
    $query = "INSERT INTO produk (kategori_id, nama_produk, deskripsi, harga, stok, gambar) 
              VALUES ($kategori_id, '$nama_produk', '$deskripsi', $harga, $stok, '$gambar')";
              
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Produk berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk!";
    }
    
    header("Location: produk.php");
    exit();
}

// Proses edit produk
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $kategori_id = $_POST['kategori_id'];
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada
            $query_old = "SELECT gambar FROM produk WHERE id = $id";
            $result_old = mysqli_query($conn, $query_old);
            $row_old = mysqli_fetch_assoc($result_old);
            if ($row_old['gambar'] && file_exists($target_dir . $row_old['gambar'])) {
                unlink($target_dir . $row_old['gambar']);
            }
            
            $query = "UPDATE produk 
                     SET kategori_id = $kategori_id,
                         nama_produk = '$nama_produk',
                         deskripsi = '$deskripsi',
                         harga = $harga,
                         stok = $stok,
                         gambar = '$new_filename'
                     WHERE id = $id";
        }
    } else {
        $query = "UPDATE produk 
                 SET kategori_id = $kategori_id,
                     nama_produk = '$nama_produk',
                     deskripsi = '$deskripsi',
                     harga = $harga,
                     stok = $stok
                 WHERE id = $id";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Produk berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal mengupdate produk!";
    }
    
    header("Location: produk.php");
    exit();
}

// Proses hapus produk
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    
    // Hapus gambar dari folder uploads
    $query_gambar = "SELECT gambar FROM produk WHERE id = $id";
    $result_gambar = mysqli_query($conn, $query_gambar);
    $row_gambar = mysqli_fetch_assoc($result_gambar);
    if ($row_gambar['gambar'] && file_exists("../uploads/" . $row_gambar['gambar'])) {
        unlink("../uploads/" . $row_gambar['gambar']);
    }
    
    $query = "DELETE FROM produk WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Produk berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk!";
    }
    
    header("Location: produk.php");
    exit();
}

// Ambil data kategori
$query_kategori = "SELECT * FROM kategori";
$kategori_result = mysqli_query($conn, $query_kategori);

// Ambil data produk
$query_produk = "SELECT p.*, k.nama_kategori 
                 FROM produk p 
                 LEFT JOIN kategori k ON p.kategori_id = k.id 
                 ORDER BY p.created_at DESC";
$produk_result = mysqli_query($conn, $query_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Toko Sembako</title>
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
                        <a class="nav-link active" href="produk.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pesanan.php">Pesanan</a>
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
            <h2>Kelola Produk</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </button>
        </div>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($produk = mysqli_fetch_assoc($produk_result)) : ?>
                        <tr>
                            <td>
                                <?php if($produk['gambar']) : ?>
                                    <img src="../uploads/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_produk']; ?>" class="img-thumbnail" style="width: 50px;">
                                <?php else : ?>
                                    <img src="../assets/img/no-image.jpg" alt="No Image" class="img-thumbnail" style="width: 50px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $produk['nama_produk']; ?></td>
                            <td><?php echo $produk['nama_kategori']; ?></td>
                            <td>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $produk['stok']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $produk['id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?php echo $produk['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?php echo $produk['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $produk['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Kategori</label>
                                                <select class="form-select" name="kategori_id" required>
                                                    <?php 
                                                    mysqli_data_seek($kategori_result, 0);
                                                    while($kategori = mysqli_fetch_assoc($kategori_result)) : 
                                                    ?>
                                                        <option value="<?php echo $kategori['id']; ?>" <?php echo ($kategori['id'] == $produk['kategori_id']) ? 'selected' : ''; ?>>
                                                            <?php echo $kategori['nama_kategori']; ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Produk</label>
                                                <input type="text" class="form-control" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea class="form-control" name="deskripsi" rows="3"><?php echo $produk['deskripsi']; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Harga</label>
                                                <input type="number" class="form-control" name="harga" value="<?php echo $produk['harga']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Stok</label>
                                                <input type="number" class="form-control" name="stok" value="<?php echo $produk['stok']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Gambar</label>
                                                <input type="file" class="form-control" name="gambar" accept="image/*">
                                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
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
                        <div class="modal fade" id="modalHapus<?php echo $produk['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus produk "<?php echo $produk['nama_produk']; ?>"?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $produk['id']; ?>">
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="kategori_id" required>
                                <?php 
                                mysqli_data_seek($kategori_result, 0);
                                while($kategori = mysqli_fetch_assoc($kategori_result)) : 
                                ?>
                                    <option value="<?php echo $kategori['id']; ?>">
                                        <?php echo $kategori['nama_kategori']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="nama_produk" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
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