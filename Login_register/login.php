<?php
session_start();

if (isset($_SESSION["error"])) {
  unset($_SESSION["error"]);
}

require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user["password"])) {
      $_SESSION["username"] = $user["username"];
      $_SESSION["user_id"] = $user["id"];
      header("Location: user.php");
      exit;
    } else {
      $_SESSION["error"] = "incorrect";
    }
  } else {
    $_SESSION["error"] = "incorrect";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link rel="stylesheet" href="login_register_style.css">
  <title>Login to Course Safari</title>
</head>
<body>
  <div class="container">
    <div class="main">
      <a href="welcome.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> <!--Back-->
      </a>
      <!-- <h1>Login to Course Safari</h1> -->
      <div class="title">
          Login to Course Safari
      </div>
      <?php if (isset($_SESSION["error"])): ?>
        <div class="error">Incorrect username or password</div>
      <?php endif; ?>
      <form action="login.php" method="post">
        <div class="form-group">
          <input type="text" class="form-control" name="username" placeholder="Username or email">
          <i class="fas fa-user"></i>
        </div>
        <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <i class="fas fa-lock"></i>
        </div>
        <input type="submit" value="Login" class="btn">
      </form>
      <a href="register.php" class="link">Don't have an account? Register here</a>
      <br>
      <a href = "../Reset_Password/email.html" class = "link">Forgot your password? Reset here</a>
    </div>
  </div>
</body>
</html>