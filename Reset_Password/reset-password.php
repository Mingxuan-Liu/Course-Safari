<?php
    session_start();

    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    require_once '../db_connection.php';

    if ($_GET["key"] && $_GET["reset"]) {
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
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
    <div class = "container">
        <div class="main">

            <?php
                if (isset($_SESSION["error"])): 
            ?>
                <section class="error-message">
                    <i class="fas fa-exclamation-circle"></i>

                    <?php if ($_SESSION["error"] == "key_not_passed"): ?>
                        <h1>An Error Occured</h1>
                        <p>An Error Occured. Please request another link.</p>
                        <a href = "email.html" class = "buttons">Request Another Link</a>

                    <?php elseif ($_SESSION["error"] == "link_expire"): ?>
                        <h1>An Error Occured</h1>
                        <p>Your are probably using a wrong or expired link. Please request another link.</p>
                        <a href = "email.html" class = "buttons">Request Another Link</a>

                    <?php elseif ($_SESSION["error"] == "same_password"): ?>
                        <h1>Reset Failed</h1>
                        <p>New password cannot be the same as the current password. Please use another password.</p>
                        <a href = "reset-password.php" class = "buttons">Try Again</a>

                    <?php elseif ($_SESSION["error"] == "password_mismatch"): ?>
                        <h1>Reset Failed</h1>
                        <p>New password mismatch. Please confirm you new password again.</p>
                        <a href = "reset-password.php" class = "buttons">Try Again</a>

                    <?php elseif ($_SESSION["error"] == "failed"): ?>
                        <h1> Reset Failed</h1>
                        <p>An error occured during resetting. Please try again later.</p>
                        <a href = "reset-password.php" class = "buttons">Try Again</a>

                    <?php endif; ?>

                </section>

            <?php 
                elseif (isset($_SESSION["reset_success"])):
            ?>

                <section class="success-message">
                    <h1>Success!</h1>

                    <p>Password Reset Successful!</p>
                    <p>Please use the new password to lgoin.<p>

                    <a href = "../Login_register/welcome.php" class = "buttons">Login</a>
                </section>

            <?php 
                else:
            ?>

                <section>

                    <form action="reset-password.php" , method="post">
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

            <?php 
                endif;
                unset($_SESSION["reset_success"]);
            ?>

        </div>
    </div>
</body>

</html>