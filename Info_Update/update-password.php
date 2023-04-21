<?php
    session_start();

    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    require_once '../db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $currentPassword = $_POST["currentPassword"];
        $newPassword = $_POST["newPassword"];
        $confirmPassword = $_POST["confirmPassword"];
        $username = $_SESSION["username"];
      
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $check_result = mysqli_query($conn, $check_sql);
        $user = mysqli_fetch_assoc($check_result);

        if (!password_verify($currentPassword, $user["password"])) {
            $_SESSION["error"] = "password_incorrect";
        } elseif ($currentPassword == $newPassword) {
            $_SESSION["error"] = "same_password";
        } elseif ($newPassword != $confirmPassword) {
            $_SESSION["error"] = "password_mismatch";
        } else {
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = '$newPassword' WHERE username = '$username' OR email = '$username'";

            if (mysqli_query($conn, $sql)) {
                $_SESSION["update_success"] = true;
            } else {
                $_SESSION["error"] = "failed";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Tianjun Zhong">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page allows users to update their Course Safari usernames.">
    <title>Update Username</title>
    <link rel="stylesheet" href="./update_pwd.css" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
</head>

<body>
<!-- <img src="img/toolkit.jpg" alt="Toolkit" width="600" height="600"> -->
    <div class = "container">
        <div class="main">

            <?php
                if (isset($_SESSION["update_success"])): 
            ?>

                <div class="success-message">
                    <h2>Password Reset Sucessful!</h2>
                    <p>Please re-login to you account using the new password.</p>
                
                    <div class="update_clear">
                        <a href="../Login_register/welcome.php" class="btn_uc">
                            Re-Login
                        </a>
                    </div>
                </div>

            <?php 
                else: 
            ?>
            
                <a href="update-info.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <h1>Reset Password</h1>

                <?php if (isset($_SESSION["error"])): ?>
                    <?php if ($_SESSION["error"] == "password_incorrect"): ?>
                        <p class="error">The current password isn't entered correctly.</p>
                    <?php elseif ($_SESSION["error"] == "same_password"): ?>
                        <p class="error">The new password cannot be the same as the original password.</p>
                    <?php elseif ($_SESSION["error"] == "password_mismatch"): ?>
                        <p class="error">New password mismatch. Please confirm you new password again.</p>
                    <?php else: ?>
                        <p class="error">An error occured. Please try again later.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="update-password.php" , method="post">

                    <div class="form-group">
                        <!-- <label for = "currentPassword">Current Password: </label> -->
                        <input type = "password" class="form-control" name = "currentPassword" id = "currentPassword" placeholder = "Your current password" required>
                        <!-- <i class="fas fa-lock"></i> -->
                    </div>
                    <div class="form-group">
                        <!-- <label for="newPassword">New Password: </label> -->
                        <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Your new password" required>
                        <!-- <i class="fas fa-lock"></i> -->
                    </div>
                    <div class="form-group">
                        <!-- <label for="confirmPassword">Confirm New Password: </label> -->
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm your new password" required>
                        <!-- <i class="fas fa-lock"></i> -->
                    </div>

                    <div class="update_clear">
                        <button type="submit" class="btn_uc">Update</button>
                        <button type="reset" class="btn_uc">Clear</button>
                    </div>

                </form>

            <?php 
                endif;
                unset($_SESSION["update_success"]);
            ?>

        </div>
    </div>
</body>

</html>
