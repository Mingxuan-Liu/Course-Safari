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
        <div class="container">
            <div class="main">
                <a href="../Login_register/user.php" class="back-btn">Back</a>
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
                </div>

                <?php $user_id = $_SESSION["user_id"]; ?>
                <?php
                    $sql = "SELECT * FROM courses_taken WHERE user_id = '$user_id'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) == 0):
                ?>
                        <h2>You have not declared your major. Please declare it <a href="../Degree_tracker/declaration.php">here</a>.</h2>
                <?php
                    else:
                        echo nl2br("<h2>"."You can see all major courses"."\n"."you have already taken from below."."</h2>");
                    endif;
                ?>

                <?php
                    $sql = "SELECT DISTINCT major_name FROM courses_taken WHERE user_id = '$user_id'";
                    $result = mysqli_query($conn, $sql);
                    $major_names = array();
                    while ($obj = mysqli_fetch_assoc($result)) {
                        $major_names[] = $obj['major_name'];
                    }

                    for ($x=0; $x<count($major_names); $x++) {
                        $temp_major = $major_names[$x];
                        echo "<h3>"."$temp_major"."</h3>";

                        $sql = "SELECT * FROM courses_taken 
                        WHERE user_id = '$user_id' AND major_name = '$temp_major' 
                        ORDER BY course_num";
                        $result = mysqli_query($conn, $sql);

                        while ($obj = mysqli_fetch_assoc($result)) {
                            $temp_str = $obj['course_prefix']." ".$obj['course_num'].": ".$obj['course_name'];
                            if ($obj['required'] == 1):
                                $temp_str = $temp_str." (required)";
                            else:
                                $temp_str = $temp_str." (elective)";
                            endif;
                            echo "<h4>".$temp_str."</h4>";
                        }
                    }
                ?>
            </div>
        </div>
    </body>
</html>