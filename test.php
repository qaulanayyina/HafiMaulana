<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Koneksi dan Session</h2>";

// Test session
session_start();
echo "<h3>Test Session:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "User ID dalam session: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'tidak ada') . "<br>";

// Test database
echo "<h3>Test Database:</h3>";
try {
    require_once 'config/database.php';
    echo "Koneksi database berhasil!<br>";
    
    // Test query tabel keranjang
    $query = "SHOW TABLES LIKE 'keranjang'";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
        echo "Tabel keranjang ditemukan!<br>";
        
        // Test struktur tabel keranjang
        $query = "DESCRIBE keranjang";
        $result = mysqli_query($conn, $query);
        echo "<strong>Struktur tabel keranjang:</strong><br>";
        while($row = mysqli_fetch_assoc($result)) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "Tabel keranjang tidak ditemukan!<br>";
    }
    
    // Test query tabel produk
    $query = "SHOW TABLES LIKE 'produk'";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
        echo "Tabel produk ditemukan!<br>";
        
        // Test struktur tabel produk
        $query = "DESCRIBE produk";
        $result = mysqli_query($conn, $query);
        echo "<strong>Struktur tabel produk:</strong><br>";
        while($row = mysqli_fetch_assoc($result)) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "Tabel produk tidak ditemukan!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 