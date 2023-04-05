<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    session_start();

    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    require_once '../db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
      
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        $user = mysqli_fetch_assoc($check_result);

        if (mysqli_num_rows($check_result) < 1) {
            $_SESSION["error"] = "not_found";
        }

        else {
            $password = $user["password"];
            $link = "www.dooleyplanner.com/Reset_Password/reset-password.php?key=".$email."&reset=".$password;
            $username = $user["username"];
            $body = "
            <p>Hi $username,</p>
            <p>Please use the below link to reset your password.</p>
            <br>
            <p>$link</p>
            <br>
            <p>Best,</p>
            <p>Course Safari Team</p>";


            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();                       
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;

                $mail->Username = 'coursesafari@gmail.com';
                $mail->Password = 'yrxhczrxvmclhwsg';

                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('coursesafari@gmail.com', 'Course Safari');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Reset Password';
                $mail->Body = $body;

                $mail->send();
            } catch (Exception $e) {
                $_SESSION["error"] = "not_sent";
            }
        }
    }
    else {
        echo "Error: request not received, please try again.";
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
                    <h1>An Error Occured</h1>

                    <?php if ($_SESSION["error"] == "not_found"): ?>
                        <p>Account registered with <?php echo $email?> is not found. Please check your email address.</p>
                    <?php elseif ($_SESSION["error"] == "not_sent"): ?>
                        <p>An error occured. The reset link wasn't sent successfully. Please try again.</p>
                    <?php else: ?>
                        <p>An error occured. Please try again later.</p>
                    <?php endif; ?>

                    <a href = "email.html" class = "buttons">Try Again</a>
                </section>

            <?php 
                else: 
            ?>

                <section class="success-message">
                    <h1>Success!</h1>

                    <p>The reset link was sent to your through email successfully!</p>
                    <p>Please use the link to reset your password.<p>

                    <a href = "../Login_register/welcome.php" class = "buttons">Login</a>
                </section>

            <?php 
                endif;
            ?>

        </div>
    </div>
</body>

</html>