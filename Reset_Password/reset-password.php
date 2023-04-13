<?php
    session_start();

    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    require_once '../db_connection.php';

    if (isset($_GET["key"]) && isset($_GET["reset"])) {
        $_SESSION["email"] = $_GET["key"];
        $_SESSION["password"] = $_GET["reset"];
        $email = $_SESSION["email"];
        $oldPassword = $_SESSION["password"];

        $check_sql = "SELECT * FROM users WHERE email = '$email' AND password = '$oldPassword'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) < 1) {
            $_SESSION["error"] = "link_expire";
        }
    }
    elseif (!isset($_SESSION["email"]) || !isset($_SESSION["password"])) {
        $_SESSION["error"] = "key_not_passed";
    }
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = $_POST["newPassword"];
        $confirmPassword = $_POST["confirmPassword"];
        $email = $_SESSION["email"];
        $oldPassword = $_SESSION["password"];

        // $check_sql = "SELECT * FROM users WHERE email = '$email' AND password = '$oldPassword'";
        // $check_result = mysqli_query($conn, $check_sql);
        // $user = mysqli_fetch_assoc($check_result);

        // if (mysqli_num_rows($check_result) < 1) {
        //     $_SESSION["error"] = "link_expire";
        // }
        if (password_verify($newPassword, $oldPassword)) {
            $_SESSION["error"] = "same_password";
        }
        elseif ($newPassword != $confirmPassword) {
            $_SESSION["error"] = "password_mismatch";
        }
        else {
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = '$newPassword' WHERE email = '$email' AND password = '$oldPassword'";

            if (mysqli_query($conn, $sql)) {
                $_SESSION["reset_success"] = true;
                unset($_SESSION["email"]);
                unset($_SESSION["password"]);
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="./reset_pswd_style.css" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
</head>

<body>
    <div class = "container">
        <div class="main">

            <?php
                if (isset($_SESSION["error"])): 
            ?>
                <section class="prompt error-message">
                    <!-- <i class="fas fa-exclamation-circle"></i> -->
                    <?php if ($_SESSION["error"] == "key_not_passed"): ?>
                        <div class="title-error">An Error Occured</div>
                        <p>An Error Occured. Please request another link.</p>
                        <a href = "email.html" class = "btn">Request Another Link</a>

                    <?php elseif ($_SESSION["error"] == "link_expire"): ?>
                        <div class="title-error">An Error Occured</div>
                        <p>Your are probably using a wrong or expired link. Please request another link.</p>
                        <a href = "email.html" class = "btn">Request Another Link</a>

                    <?php elseif ($_SESSION["error"] == "same_password"): ?>
                        <div class="title-error">Reset Failed</div>
                        <p>New password cannot be the same as the current password. Please use another password.</p>
                        <a href = "reset-password.php" class = "btn">Try Again</a>

                    <?php elseif ($_SESSION["error"] == "password_mismatch"): ?>
                        <div class="title-error">Reset Failed</div>
                        <p>New password mismatch. Please confirm you new password again.</p>
                        <a href = "reset-password.php" class = "btn">Try Again</a>

                    <?php elseif ($_SESSION["error"] == "failed"): ?>
                        <div class="title-error"> Reset Failed</div>
                        <p>An error occured during resetting. Please try again later.</p>
                        <a href = "reset-password.php" class = "btn">Try Again</a>

                    <?php endif; ?>

                </section>

            <?php 
                elseif (isset($_SESSION["reset_success"])):
            ?>

                <section class="success-message">
                    <div class="title">Success!</div>

                    <p>Password Reset Successful!</p>
                    <p>Please use the new password to lgoin.<p>

                    <a href = "../Login_register/welcome.php" class = "btn">Login</a>
                </section>

            <?php 
                else:
            ?>

                <section>
                    <div class="title">Reset your password here</div>
                    <form action="reset-password.php" , method="post">
                        <div class="form-group">
                            <!-- <label for="newPassword">New Password: </label> -->
                            <input type="password" class = "form-control" name="newPassword" id="newPassword" placeholder="Enter your new password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="form-group">
                            <!-- <label for="confirmPassword">Confirm New Password: </label> -->
                            <input type="password" class = "form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm your new password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                        <br>
                        <button type="submit" class="btn">Update</button>
                        <button type="reset" class="btn">Clear</button>
                    </form>

                </section>

            <?php 
                endif;
                unset($_SESSION["reset_success"]);
            ?>

        </div>
    </div>
</body>

</html>