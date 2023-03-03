<?php session_start(); ?>

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
        <div class="container">
            <div class="main">
                <h1>Update My Information</h1>
                <p>Customize your username or reset your password</p>

                <a href="../Login_register/user.php" class="back-btn">Back</a>

                <div class="buttons">
                    <a href="update-username.php" class="btn btn-big btn-circle">
                        <i class="fas fa-user"></i>
                        <p>Update My Username</p>
                    </a>

                    <a href="update-password.php" class="btn btn-big btn-circle">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Reset My Password</p>
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>