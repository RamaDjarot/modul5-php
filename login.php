<?php
session_start();
include("connection.php"); 

$error_message = "";
$username = "";

if (isset($_POST["submit"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $error_message .= "- Username belum diisi <br>";
    }

    if (empty($password)) {
        $error_message .= "- Password belum diisi <br>";
    }

    // Pengecekan database
    if (empty($error_message)) {
        $stmt = mysqli_prepare($connection, "SELECT password FROM admin WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (sha1($password) === $row['password']) {
                $_SESSION["username"] = $username;
                header("Location: student_view.php");
                exit();
            } else {
                $error_message .= "- Username dan/atau Password tidak sesuai";
            }
        } else {
            $error_message .= "- Username dan/atau Password tidak sesuai";
        }

        mysqli_stmt_close($stmt);
    }
    mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Mahasiswa</title>
  <link rel="stylesheet" href="assets/login.css">
</head>
<body>
  <div class="container">
    <h1>Data Mahasiswa</h1>
    <?php if ($error_message !== ""): ?>
      <div class="error"><?= $error_message ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
      <fieldset>
        <legend>Login</legend>
        <p>
          <label for="username">Username : </label>
          <input type="text" name="username" id="username" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>">
        </p>
        <p>
          <label for="password">Password : </label>
          <input type="password" name="password" id="password" value="">
        </p>
        <p>
          <input type="submit" name="submit" value="Log In">
        </p>
      </fieldset>
    </form>
  </div>
</body>
</html>