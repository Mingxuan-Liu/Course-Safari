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
    }

    if ($_SESSION['primary_major'] != "None") {
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
    }

    if ($_SESSION['secondary_major'] != "None") {
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
    }

    if ($_SESSION['minor'] != "None") {
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
    }

    $_SESSION["submit_success"] = true;
}

// add a major_name column into the database
// Check if the major_name column already exists
$check_column_sql = "SELECT COUNT(*)
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE table_schema = '$dbname'
                     AND table_name = 'major_minor'
                     AND column_name = 'major_name'";
$column_exists_result = mysqli_query($conn, $check_column_sql);
$column_exists = mysqli_fetch_array($column_exists_result)[0];

// If the major_name column doesn't exist, add it
if (!$column_exists) {
    $add_major_name_sql = "ALTER TABLE major_minor ADD major_name VARCHAR(255)";
    // handle the error when adding a new column into the table
    if (!mysqli_query($conn, $add_major_name_sql)) {
        echo "Error adding major_name column: " . mysqli_error($conn);
    }
}

// Update the major_name column based on the major attribute
$update_major_name_sql = "
    UPDATE major_minor
    SET major_name = CASE
        WHEN major = 1 THEN CONCAT(degree, ' in ', subject_name)
        ELSE CONCAT('Minor in ', subject_name)
    END;
";
// handle the error when updating the table
if (!mysqli_query($conn, $update_major_name_sql)) {
    echo "Error updating major_name column: " . mysqli_error($conn);
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
                    <a href="course_progress.php" class="btn">Check Your Academic Progress</a>
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