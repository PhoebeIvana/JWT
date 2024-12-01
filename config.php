<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wikiWow";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>