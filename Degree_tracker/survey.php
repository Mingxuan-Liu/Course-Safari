<?php
session_start();

if (isset($_SESSION["error"])) {
    unset($_SESSION["error"]);
}

require_once '../db_connection.php';

$primary_subject_name = substr($_SESSION['primary_major'], 6, 100);
$primary_degree_name = substr($_SESSION['primary_major'], 0, 2);
$secondary_subject_name = substr($_SESSION['secondary_major'], 6, 100);
$secondary_degree_name = substr($_SESSION['secondary_major'], 0, 2);
$minor_subject_name = substr($_SESSION['minor'], 9, 100);

function array_has_dupes($array) {
    return count($array) !== count(array_unique($array));
 }

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT * FROM courses_taken WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $sql = "DELETE FROM courses_taken WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
    }

    $sql = "SELECT * FROM electives_taken WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $sql = "DELETE FROM electives_taken WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
    }

    if ($_SESSION['primary_major'] != "None") {
        if ($_POST['primary_major'] != NULL) {
            $major_name =  $_SESSION['primary_major'];
            foreach ($_POST['primary_major'] as $item) {
                $course_prefix = explode(" ", $item)[0];
                $course_num = explode(" ", $item)[1];
                $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                        SELECT '$user_id', '$major_name', course_prefix, course_num, course_name, required 
                        FROM major_minor 
                        WHERE subject_name = '$primary_subject_name' AND degree = '$primary_degree_name' AND 
                            course_prefix = '$course_prefix' AND course_num = $course_num";
                mysqli_query($conn, $sql);
            }

            $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $total_electives = mysqli_fetch_array($result)[0];
            $sql = "SELECT COUNT(*) FROM courses_taken 
                    WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
            $result = mysqli_query($conn, $sql);
            $electives_taken = mysqli_fetch_array($result)[0];
            $ready_for_insert = $total_electives - $electives_taken;
            $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                    VALUES ('$user_id', '$major_name', '$ready_for_insert')";
            mysqli_query($conn, $sql);
        }
    }

    if ($_SESSION['secondary_major'] != "None") {
        if ($_POST['secondary_major'] != NULL) {
            $major_name =  $_SESSION['secondary_major'];
            foreach ($_POST['secondary_major'] as $item) {
                $course_prefix = explode(" ", $item)[0];
                $course_num = explode(" ", $item)[1];
                $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                        SELECT '$user_id', '$major_name', course_prefix, course_num, course_name, required 
                        FROM major_minor 
                        WHERE subject_name = '$secondary_subject_name' AND degree = '$secondary_degree_name' AND 
                            course_prefix = '$course_prefix' AND course_num = $course_num";
                mysqli_query($conn, $sql);
            }

            $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $total_electives = mysqli_fetch_array($result)[0];
            $sql = "SELECT COUNT(*) FROM courses_taken 
                    WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
            $result = mysqli_query($conn, $sql);
            $electives_taken = mysqli_fetch_array($result)[0];
            $ready_for_insert = $total_electives - $electives_taken;
            $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                    VALUES ('$user_id', '$major_name', '$ready_for_insert')";
            mysqli_query($conn, $sql);
        }
    }

    if ($_SESSION['minor'] != "None") {
        if ($_POST['minor'] != NULL) {
            $major_name =  $_SESSION['minor'];
            foreach ($_POST['minor'] as $item) {
                $course_prefix = explode(" ", $item)[0];
                $course_num = explode(" ", $item)[1];
                $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                        SELECT '$user_id', '$major_name', course_prefix, course_num, course_name, required 
                        FROM major_minor 
                        WHERE subject_name = '$minor_subject_name' AND degree IS NULL AND 
                            course_prefix = '$course_prefix' AND course_num = $course_num";
                mysqli_query($conn, $sql);
            }

            $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $total_electives = mysqli_fetch_array($result)[0];
            $sql = "SELECT COUNT(*) FROM courses_taken 
                    WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
            $result = mysqli_query($conn, $sql);
            $electives_taken = mysqli_fetch_array($result)[0];
            $ready_for_insert = $total_electives - $electives_taken;
            $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                    VALUES ('$user_id', '$major_name', '$ready_for_insert')";
            mysqli_query($conn, $sql);
        }
    }



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
                    <a href="../Info_Update/course_progress.php" class="btn">Check Your Academic Progress</a>
                </div>
            <?php
                else:
            ?>
                <h2>Please click on the courses you have already taken from below.</h2>
                <form action="survey.php" method="post">
                    <?php
                        if ($_SESSION['primary_major'] != "None"):
                            echo "<h3>" . "Primary Major: " . $_SESSION['primary_major'] . "</h3>";

                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$primary_subject_name' AND degree = '$primary_degree_name' AND major IS TRUE AND required IS TRUE 
                                        ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the courses required by your primary major.". "</h4>";
                            else:
                                echo "<h4>". "You do not have courses required by your primary major.". "</h4>";
                            endif;
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="primary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                            
                        <?php
                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                            WHERE subject_name = '$primary_subject_name' AND degree = '$primary_degree_name' AND major IS TRUE AND required IS FALSE 
                            ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the elective courses in your primary major.". "</h4>";
                            else:
                                echo "<h4>". "You do not have elective courses in your primary major.". "</h4>";
                            endif;
                        ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="primary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                    <?php 
                        endif; 
                    ?>
                    <?php
                        if ($_SESSION['secondary_major'] != "None"):
                            echo "<h3>" . "Secondary Major: " . $_SESSION['secondary_major'] . "</h3>";

                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$secondary_subject_name' AND degree = '$secondary_degree_name' AND major IS TRUE AND required IS TRUE 
                                        ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the courses required by your secondary major.". "</h4>";
                            else:
                                echo "<h4>". "You do not have courses required by your secondary major.". "</h4>";
                            endif;
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="secondary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>

                        <?php
                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                            WHERE subject_name = '$secondary_subject_name' AND degree = '$secondary_degree_name' AND major IS TRUE AND required IS FALSE 
                            ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the elective courses in your secondary major.". "</h4>";
                            else:
                                echo "<h4>". "You do not have elective courses in your secondary major.". "</h4>";
                            endif;
                        ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="secondary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>
                    <?php 
                        endif; 
                    ?>
                    <?php
                        if ($_SESSION['minor'] != "None"):
                            echo "<h3>" . "Minor: " . $subject_name . "</h3>";

                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                                        WHERE subject_name = '$minor_subject_name' AND degree IS NULL AND major IS FALSE AND required is TRUE 
                                        ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the courses required by your minor.". "</h4>";
                            else:
                                echo "<h4>". "You do not have courses required by your minor.". "</h4>";
                            endif;
                    ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="minor[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php } ?>

                        <?php
                            $sql = "SELECT course_prefix, course_num, course_name FROM major_minor 
                            WHERE subject_name = '$minor_subject_name' AND degree IS NULL AND major IS FALSE AND required is FALSE 
                            ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0):
                                echo "<h4>". "These are the elective courses in your minor.". "</h4>";
                            else:
                                echo "<h4>". "You do not have elective courses in your minor.". "</h4>";
                            endif;
                        ?>
                            <?php while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="minor[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num'];?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
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