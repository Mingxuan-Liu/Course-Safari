<?php
    session_start();

    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    require_once '../db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newUsername = $_POST["newUsername"];
      
        $check_sql = "SELECT * FROM users WHERE username = '$newUsername' OR email = '$newUsername'";
        $check_result = mysqli_query($conn, $check_sql);
      
        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION["error"] = "existing_1";
        } else {
            $oldUsername = $_SESSION["username"];
            $sql = "UPDATE users SET username = '$newUsername' WHERE username = '$oldUsername'";

            if (mysqli_query($conn, $sql)) {
                $_SESSION["update_success"] = true;
                $_SESSION["username"] = $newUsername;
            } else {
                echo 'query error: '. mysqli_error($conn);
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
<!-- <img src="img/toolkit.jpg" alt="Toolkit" width="300" height="300"> -->
    <div class = "container">
        <div class="main">
            <a href="update-info.php" class="back-btn-update">
                <i class="fas fa-arrow-left"></i>
            </a>
    
            <?php
                if (isset($_SESSION["update_success"])): 
            ?>

                <div class="success-message">
                    <div class="title">Username Update Successful!</div>
                    <p>Your username is now: <?php echo $_SESSION["username"]; ?></p>
                
                    <!-- <div class="back-to-user">
                        <a href="../Login_register/user.php" class="back-btn">
                            <i class="fas fa-user"></i>
                        </a>
                    </div> -->
                </div>

            <?php 
                else: 
            ?>
            

            <div class="title">
                Update Username
            </div>
            
            <br>

            <section>

                <p>Current Username:
                    <?php echo $_SESSION["username"]; ?>
                </p>

                <form action="update-username.php" , method="post">
                    <n>
                        <!-- <label for="newUsename">New Username: </label> -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="newUsername" id="newUsername" placeholder="Enter your new username" required>
                            <i class="fas fa-user"></i>
                        <div>
                    </n>
                    <br>
                    <button type="submit" class="btn">Update</button>
                    <button type="reset" class="btn">Clear</button>
                </form>

            </section>

                <?php 
                if (isset($_SESSION["error"])): 
                    if ($_SESSION["error"] == "existing_1"):
                ?>
                        <section>
                            <!-- <i class="fas fa-exclamation-circle"></i> -->
                            <div class="error">Update Failed! Username already exists</div>
                        </section>
                    <?php endif; ?>
                <?php endif; ?>

            <?php 
                endif;
                unset($_SESSION["update_success"]);
            ?>

        </div>
    </div>
</body>

</html>
