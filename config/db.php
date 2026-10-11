<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "tukang_emas_rezky";

// Nonaktifkan pelaporan error bawaan ke publik untuk melindungi kredensial
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error);
    die("Koneksi ke sistem database sedang mengalami kendala. Silakan coba beberapa saat lagi.");
}

$conn->set_charset("utf8mb4");
?>