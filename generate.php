<?php
  $dbhost = "localhost";
  $dbuser = "root";
  $dbpass = "";

  // Buat Koneksi
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass);
  if (!$connection) {
    die("Koneksi dengan database gagal: " . mysqli_connect_errno() . " - " . mysqli_connect_error());
  }

  // ─── Buat Database baru (tidak akan menimpa my_campus) ───────────
  $query = "CREATE DATABASE IF NOT EXISTS kampus_digital";
  $result = mysqli_query($connection, $query);
  if (!$result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Database <b>'kampus_digital'</b> berhasil dibuat... <br>";
  }

  // Pilih Database
  $result = mysqli_select_db($connection, "kampus_digital");
  if (!$result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Database <b>'kampus_digital'</b> berhasil dipilih... <br>";
  }

  // ─── Buat Tabel student ───────────────────────────────────────────
  $query = "DROP TABLE IF EXISTS student";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'student'</b> berhasil dihapus (reset)... <br>";
  }

  $query = "
    CREATE TABLE student (
      nim         CHAR(8)       NOT NULL,
      name        VARCHAR(100),
      birth_city  VARCHAR(50),
      birth_date  DATE,
      faculty     VARCHAR(50),
      department  VARCHAR(50),
      gpa         DECIMAL(3,2),
      PRIMARY KEY (nim)
    )";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'student'</b> berhasil dibuat... <br>";
  }

  // ─── Insert data contoh student ──────────────────────────────────
  $query = "
    INSERT INTO student VALUES
      ('12041952', 'Brian Yuko',    'Padang',  '1996-11-23', 'FTIB',  'Sistem Informasi', 3.10),
      ('12042010', 'Safira Yanuar', 'Bandung', '1994-08-22', 'FTIB',  'Sistem Informasi', 3.90),
      ('12042012', 'Rezja Zalva',   'Jakarta', '1997-12-31', 'FTIB',  'Informatika',      3.50),
      ('12042034', 'Agha Rizky',    'Jakarta', '1997-06-28', 'FTEIC', 'Sains Data',       3.40),
      ('12042041', 'Adhiaksa',      'Medan',   '1995-04-02', 'FTEIC', 'Bisnis Digital',   3.70)";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'student'</b> berhasil diisi dengan data contoh... <br>";
  }

  // ─── Buat Tabel admin ─────────────────────────────────────────────
  $query = "DROP TABLE IF EXISTS admin";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'admin'</b> berhasil dihapus (reset)... <br>";
  }

  $query = "CREATE TABLE admin (username VARCHAR(50), password CHAR(40))";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'admin'</b> berhasil dibuat... <br>";
  }

  // ─── Insert admin (password: admin123 — disimpan sebagai SHA1) ───
  $username = "admin";
  $password = sha1("admin123");   // hash: f865b53623b121fd34ee5426c792e5c33af8c227
  $query = "INSERT INTO admin VALUES ('$username', '$password')";
  $query_result = mysqli_query($connection, $query);
  if (!$query_result) {
    die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
  } else {
    echo "Tabel <b>'admin'</b> berhasil diisi... <br>";
  }

  mysqli_close($connection);

  echo "<br><hr>";
  echo "<b style='color:green'>✅ Setup selesai!</b><br>";
  echo "Database <b>kampus_digital</b> siap digunakan.<br><br>";
  echo "<a href='index.php' style='padding:8px 16px;background:#4f46e5;color:#fff;border-radius:6px;text-decoration:none'>🚀 Buka Aplikasi</a>";
?>