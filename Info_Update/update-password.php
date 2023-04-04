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
        echo 'Connection error: ' . mysqli_connect_error();
    }

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
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
<img src="img/toolkit.jpg" alt="Toolkit" width="600" height="600">
    <div class = "container">
        <div class="main">

            <?php
                if (isset($_SESSION["update_success"])): 
            ?>

                <div class="success-message">
                    <h1>Password Reset Successful!</h1>
                    <p>Please re-login to you account using the new password.</p>
                
                    <div class="re-login">
                        <a href="../Login_register/welcome.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <p>Re-Login</p>
                        </a>
                    </div>
                </div>

            <?php 
                else: 
            ?>
            
            <a href="update-info.php" class="back-btn">&laquo; Back</a>

                <h1>Reset Password</h1>
                <br>

                <section>

                    <form action="update-password.php" , method="post">
                        <p>
                            <label for = "currentPassword">Current Password: </label>
                            <input type = "password" name = "currentPassword" id = "currentPassword" placeholder = "Enter you current password" required>
                        </p>
                        <p>
                            <label for="newPassword">New Password: </label>
                            <input type="password" name="newPassword" id="newPassword" placeholder="Enter your new password" required>
                        </p>
                        <p>
                            <label for="confirmPassword">Confirm New Password: </label>
                            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your new password" required>
                        </p>
                        <br>
                        <button type="submit">Update</button>
                        <button type="reset">Clear</button>
                    </form>

                </section>

                <?php if (isset($_SESSION["error"])): ?>
                    <section class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <h1>Password Reset Failed!</h1>

                        <?php if ($_SESSION["error"] == "password_incorrect"): ?>
                            <p>The current password isn't entered correctly.</p>
                        <?php elseif ($_SESSION["error"] == "same_password"): ?>
                            <p>The new password cannot be the same as the original password.</p>
                        <?php elseif ($_SESSION["error"] == "password_mismatch"): ?>
                            <p>New password mismatch. Please confirm you new password again.</p>
                        <?php else: ?>
                            <p>An error occured. Please try again later.</p>
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
