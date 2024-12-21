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

// Ambil riwayat pesanan
$query_pesanan = "SELECT p.*, COUNT(dp.id) as total_item 
                 FROM pesanan p 
                 LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
                 WHERE p.user_id = $user_id 
                 GROUP BY p.id
                 ORDER BY p.created_at DESC
                 LIMIT 5";
$result_pesanan = mysqli_query($conn, $query_pesanan);

// Proses update profil
if (isset($_POST['update_profile'])) {
    try {
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
        
        // Update password jika diisi
        $password_query = "";
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_query = ", password = '$password'";
        }

        // Proses upload gambar
        $foto_profil_query = "";
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['foto_profil']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                throw new Exception("Format file tidak diizinkan. Gunakan format: " . implode(', ', $allowed));
            }

            // Buat direktori jika belum ada
            $upload_dir = 'uploads/profile';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception("Gagal membuat direktori upload");
                }
                chmod($upload_dir, 0777);
            }

            // Generate nama file unik
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $destination = $upload_dir . '/' . $new_filename;

            // Cek ukuran file (max 5MB)
            if ($_FILES['foto_profil']['size'] > 5 * 1024 * 1024) {
                throw new Exception("Ukuran file terlalu besar. Maksimal 5MB");
            }

            if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $destination)) {
                throw new Exception("Gagal mengupload file");
            }

            // Hapus foto lama jika ada
            if (!empty($user['foto_profil']) && file_exists($user['foto_profil'])) {
                unlink($user['foto_profil']);
            }

            $foto_profil_query = ", foto_profil = '$destination'";
        }
        
        $query = "UPDATE users 
                 SET nama_lengkap = '$nama_lengkap',
                     alamat = '$alamat',
                     no_telp = '$no_telp'
                     $password_query
                     $foto_profil_query
                 WHERE id = $user_id";
                
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Gagal mengupdate database: " . mysqli_error($conn));
        }

        $_SESSION['success'] = "Profil berhasil diupdate!";
        header("Location: profil.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - TOKO SEMBAKO BUMP ALKAHFI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 60px;
        }
        .profile-header {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
            padding: 100px 0 30px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1578916171728-46686eac8d58?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=600&q=80') center/cover;
            opacity: 0.1;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .profile-img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 6px solid rgba(255,255,255,0.9);
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }
        .profile-img:hover {
            transform: scale(1.05);
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-header {
            background: none;
            border-bottom: 2px solid #f0f0f0;
            padding: 25px;
            font-weight: 600;
        }
        .card-body {
            padding: 25px;
        }
        .status-badge {
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .stats-card {
            background: linear-gradient(45deg, #4e54c8, #8f94fb);
            color: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 0.2rem rgba(78, 84, 200, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #4e54c8, #8f94fb);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 84, 200, 0.4);
        }
        .order-card {
            border: none;
            border-radius: 15px;
            background: white;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(78, 84, 200, 0.95) !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 600;
            letter-spacing: 1px;
        }
        .nav-link {
            font-weight: 500;
            padding: 10px 15px !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: translateY(-2px);
        }
        footer {
            background: #2d2d2d;
            padding: 60px 0 30px;
            margin-top: 50px;
        }
        footer h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 25px;
        }
        footer .text-muted {
            color: #a0a0a0 !important;
        }
        .alert {
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        .empty-state p {
            color: #888;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .profile-header {
                padding: 60px 0 20px;
            }
            .profile-img {
                width: 140px;
                height: 140px;
            }
            .stats-card {
                margin-bottom: 15px;
            }
            .card-header {
                padding: 20px;
            }
            .card-body {
                padding: 20px;
            }
        }
        .profile-img-container {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }
        .profile-img-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(78, 84, 200, 0.9);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .profile-img-upload:hover {
            background: rgba(78, 84, 200, 1);
            transform: scale(1.1);
        }
        .profile-img-upload i {
            color: white;
            font-size: 1.2rem;
        }
        #foto_profil {
            display: none;
        }
        .preview-container {
            display: none;
            margin-top: 10px;
        }
        .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop me-2"></i>
                TOKO SEMBAKO BUMP ALKAHFI
            </a>
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
                        <a class="nav-link active" href="profil.php">
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

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container text-center position-relative">
            <div class="profile-img-container">
                <img src="<?php echo !empty($user['foto_profil']) ? $user['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap']) . '&background=random'; ?>" 
                     alt="Profile" class="profile-img" id="profile_preview">
                <label for="foto_profil" class="profile-img-upload">
                    <i class="bi bi-camera"></i>
                </label>
            </div>
            <h2 class="mb-2"><?php echo $user['nama_lengkap']; ?></h2>
            <p class="mb-0"><i class="bi bi-geo-alt me-2"></i><?php echo $user['alamat']; ?></p>
            <p><i class="bi bi-telephone me-2"></i><?php echo $user['no_telp']; ?></p>
        </div>
    </div>

    <!-- Content -->
    <div class="container mb-5">
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row mb-4">
            <?php
            // Total pesanan
            $query_total = "SELECT 
                COUNT(*) as total_pesanan,
                SUM(total_harga) as total_belanja,
                COUNT(CASE WHEN status = 'selesai' THEN 1 END) as pesanan_selesai
                FROM pesanan WHERE user_id = $user_id";
            $result_total = mysqli_query($conn, $query_total);
            $total = mysqli_fetch_assoc($result_total);
            ?>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="bi bi-bag"></i>
                    <h3><?php echo number_format($total['total_pesanan']); ?></h3>
                    <p class="mb-0">Total Pesanan</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="bi bi-cash"></i>
                    <h3>Rp <?php echo number_format($total['total_belanja'], 0, ',', '.'); ?></h3>
                    <p class="mb-0">Total Belanja</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="bi bi-check-circle"></i>
                    <h3><?php echo number_format($total['pesanan_selesai']); ?></h3>
                    <p class="mb-0">Pesanan Selesai</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Info -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="form-control">
                            <div class="preview-container" id="preview_container">
                                <img src="" alt="Preview" class="preview-image" id="image_preview">
                                <button type="button" class="btn btn-sm btn-danger w-100" id="remove_preview">Hapus Gambar</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" value="<?php echo $user['nama_lengkap']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="3" required><?php echo $user['alamat']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" name="no_telp" value="<?php echo $user['no_telp']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="password">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profil</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Pesanan Terakhir</h5>
                        <a href="pesanan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_pesanan) > 0) : ?>
                            <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan)) : ?>
                                <div class="card order-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Pesanan #<?php echo $pesanan['id']; ?></h6>
                                                <p class="mb-1 text-muted">
                                                    <?php echo date('d F Y H:i', strtotime($pesanan['created_at'])); ?>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge bg-primary"><?php echo $pesanan['total_item']; ?> Item</span>
                                                    <span class="badge bg-success">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></span>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <span class="status-badge bg-<?php 
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
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="empty-state">
                                <i class="bi bi-bag-x"></i>
                                <p>Belum ada pesanan</p>
                                <a href="index.php" class="btn btn-primary">Mulai Belanja</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">
                        <i class="bi bi-shop me-2"></i>
                        TOKO SEMBAKO<br>
                        BUMP ALKAHFI
                    </h5>
                    <p class="text-white">Menyediakan berbagai macam kebutuhan pokok dengan harga terjangkau dan kualitas terjamin.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Kontak Kami</h5>
                    <ul class="list-unstyled text-white">
                        <li><i class="bi bi-telephone me-2"></i> +62 123 4567 890</li>
                        <li><i class="bi bi-envelope me-2"></i> info@bumpalkahfi.com</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Jl. Srosodiningrat,Jepara, Indonesia</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Jam Operasional</h5>
                    <ul class="list-unstyled text-white">
                        <li>Senin - Minggu: 24 Jam</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <small class="text-white">&copy; <?php echo date('Y'); ?> TOKO SEMBAKO BUMP ALKAHFI. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('foto_profil');
            const previewContainer = document.getElementById('preview_container');
            const imagePreview = document.getElementById('image_preview');
            const profilePreview = document.getElementById('profile_preview');
            const removeButton = document.getElementById('remove_preview');

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        profilePreview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            removeButton.addEventListener('click', function() {
                fileInput.value = '';
                previewContainer.style.display = 'none';
                profilePreview.src = '<?php echo !empty($user['foto_profil']) ? $user['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap']) . '&background=random'; ?>';
            });
        });
    </script>
</body>
</html>