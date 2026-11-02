<?php
  session_start();
  if (!isset($_SESSION["username"])) {
     header("Location: login.php");
     exit();
  }

  include("connection.php");

  if (isset($_POST["submit"])) {
    $nim        = htmlentities(strip_tags(trim($_POST["nim"])));
    $name       = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty    = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa        = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date  = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year  = htmlentities(strip_tags(trim($_POST["birth_year"])));

    $error_message = "";

    if (empty($nim)) {
      $error_message .= "- NIM belum diisi <br>";
    } elseif (!preg_match("/^[0-9]{8}$/", $nim)) {
      $error_message .= "- NIM harus berupa 8 digit angka <br>";
    }

    $nim_escaped   = mysqli_real_escape_string($connection, $nim);
    $dup_query     = mysqli_query($connection, "SELECT * FROM student WHERE nim='$nim_escaped'");
    if (mysqli_num_rows($dup_query) >= 1) {
      $error_message .= "- NIM yang sama sudah digunakan <br>";
    }

    if (empty($name))       $error_message .= "- Nama belum diisi <br>";
    if (empty($birth_city)) $error_message .= "- Tempat lahir belum diisi <br>";
    if (empty($department)) $error_message .= "- Jurusan belum diisi <br>";
    if (!is_numeric($gpa) || (float)$gpa <= 0) {
      $error_message .= "- IPK harus diisi dengan angka positif";
    }

    $select_ftib  = ($faculty === 'FTIB')  ? "selected" : "";
    $select_fteic = ($faculty === 'FTEIC') ? "selected" : "";

    if ($error_message === "") {
      $nim        = mysqli_real_escape_string($connection, $nim);
      $name       = mysqli_real_escape_string($connection, $name);
      $birth_city = mysqli_real_escape_string($connection, $birth_city);
      $faculty    = mysqli_real_escape_string($connection, $faculty);
      $department = mysqli_real_escape_string($connection, $department);
      $birth_date  = mysqli_real_escape_string($connection, $birth_date);
      $birth_month = mysqli_real_escape_string($connection, $birth_month);
      $birth_year  = mysqli_real_escape_string($connection, $birth_year);
      $gpa         = (float) $gpa;

      $birth_date_full = $birth_year . "-" . str_pad($birth_month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($birth_date, 2, "0", STR_PAD_LEFT);

      $query  = "INSERT INTO student VALUES ";
      $query .= "('$nim', '$name', '$birth_city', '$birth_date_full', '$faculty', '$department', $gpa)";

      $result = mysqli_query($connection, $query);
      if ($result) {
        $message = "Mahasiswa dengan nama &quot;<b>$name</b>&quot; berhasil ditambahkan";
        $message = urlencode($message);
        header("Location: student_view.php?message={$message}");
        exit();
      } else {
        die("Query gagal: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
      }
    }
  } else {
    $error_message = "";
    $nim        = "";
    $name       = "";
    $birth_city = "";
    $select_ftib  = "selected";
    $select_fteic = "";
    $department = "";
    $gpa        = "";
    $birth_date  = 1;
    $birth_month = "1";
    $birth_year  = 2000;
    $faculty     = "FTIB";
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
  <title>Tambah Mahasiswa</title>
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
        <li><a href="student_add.php" class="active">➕ Tambah</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
      </ul>
    </nav>

    <h2>Tambah Data Mahasiswa</h2>

    <?php if ($error_message !== ""): ?>
      <div class="error">⚠️ <?= $error_message ?></div>
    <?php endif; ?>

    <div class="form-card">
      <div class="form-card-header">
        <h3>➕ Formulir Mahasiswa Baru</h3>
        <p>Isi semua field yang diperlukan dengan benar</p>
      </div>
      <div class="form-card-body">
        <form id="form_mahasiswa" action="student_add.php" method="post">
          <div class="form-grid">

            <div class="form-group">
              <label for="nim">NIM</label>
              <input type="text" name="nim" id="nim"
                     value="<?= htmlspecialchars($nim) ?>"
                     placeholder="Contoh: 12345678" maxlength="8">
              <span class="form-hint">8 digit angka</span>
            </div>

            <div class="form-group">
              <label for="name">Nama Lengkap</label>
              <input type="text" name="name" id="name"
                     value="<?= htmlspecialchars($name) ?>"
                     placeholder="Nama lengkap mahasiswa">
            </div>

            <div class="form-group">
              <label for="birth_city">Tempat Lahir</label>
              <input type="text" name="birth_city" id="birth_city"
                     value="<?= htmlspecialchars($birth_city) ?>"
                     placeholder="Kota kelahiran">
            </div>

            <div class="form-group">
              <label>Tanggal Lahir</label>
              <div class="date-row">
                <select name="birth_date" id="birth_date">
                  <?php for ($i = 1; $i <= 31; $i++): ?>
                    <option value="<?= $i ?>" <?= ($i == $birth_date ? 'selected' : '') ?>>
                      <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                    </option>
                  <?php endfor; ?>
                </select>
                <select name="birth_month">
                  <?php foreach ($arr_month as $key => $value): ?>
                    <option value="<?= $key ?>" <?= ($key == $birth_month ? 'selected' : '') ?>>
                      <?= $value ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <select name="birth_year">
                  <?php for ($i = 1990; $i <= 2010; $i++): ?>
                    <option value="<?= $i ?>" <?= ($i == $birth_year ? 'selected' : '') ?>>
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
                     value="<?= htmlspecialchars($department) ?>"
                     placeholder="Nama jurusan">
            </div>

            <div class="form-group">
              <label for="gpa">IPK</label>
              <input type="text" name="gpa" id="gpa"
                     value="<?= htmlspecialchars($gpa) ?>"
                     placeholder="Contoh: 3.75">
              <span class="form-hint">Angka desimal dipisah titik, maks. 4.00</span>
            </div>

          </div><!-- .form-grid -->

          <div class="form-actions" style="margin-top:24px">
            <button type="submit" name="submit" class="btn btn-primary">💾 Simpan Data</button>
            <a href="student_view.php" class="btn btn-secondary">✖ Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
<?php mysqli_close($connection); ?>
