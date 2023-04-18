<?php 
session_start();

require_once '../db_connection.php';

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Tianjun Zhong">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This page allows users to update their Course Safari account information.">
        <title>Update My Information</title>
        <link rel="stylesheet" href="./info_update.css" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    </head>

    <body>
            <div class="main">
            <a href="../Login_register/user.php" class="back-btn"> <!--Back-->
                <i class="fas fa-arrow-left"></i>
            </a>
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
