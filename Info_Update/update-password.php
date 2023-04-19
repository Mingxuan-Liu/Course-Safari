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
    <link rel="stylesheet" href="./info_update.css" type="text/css">
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
                    <div class="title">Password Reset Sucessful!</div>
                    <p>Please re-login to you account using the new password.</p>
                
                    <div class="re-login">
                        <a href="../Login_register/welcome.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <!-- <p>Re-Login</p> -->
                        </a>
                    </div>
                </div>

            <?php 
                else: 
            ?>
            
            <a href="update-info.php" class="back-btn-update">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="title">Reset Password</div>

                <br>

            <section>

                <form action="update-password.php" , method="post">
                    <n>
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
                        <br>
                        <button type="submit" class="btn">Update</button>
                        <button type="reset" class="btn">Clear</button>
                    </n>
                </form>

            </section>

                <?php if (isset($_SESSION["error"])): ?>
                    <section class="error-message">
                        <!-- <i class="fas fa-exclamation-circle"></i> -->
                        <!-- <h1>Password Reset Failed!</h1> -->

                        <?php if ($_SESSION["error"] == "password_incorrect"): ?>
                            <div class="error">The current password isn't entered correctly.</div>
                        <?php elseif ($_SESSION["error"] == "same_password"): ?>
                            <div class="error">The new password cannot be the same as the original password.</div>
                        <?php elseif ($_SESSION["error"] == "password_mismatch"): ?>
                            <div class="error">New password mismatch. Please confirm you new password again.</div>
                        <?php else: ?>
                            <div class="error">An error occured. Please try again later.</div>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

            <?php 
                endif;
                unset($_SESSION["update_success"]);
            ?>

        </div>
    </div>
</body>

</html>
