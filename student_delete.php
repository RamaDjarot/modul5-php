<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["nim"])) {
    header("Location: student_view.php");
    exit();
}

include("connection.php");

$nim = mysqli_real_escape_string($connection, $_GET["nim"]);

$check  = mysqli_query($connection, "SELECT name FROM student WHERE nim = '$nim'");
if (!$check || mysqli_num_rows($check) === 0) {
    header("Location: student_view.php?message=" . urlencode("Mahasiswa tidak ditemukan."));
    exit();
}
$row  = mysqli_fetch_assoc($check);
$name = $row['name'];

$result = mysqli_query($connection, "DELETE FROM student WHERE nim = '$nim'");

if ($result) {
    $message = urlencode("Mahasiswa \"<b>$name</b>\" (NIM: $nim) berhasil dihapus");
    mysqli_close($connection);
    header("Location: student_view.php?message=$message");
} else {
    die("Query gagal: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
}
exit();
?>
