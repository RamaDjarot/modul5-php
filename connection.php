<?php
  $dbhost = "localhost";
  $dbuser = "root";
  $dbpass = "";
  $dbname = "kampus_digital";   // database baru, terpisah dari my_campus

  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

  if (!$connection) {
    die("Koneksi dengan database gagal: " . mysqli_connect_errno() . " - " . mysqli_connect_error());
  }
?>