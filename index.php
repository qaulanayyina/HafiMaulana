<?php
session_start();
require_once 'config/database.php';

// Ambil daftar kategori
$query_kategori = "SELECT * FROM kategori";
$kategori_result = mysqli_query($conn, $query_kategori);

// Filter produk berdasarkan kategori
$kategori_id = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$where_clause = $kategori_id ? "WHERE kategori_id = $kategori_id" : "";

// Ambil daftar produk
$query_produk = "SELECT p.*, k.nama_kategori 
                 FROM produk p 
                 LEFT JOIN kategori k ON p.kategori_id = k.id 
                 $where_clause 
                 ORDER BY p.created_at DESC";
$produk_result = mysqli_query($conn, $query_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOKO SEMBAKO BUMP ALKAHFI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hero-section {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .card {
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .card:hover .card-img-top {
            transform: scale(1.05);
        }
        .carousel-item img {
            filter: brightness(0.7);
            height: 500px;
            object-fit: cover;
        }
        .carousel-caption {
            background: rgba(0,0,0,0.5);
            padding: 20px;
            border-radius: 10px;
            bottom: 50px;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        footer {
            margin-top: 50px;
        }
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .price-tag {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: #007bff;
        }
        .category-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .category-filter .btn {
            border-radius: 20px;
            padding: 8px 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-shop me-2"></i>
                <span>
                    TOKO SEMBAKO<br>
                    BUMP ALKAHFI
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-grid me-1"></i>Kategori
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php">Semua</a></li>
                            <?php while($kategori = mysqli_fetch_assoc($kategori_result)) : ?>
                                <li><a class="dropdown-item" href="index.php?kategori=<?php echo $kategori['id']; ?>">
                                    <?php echo $kategori['nama_kategori']; ?>
                                </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keranjang.php">
                                <i class="bi bi-cart me-1"></i>Keranjang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">
                                <i class="bi bi-person me-1"></i><?php echo $_SESSION['username']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="bi bi-person-plus me-1"></i>Daftar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">
                TOKO SEMBAKO<br>
                BUMP ALKAHFI
            </h1>
            <p class="hero-subtitle">Menyediakan Berbagai Macam Kebutuhan Pokok dengan Harga Terjangkau</p>
        </div>
    </div>

    <!-- Carousel -->
    <div id="mainCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=600&q=80" class="d-block w-100" alt="Toko Sembako 1">
                <div class="carousel-caption">
                    <h2 class="display-4 fw-bold">Selamat Datang di<br>TOKO SEMBAKO BUMP ALKAHFI</h2>
                    <p class="lead">Belanja kebutuhan pokok dengan mudah dan cepat</p>
                    <a href="#products" class="btn btn-primary btn-lg">Belanja Sekarang</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=600&q=80" class="d-block w-100" alt="Toko Sembako 2">
                <div class="carousel-caption">
                    <h2 class="display-4 fw-bold">TOKO SEMBAKO BUMP ALKAHFI<br>Produk Berkualitas</h2>
                    <p class="lead">Dijamin fresh dan berkualitas tinggi</p>
                    <a href="#products" class="btn btn-primary btn-lg">Lihat Produk</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&h=600&q=80" class="d-block w-100" alt="Toko Sembako 3">
                <div class="carousel-caption">
                    <h2 class="display-4 fw-bold">TOKO SEMBAKO BUMP ALKAHFI<br>Pengiriman Cepat</h2>
                    <p class="lead">Layanan pengiriman yang cepat dan aman</p>
                    <a href="#products" class="btn btn-primary btn-lg">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Content -->
    <div class="container my-5" id="products">
        <?php if($kategori_id) : 
            $current_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id = $kategori_id"));
        ?>
            <h2 class="section-title">Kategori: <?php echo $current_kategori['nama_kategori']; ?></h2>
        <?php else : ?>
            <h2 class="section-title">Semua Produk</h2>
        <?php endif; ?>

        <!-- Category Filter -->
        <div class="category-filter">
            <a href="index.php" class="btn <?php echo !$kategori_id ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
            <?php 
            mysqli_data_seek($kategori_result, 0);
            while($kategori = mysqli_fetch_assoc($kategori_result)) : 
            ?>
                <a href="index.php?kategori=<?php echo $kategori['id']; ?>" 
                   class="btn <?php echo $kategori_id == $kategori['id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php echo $kategori['nama_kategori']; ?>
                </a>
            <?php endwhile; ?>
        </div>
        
        <div class="product-grid">
            <?php while($produk = mysqli_fetch_assoc($produk_result)) : ?>
                <div class="card h-100">
                    <?php if($produk['gambar']) : ?>
                        <img src="uploads/<?php echo $produk['gambar']; ?>" class="card-img-top" alt="<?php echo $produk['nama_produk']; ?>">
                    <?php else : ?>
                        <?php
                        $unsplash_images = [
                            'Beras' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Minyak Goreng' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Gula' => 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Telur' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Tepung' => 'https://images.unsplash.com/photo-1609164994411-de9f0ec72045?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Mie Instan' => 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Bumbu Dapur' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Minuman' => 'https://images.unsplash.com/photo-1544252890-c3e95a7f4ac1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Snack' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'Kebutuhan Pokok Lainnya' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                        ];
                        $image_url = isset($unsplash_images[$produk['nama_kategori']]) ? 
                                   $unsplash_images[$produk['nama_kategori']] : 
                                   'https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                        ?>
                        <img src="<?php echo $image_url; ?>" class="card-img-top" alt="<?php echo $produk['nama_produk']; ?>">
                    <?php endif; ?>
                    <span class="category-badge"><?php echo $produk['nama_kategori']; ?></span>
                    <?php if($produk['stok'] < 10) : ?>
                        <span class="stock-badge bg-warning">Stok Terbatas</span>
                    <?php elseif($produk['stok'] > 0) : ?>
                        <span class="stock-badge">Stok Tersedia</span>
                    <?php else : ?>
                        <span class="stock-badge bg-danger">Stok Habis</span>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $produk['nama_produk']; ?></h5>
                        <p class="card-text text-muted"><?php echo $produk['deskripsi']; ?></p>
                        <div class="price-tag">
                            Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </div>
                        <?php if(isset($_SESSION['user_id']) && $produk['stok'] > 0) : ?>
                            <form action="keranjang_aksi.php" method="POST">
                                <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
                                <div class="d-flex gap-2">
                                    <input type="number" name="jumlah" class="form-control" value="1" min="1" max="<?php echo $produk['stok']; ?>">
                                    <button type="submit" name="tambah_keranjang" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </form>
                        <?php elseif($produk['stok'] == 0) : ?>
                            <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                        <?php else : ?>
                            <a href="auth/login.php" class="btn btn-primary w-100">Login untuk membeli</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
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