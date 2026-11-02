<?php
  session_start();
  if (!isset($_SESSION["username"])) {
     header("Location: login.php");
     exit();
  }

  include("connection.php");
  
  if (isset($_GET["message"])) {
      $message = $_GET["message"];
  }
  
  $total_result = mysqli_query($connection, "SELECT COUNT(*) AS total FROM student");
  $total_row    = mysqli_fetch_assoc($total_result);
  $total        = $total_row['total'];

  $ftib_result  = mysqli_query($connection, "SELECT COUNT(*) AS c FROM student WHERE faculty='FTIB'");
  $ftib_row     = mysqli_fetch_assoc($ftib_result);
  $total_ftib   = $ftib_row['c'];

  $avg_result   = mysqli_query($connection, "SELECT ROUND(AVG(gpa),2) AS avg_gpa FROM student");
  $avg_row      = mysqli_fetch_assoc($avg_result);
  $avg_gpa      = $avg_row['avg_gpa'] ?? '0.00';
     
  $query = "SELECT * FROM student ORDER BY nim ASC";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">🎓 Data Mahasiswa <span>Campus Portal</span></h1>
    </div>
    <nav>
      <ul>
        <li><a href="student_view.php" class="active">📋 Tampil</a></li>
        <li><a href="student_add.php">➕ Tambah</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
      </ul>
    </nav>

    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
          <div class="stat-num"><?= $total ?></div>
          <div class="stat-label">Total Mahasiswa</div>
        </div>
      </div>
      <div class="stat-card stat-secondary">
        <div class="stat-icon">🏫</div>
        <div class="stat-info">
          <div class="stat-num"><?= $total_ftib ?></div>
          <div class="stat-label">Mahasiswa FTIB</div>
        </div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
          <div class="stat-num"><?= $avg_gpa ?></div>
          <div class="stat-label">Rata-rata IPK</div>
        </div>
      </div>
    </div>

    <?php if (isset($message)): ?>
      <div class="pesan">✅ <?= $message ?></div>
    <?php endif; ?>

    <h2>Daftar Mahasiswa</h2>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Fakultas</th>
            <th>Jurusan</th>
            <th>IPK</th>
            <th style="text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $result = mysqli_query($connection, $query);
          if (!$result) {
              die("Query Error: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
          }

          $row_count = mysqli_num_rows($result);
          if ($row_count === 0):
        ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Belum ada data mahasiswa. <a href="student_add.php">Tambah sekarang</a>.</p>
              </div>
            </td>
          </tr>
        <?php
          else:
            while ($data = mysqli_fetch_assoc($result)):
              $birth_date     = strtotime($data["birth_date"]);
              $formatted_date = date("d-m-Y", $birth_date);
              $gpa            = (float) $data["gpa"];
              $gpa_class      = $gpa >= 3.5 ? 'gpa-high' : ($gpa >= 3.0 ? 'gpa-mid' : 'gpa-low');
        ?>
          <tr>
            <td><span class="badge-nim"><?= htmlspecialchars($data['nim']) ?></span></td>
            <td><?= htmlspecialchars($data['name']) ?></td>
            <td><?= htmlspecialchars($data['birth_city']) ?></td>
            <td><?= $formatted_date ?></td>
            <td><?= htmlspecialchars($data['faculty']) ?></td>
            <td><?= htmlspecialchars($data['department']) ?></td>
            <td><span class="badge-gpa <?= $gpa_class ?>"><?= number_format($gpa, 2) ?></span></td>
            <td>
              <div class="btn-group" style="justify-content:center">
                <a href="student_edit.php?nim=<?= urlencode($data['nim']) ?>" class="btn btn-edit">✏️ Edit</a>
                <button class="btn btn-delete" onclick="confirmDelete('<?= htmlspecialchars($data['nim'], ENT_QUOTES) ?>', '<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>')">🗑️ Hapus</button>
              </div>
            </td>
          </tr>
        <?php endwhile; endif; ?>
        <?php mysqli_free_result($result); mysqli_close($connection); ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="deleteModal">
    <div class="modal">
      <div class="modal-icon">🗑️</div>
      <h3>Hapus Mahasiswa?</h3>
      <p id="deleteMessage">Data mahasiswa ini akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
      <div class="modal-actions">
        <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-delete" style="padding:10px 24px;font-size:.9rem">Ya, Hapus</a>
      </div>
    </div>
  </div>

  <script>
    function confirmDelete(nim, name) {
      document.getElementById('deleteMessage').innerHTML =
        'Yakin ingin menghapus mahasiswa <strong>' + name + '</strong> (NIM: ' + nim + ')?<br>Data tidak dapat dikembalikan.';
      document.getElementById('confirmDeleteBtn').href = 'student_delete.php?nim=' + encodeURIComponent(nim);
      document.getElementById('deleteModal').classList.add('active');
    }
    function closeModal() {
      document.getElementById('deleteModal').classList.remove('active');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
  </script>
</body>
</html>
