<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Tambah ke keranjang
if (isset($_POST['tambah_keranjang'])) {
    $user_id = $_SESSION['user_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Cek stok produk
    $query_stok = "SELECT stok FROM produk WHERE id = $produk_id";
    $result_stok = mysqli_query($conn, $query_stok);
    $produk = mysqli_fetch_assoc($result_stok);

    if ($jumlah > $produk['stok']) {
        $_SESSION['error'] = "Jumlah melebihi stok yang tersedia!";
        header("Location: index.php");
        exit();
    }

    // Cek apakah produk sudah ada di keranjang
    $query_check = "SELECT * FROM keranjang WHERE user_id = $user_id AND produk_id = $produk_id";
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Update jumlah
        $keranjang = mysqli_fetch_assoc($result_check);
        $new_jumlah = $keranjang['jumlah'] + $jumlah;
        
        if ($new_jumlah > $produk['stok']) {
            $_SESSION['error'] = "Total jumlah melebihi stok yang tersedia!";
            header("Location: index.php");
            exit();
        }

        $query = "UPDATE keranjang SET jumlah = $new_jumlah WHERE user_id = $user_id AND produk_id = $produk_id";
    } else {
        // Insert baru
        $query = "INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES ($user_id, $produk_id, $jumlah)";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Produk berhasil ditambahkan ke keranjang!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk ke keranjang!";
    }

    header("Location: index.php");
    exit();
}

// Update jumlah di keranjang
if (isset($_POST['update_keranjang'])) {
    $keranjang_id = $_POST['keranjang_id'];
    $jumlah = $_POST['jumlah'];

    // Cek stok produk
    $query_stok = "SELECT p.stok 
                   FROM keranjang k 
                   JOIN produk p ON k.produk_id = p.id 
                   WHERE k.id = $keranjang_id";
    $result_stok = mysqli_query($conn, $query_stok);
    $produk = mysqli_fetch_assoc($result_stok);

    if ($jumlah > $produk['stok']) {
        $_SESSION['error'] = "Jumlah melebihi stok yang tersedia!";
    } else {
        $query = "UPDATE keranjang SET jumlah = $jumlah WHERE id = $keranjang_id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Keranjang berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal mengupdate keranjang!";
        }
    }

    header("Location: keranjang.php");
    exit();
}

// Hapus dari keranjang
if (isset($_POST['hapus_keranjang'])) {
    $keranjang_id = $_POST['keranjang_id'];
    
    $query = "DELETE FROM keranjang WHERE id = $keranjang_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Produk berhasil dihapus dari keranjang!";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk dari keranjang!";
    }

    header("Location: keranjang.php");
    exit();
}
?> 