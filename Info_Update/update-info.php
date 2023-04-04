<?php 
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Tianjun Zhong">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This page allows users to update their Course Safari account information.">
        <title>Update My Information</title>
        <link rel="stylesheet" href="style.css" type="text/css">
    </head>

    <body>
            <div class="main">
            <a href="../Login_register/user.php" class="back-btn">&laquo; Back</a>
                <h1>User Home Page</h1>

                <div class="buttons">
                    <a href="update-username.php" class="btn btn-big btn-circle">
                        <i class="fas fa-user"></i>
                        <p>Update My Username</p>
                    </a>

                    <a href="update-password.php" class="btn btn-big btn-circle">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Reset My Password</p>
                    </a>

                    <a href="course_progress.php" class="btn btn-big btn-circle">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Degree Tracker</p>
                    </a>
                </div>
            </div>
    </body>
</html>
