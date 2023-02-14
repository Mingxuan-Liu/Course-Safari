<?php
session_start();

if (isset($_SESSION["error"])) {
  unset($_SESSION["error"]);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user["password"])) {
      $_SESSION["username"] = $user["username"];
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
  <link rel="stylesheet" href="style.css">
  <title>Login to Course Safari</title>
</head>
<body>
  <div class="container">
    <div class="main">
      <a href="welcome.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <h1>Login to Course Safari</h1>
      <?php if (isset($_SESSION["error"])): ?>
        <p class="error">Incorrect username or password</p>
      <?php endif; ?>
      <form action="login.php" method="post">
        <div class="form-group">
          <input type="text" class="form-control" name="username" placeholder="Username or email">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <input type="submit" value="Login" class="btn">
      </form>
      <a href="register.php" class="link">Don't have an account? Register here</a>
    </div>
  </div>
</body>
</html>