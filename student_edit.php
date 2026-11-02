<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include("connection.php");

// ── Ambil NIM dari query string ──────────────────────────
if (!isset($_GET["nim"]) && !isset($_POST["nim"])) {
    header("Location: student_view.php");
    exit();
}

$nim_param = isset($_POST["nim"]) ? $_POST["nim"] : $_GET["nim"];
$nim_param = mysqli_real_escape_string($connection, $nim_param);

$error_message = "";
$success = false;

// ── Proses UPDATE ─────────────────────────────────────────
if (isset($_POST["submit"])) {
    $name        = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city  = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty     = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department  = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa         = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date  = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year  = htmlentities(strip_tags(trim($_POST["birth_year"])));

    if (empty($name))       $error_message .= "- Nama belum diisi <br>";
    if (empty($birth_city)) $error_message .= "- Tempat lahir belum diisi <br>";
    if (empty($department)) $error_message .= "- Jurusan belum diisi <br>";
    if (!is_numeric($gpa) || (float)$gpa <= 0) {
        $error_message .= "- IPK harus diisi dengan angka positif";
    }

    $select_ftib  = ($faculty === 'FTIB')  ? "selected" : "";
    $select_fteic = ($faculty === 'FTEIC') ? "selected" : "";

    if ($error_message === "") {
        $name        = mysqli_real_escape_string($connection, $name);
        $birth_city  = mysqli_real_escape_string($connection, $birth_city);
        $faculty     = mysqli_real_escape_string($connection, $faculty);
        $department  = mysqli_real_escape_string($connection, $department);
        $birth_date  = mysqli_real_escape_string($connection, $birth_date);
        $birth_month = mysqli_real_escape_string($connection, $birth_month);
        $birth_year  = mysqli_real_escape_string($connection, $birth_year);
        $gpa         = (float) $gpa;

        $birth_date_full = $birth_year . "-"
            . str_pad($birth_month, 2, "0", STR_PAD_LEFT) . "-"
            . str_pad($birth_date,  2, "0", STR_PAD_LEFT);

        $query = "UPDATE student SET
                    name        = '$name',
                    birth_city  = '$birth_city',
                    birth_date  = '$birth_date_full',
                    faculty     = '$faculty',
                    department  = '$department',
                    gpa         = $gpa
                  WHERE nim = '$nim_param'";

        $result = mysqli_query($connection, $query);
        if ($result) {
            $msg = urlencode("Data mahasiswa \"<b>$name</b>\" berhasil diperbarui");
            mysqli_close($connection);
            header("Location: student_view.php?message=$msg");
            exit();
        } else {
            die("Query gagal: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
        }
    }

    // Jika ada error, isi variabel dari POST
    $data = [
        'nim'        => $nim_param,
        'name'       => htmlspecialchars_decode($name),
        'birth_city' => htmlspecialchars_decode($birth_city),
        'faculty'    => $faculty,
        'department' => htmlspecialchars_decode($department),
        'gpa'        => $gpa,
    ];
    $birth_d = (int)$birth_date;
    $birth_m = (int)$birth_month;
    $birth_y = (int)$birth_year;

} else {
    // ── Load data existing dari DB ──────────────────────────
    $query  = "SELECT * FROM student WHERE nim = '$nim_param'";
    $result = mysqli_query($connection, $query);
    if (!$result || mysqli_num_rows($result) === 0) {
        header("Location: student_view.php");
        exit();
    }
    $data = mysqli_fetch_assoc($result);

    // Parse tanggal
    $parts   = explode("-", $data["birth_date"]);
    $birth_y = (int)$parts[0];
    $birth_m = (int)$parts[1];
    $birth_d = (int)$parts[2];

    $faculty     = $data["faculty"];
    $select_ftib  = ($faculty === 'FTIB')  ? "selected" : "";
    $select_fteic = ($faculty === 'FTEIC') ? "selected" : "";
}

$arr_month = [
    "1"=>"Januari","2"=>"Februari","3"=>"Maret","4"=>"April",
    "5"=>"Mei","6"=>"Juni","7"=>"Juli","8"=>"Agustus",
    "9"=>"September","10"=>"Oktober","11"=>"Nopember","12"=>"Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">🎓 Data Mahasiswa <span>Campus Portal</span></h1>
    </div>
    <nav>
      <ul>
        <li><a href="student_view.php">📋 Tampil</a></li>
        <li><a href="student_add.php">➕ Tambah</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
      </ul>
    </nav>

    <h2>Edit Data Mahasiswa</h2>

    <?php if ($error_message !== ""): ?>
      <div class="error">⚠️ <?= $error_message ?></div>
    <?php endif; ?>

    <div class="form-card">
      <div class="form-card-header">
        <h3>✏️ Edit Mahasiswa — NIM: <?= htmlspecialchars($data['nim']) ?></h3>
        <p>NIM tidak dapat diubah. Perbarui data lainnya sesuai kebutuhan.</p>
      </div>
      <div class="form-card-body">
        <form action="student_edit.php" method="post">
          <!-- NIM hidden (tidak bisa diubah) -->
          <input type="hidden" name="nim" value="<?= htmlspecialchars($data['nim']) ?>">

          <div class="form-grid">

            <div class="form-group">
              <label>NIM (tidak dapat diubah)</label>
              <input type="text" value="<?= htmlspecialchars($data['nim']) ?>" disabled
                     style="opacity:.6;cursor:not-allowed;background:#f1f5f9">
            </div>

            <div class="form-group">
              <label for="name">Nama Lengkap</label>
              <input type="text" name="name" id="name"
                     value="<?= htmlspecialchars($data['name']) ?>"
                     placeholder="Nama lengkap mahasiswa" required>
            </div>

            <div class="form-group">
              <label for="birth_city">Tempat Lahir</label>
              <input type="text" name="birth_city" id="birth_city"
                     value="<?= htmlspecialchars($data['birth_city']) ?>"
                     placeholder="Kota kelahiran" required>
            </div>

            <div class="form-group">
              <label>Tanggal Lahir</label>
              <div class="date-row">
                <select name="birth_date">
                  <?php for ($i = 1; $i <= 31; $i++): ?>
                    <option value="<?= $i ?>" <?= ($i === $birth_d ? 'selected' : '') ?>>
                      <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                    </option>
                  <?php endfor; ?>
                </select>
                <select name="birth_month">
                  <?php foreach ($arr_month as $key => $value): ?>
                    <option value="<?= $key ?>" <?= ($key == $birth_m ? 'selected' : '') ?>>
                      <?= $value ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <select name="birth_year">
                  <?php for ($i = 1990; $i <= 2010; $i++): ?>
                    <option value="<?= $i ?>" <?= ($i === $birth_y ? 'selected' : '') ?>>
                      <?= $i ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="faculty">Fakultas</label>
              <select name="faculty" id="faculty">
                <option value="FTIB"  <?= $select_ftib  ?>>FTIB</option>
                <option value="FTEIC" <?= $select_fteic ?>>FTEIC</option>
              </select>
            </div>

            <div class="form-group">
              <label for="department">Jurusan</label>
              <input type="text" name="department" id="department"
                     value="<?= htmlspecialchars($data['department']) ?>"
                     placeholder="Nama jurusan" required>
            </div>

            <div class="form-group">
              <label for="gpa">IPK</label>
              <input type="text" name="gpa" id="gpa"
                     value="<?= htmlspecialchars($data['gpa']) ?>"
                     placeholder="Contoh: 3.75">
              <span class="form-hint">Angka desimal dipisah titik, maks. 4.00</span>
            </div>

          </div><!-- .form-grid -->

          <div class="form-actions" style="margin-top:24px">
            <button type="submit" name="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="student_view.php" class="btn btn-secondary">✖ Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
