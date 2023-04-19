<?php
session_start();

require_once '../db_connection.php';

$user_id = $_SESSION["user_id"];

$sql = "SELECT DISTINCT major_name FROM courses_taken WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$temp_list = array();
while ($obj = mysqli_fetch_assoc($result)) {
    array_push($temp_list, $obj['major_name']);
}

if (count($temp_list) == 0) {
    $non_declare = true;
}
elseif (count($temp_list) == 1) {
    $_SESSION['primary_major'] = $temp_list[0];
    $_SESSION['secondary_major'] = 'None';
    $_SESSION['minor'] = 'None';
}
elseif (count($temp_list) == 2) {
    $_SESSION['primary_major'] = $temp_list[0];
    if (str_contains($temp_list[1], 'Minor')) {
        $_SESSION['secondary_major'] = 'None';
        $_SESSION['minor'] = $temp_list[1];
    }
    else {
        $_SESSION['secondary_major'] = $temp_list[1];
        $_SESSION['minor'] = 'None';
    }
}
else {
    $_SESSION['primary_major'] = $temp_list[0];
    $_SESSION['secondary_major'] = $temp_list[1];
    $_SESSION['minor'] = $temp_list[2];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "SELECT * FROM electives_taken WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $sql = "DELETE FROM electives_taken WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
    }

    if (isset($_POST['drop_primary_major'])) {
        $major_name = $_SESSION['primary_major'];
        foreach ($_POST['drop_primary_major'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "DELETE FROM courses_taken 
                    WHERE user_id = '$user_id' AND course_prefix = '$temp_prefix' AND course_num = '$temp_num' 
                    AND major_name = '$major_name'";
            mysqli_query($conn, $sql);
        }
    }

    if (isset($_POST['drop_secondary_major'])) {
        $major_name = $_SESSION['secondary_major'];
        foreach ($_POST['drop_secondary_major'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "DELETE FROM courses_taken 
                    WHERE user_id = '$user_id' AND course_prefix = '$temp_prefix' AND course_num = '$temp_num' 
                    AND major_name = '$major_name'";
            mysqli_query($conn, $sql);
        }
    }

    if (isset($_POST['drop_minor'])) {
        $major_name = $_SESSION['minor'];
        foreach ($_POST['drop_minor'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "DELETE FROM courses_taken 
                    WHERE user_id = '$user_id' AND course_prefix = '$temp_prefix' AND course_num = '$temp_num' 
                    AND major_name = '$major_name'";
            mysqli_query($conn, $sql);
        }
    }

    if (isset($_POST['add_primary_major'])) {
        $major_name = $_SESSION['primary_major'];
        foreach ($_POST['add_primary_major'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "SELECT DISTINCT course_name, required
                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                    AND major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $temp_name = $result['course_name'];
            $temp_required = $result['required'];
            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
            mysqli_query($conn, $sql);
        }
    }

    if (isset($_POST['add_secondary_major'])) {
        $major_name = $_SESSION['secondary_major'];
        foreach ($_POST['add_secondary_major'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "SELECT DISTINCT course_name, required
                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                    AND major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $temp_name = $result['course_name'];
            $temp_required = $result['required'];
            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
            mysqli_query($conn, $sql);
        }
    }

    if (isset($_POST['add_minor'])) {
        $major_name = $_SESSION['minor'];
        foreach ($_POST['add_minor'] as $item) {
            $temp_prefix = explode(" ", $item)[0];
            $temp_num = explode(" ", $item)[1];
            $sql = "SELECT DISTINCT course_name, required
                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                    AND major_name = '$major_name'";
            $result = mysqli_query($conn, $sql);
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $temp_name = $result['course_name'];
            $temp_required = $result['required'];
            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
            mysqli_query($conn, $sql);
        }
    }

    if ($_SESSION['primary_major'] != "None") {
        $major_name =  $_SESSION['primary_major'];
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

    if ($_SESSION['secondary_major'] != "None") {
        $major_name =  $_SESSION['secondary_major'];
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

    if ($_SESSION['minor'] != "None") {
        $major_name =  $_SESSION['minor'];
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

    $success_declare = true;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link rel="stylesheet" href="add_drop_style_final.css">
  <title>Add/Drop to Course Safari</title>
</head>

<body>
    <div class='container'>
        <div class='main'>
            <?php if (isset($non_declare)): ?>
                <div class="success-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <h1>You have not declared your major!</h1>
                    <a href="../Degree_tracker/declaration.php" class="btn">Declare it here.</a>
                </div>
            <?php elseif (isset($success_declare)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h1>Add/drop success!</h1>
                    <a href="../Info_Update/course_progress.php" class="btn">Check Your Academic Progress</a>
                </div>
            <?php else: ?>
                <form action="add_drop_courses.php" method="post">
                    <h2>Here are the courses you have already taken. Check the box to drop it.</h2>
                    
                    <?php
                        if ($_SESSION['primary_major'] != "None"):
                            echo "<h3>" . "Primary Major: " . $_SESSION['primary_major'] . "</h3>";
                            $major_name = $_SESSION['primary_major'];
                            $sql = "SELECT course_prefix, course_num, course_name 
                                    FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name'
                                    ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have not taken any courses in your primary major.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="drop_primary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>

                    <?php
                        if ($_SESSION['secondary_major'] != "None"):
                            echo "<h3>" . "Secondary Major: " . $_SESSION['secondary_major'] . "</h3>";
                            $major_name = $_SESSION['secondary_major'];
                            $sql = "SELECT course_prefix, course_num, course_name 
                                    FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name'
                                    ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have not taken any courses in your secondary major.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="drop_secondary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>

                    <?php
                        if ($_SESSION['minor'] != "None"):
                            echo "<h3>" . "Minor: " . $_SESSION['minor'] . "</h3>";
                            $major_name = $_SESSION['minor'];
                            $sql = "SELECT course_prefix, course_num, course_name 
                                    FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name'
                                    ORDER BY course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have not taken any courses in your minor.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="drop_minor[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>

                    <h2>Here are the courses you not yet taken. Check the box to add it.</h2>

                    <?php
                        if ($_SESSION['primary_major'] != "None"):
                            echo "<h3>" . "Primary Major: " . $_SESSION['primary_major'] . "</h3>";
                            $major_name = $_SESSION['primary_major'];
                            $sql = "SELECT * FROM 
                                    ((SELECT course_prefix, course_num, course_name FROM major_minor WHERE major_name = '$major_name')
                                    EXCEPT
                                    (SELECT course_prefix, course_num, course_name FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name')) AS subq
                                    ORDER BY subq.course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have taken all courses in your primary major.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="add_primary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>

                    <?php
                        if ($_SESSION['secondary_major'] != "None"):
                            echo "<h3>" . "Secondary Major: " . $_SESSION['secondary_major'] . "</h3>";
                            $major_name = $_SESSION['secondary_major'];
                            $sql = "SELECT * FROM 
                                    ((SELECT course_prefix, course_num, course_name FROM major_minor WHERE major_name = '$major_name')
                                    EXCEPT
                                    (SELECT course_prefix, course_num, course_name FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name')) AS subq
                                    ORDER BY subq.course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have taken all courses in your secondary major.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="add_secondary_major[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>

                    <?php
                        if ($_SESSION['minor'] != "None"):
                            echo "<h3>" . "Minor Major: " . $_SESSION['minor'] . "</h3>";
                            $major_name = $_SESSION['minor'];
                            $sql = "SELECT * FROM 
                                    ((SELECT course_prefix, course_num, course_name FROM major_minor WHERE major_name = '$major_name')
                                    EXCEPT
                                    (SELECT course_prefix, course_num, course_name FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$major_name')) AS subq
                                    ORDER BY subq.course_num";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) == 0):
                                echo "<h4>". "You have taken all courses in your minor.". "</h4>";
                            else:
                                while ($obj = mysqli_fetch_assoc($result)) { ?>
                                <label class="survey-item">
                                    <input type="checkbox" name="add_minor[]" value="<?php echo $obj['course_prefix']." ".$obj['course_num']; ?>">
                                    <span class="checkmark"></span>
                                    <?php echo $obj['course_prefix']." ".$obj['course_num'].": ".$obj["course_name"]; ?>
                                </label>
                            <?php }
                            endif;
                        endif;
                    ?>
                    
                <input type="submit" value="Submit" class="btn">
            <?php endif; ?>
        </div>
    </div>
</body>