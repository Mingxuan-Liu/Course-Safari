<?php
session_start();

if (isset($_SESSION["error"])) {
  unset($_SESSION["error"]);
}

require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $confirm_password = $_POST["confirm_password"];

  if ($password != $confirm_password) {
    $_SESSION["error"] = "password_mismatch";
  } else {
    $password = password_hash($password, PASSWORD_DEFAULT);

    $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
      $_SESSION["error"] = "existing_1";
    } else {
      $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
      if (mysqli_query($conn, $sql)) {
        $_SESSION["register_success"] = true;
        $_SESSION["username"] = $username;

        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = $user["id"];
      } else {
        $_SESSION["error"] = "failed";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
  <link rel="stylesheet" href="login_register_style.css">
  <title>Welcome to Course Safari</title>
</head>

<body>
  <div class="container">
    <div class="main">
      <?php
        if (isset($_SESSION["register_success"])): 
      ?>
        <div class="success-message">
          <i class="fas fa-check-circle"></i>
          <h1S>Registration Successful!</h1S>
          <pS>Do you want to go to the user page?</pS>
          <div class="btn-group">
            <a href="user.php" class="btn">Yes</a>
            <a href="welcome.php" class="btn">No</a>
          </div>
        </div>
      <?php 
        else: 
      ?>
        <a href="welcome.php" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
        <div class="title">
          Register for Course Safari
        </div>
        <form action="register.php" method="post">
          <div class="form-group">
              <input type="text" class="form-control" name="username" placeholder="Username">
              <i class="fas fa-user"></i>
          </div>
          <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Email">
            <i class="far fa-envelope"></i>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <i class="fas fa-lock"></i>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
            <i class="fas fa-lock"></i>
          </div>
          <input type="submit" value="Register" class="btn">
        </form>
        <a href="login.php" class="link">Already have an account? Login here</a>
        <?php 
          if (isset($_SESSION["error"])): 
            if ($_SESSION["error"] == "existing_1"):
        ?>
          <div class="error-message">
            
            <h1>Registration Failed!</h1>
            <p>Username or email already exists.</p>
          </div>
        <?php 
            elseif ($_SESSION["error"] == "password_mismatch"):
        ?>
          <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <h1>Oops!</h1>
            <p>Passwords do not match. Please try again.</p>
          </div>
        <?php 
            else:
        ?>
          <div class="error-message">
            
            <h1>Registration Failed!</h1>
            <p>An error occurred. Please try again later.</p>
          </div>
        <?php 
            endif;
            unset($_SESSION["error"]);
          endif;
        ?>
      <?php 
        endif;
        unset($_SESSION["register_success"]);
      ?>
    </div>
  </div>
</body>
</html>

