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
    die("Connection failed: " . mysqli_connect_error());
}

$primary_subject_name = substr($_SESSION['primary_major'], 6, 100);
$primary_degree_name = substr($_SESSION['primary_major'], 0, 2);
$secondary_subject_name = substr($_SESSION['secondary_major'], 6, 100);
$secondary_degree_name = substr($_SESSION['secondary_major'], 0, 2);
$minor_subject_name = substr($_SESSION['minor'], 9, 100);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT * FROM courses_taken WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $sql = "DELETE FROM courses_taken WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM courses_totake WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
    }

    if ($_SESSION['secondary_major'] != "None" && $_SESSION['minor'] == "None") {
        $array_all = array_unique(array_merge($_POST['primary_major'], $_POST['secondary_major']));
    }
    elseif ($_SESSION['secondary_major'] != "None" && $_SESSION['minor'] != "None") {
        $array_all = array_unique(array_merge($_POST['primary_major'], $_POST['secondary_major'], $_POST['minor']));
    }
    elseif ($_SESSION['primary_major'] != "None" && $_SESSION['minor'] != "None") {
        $array_all = array_unique(array_merge($_POST['primary_major'], $_POST['minor']));
    }
    else {
        $array_all = $_POST['primary_major'];
    }

    foreach ($array_all as $item) {
        $sql = "INSERT INTO courses_taken (user_id, course_num, course_name) 
                    SELECT '$user_id', '$item', course_name 
                    FROM major_minor 
                    WHERE course_num = '$item' 
                    LIMIT 1";
        mysqli_query($conn, $sql);
    }
    
    $sql = "INSERT INTO courses_totake (user_id, course_num, course_name) 
                SELECT DISTINCT '$user_id', course_num, course_name 
                FROM major_minor 
                WHERE ((degree = '$primary_degree_name' AND subject_name = '$primary_subject_name') OR 
                    (degree = '$secondary_degree_name' AND subject_name = '$secondary_subject_name') OR 
                    (degree IS NULL AND subject_name = '$minor_subject_name')) AND 
                    (course_num NOT IN (SELECT course_num FROM courses_taken))";
    mysqli_query($conn, $sql);

    $_SESSION["submit_success"] = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link rel="stylesheet" href="degree_tracker_style.css">
  <title>Survey to Course Safari</title>
</head>
<body>
    <div class="container">
        <div class="main">
            <?php
                if (isset($_SESSION["submit_success"])):
            ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h1>Submission Successful!</h1>
                    <a href="../Login_register/user.php" class="btn">Go Back to the User Page</a>
                </div>
            <?php
                else:
            ?>
                <h2>Please click on the courses you have taken already from below.</h2>
                <form action="survey.php" method="post">
                    <?php
                        if ($_SESSION['primary_major'] != "None"):
                            echo "<h3>" . "Primary Major: " . $_SESSION['primary_major'] . "</h3>";
                            $sql = "SELECT course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$primary_subject_name' AND degree = '$primary_degree_name' AND major IS TRUE";
                            $result = mysqli_query($conn, $sql);
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="primary_major[]" value="<?php echo $obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj["course_num"].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                    <?php 
                        endif; 
                    ?>
                    <?php
                        if ($_SESSION['secondary_major'] != "None"):
                            echo "<h3>" . "Secondary Major: " . $_SESSION['secondary_major'] . "</h3>";
                            $sql = "SELECT course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$secondary_subject_name' AND degree = '$secondary_degree_name' AND major IS TRUE";
                            $result = mysqli_query($conn, $sql);
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="secondary_major[]" value="<?php echo $obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj["course_num"].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                    <?php 
                        endif; 
                    ?>
                    <?php
                        if ($_SESSION['minor'] != "None"):
                            echo "<h3>" . "Minor: " . $subject_name . "</h3>";
                            $sql = "SELECT course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$minor_subject_name' AND degree IS NULL AND major IS FALSE";
                            $result = mysqli_query($conn, $sql);
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="minor[]" value="<?php echo $obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj["course_num"].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                    <?php 
                        endif; 
                    ?>
                    <input type="submit" value="Submit" class="btn">
                </form>
            <?php
                endif;
                unset($_SESSION["submit_success"]);
            ?>
        </div>
    </div>
</body>
</html>