<?php
session_start();

require_once '../db_connection.php';

$user_id = $_SESSION["user_id"];

if (isset($_GET["major"])) {
    $clickedName = $_GET["major"];
    echo "<h2>" . "Course Requirements" . "</h2>";
    // Calculate the total number of required & elective courses and the number of completed courses
    $total_required_courses_sql = "SELECT COUNT(*) FROM major_minor WHERE major_name = '$clickedName' AND required IS TRUE";
    $total_elective_courses_sql = "SELECT num_electives FROM electives WHERE major_name = '$clickedName'";
    $completed_courses_sql = "SELECT COUNT(*) FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$clickedName'";

    $total_required_courses_result = mysqli_query($conn, $total_required_courses_sql);
    $total_elective_courses_result = mysqli_query($conn, $total_elective_courses_sql);
    $completed_courses_result = mysqli_query($conn, $completed_courses_sql);

    $total_required_courses = mysqli_fetch_array($total_required_courses_result)[0];
    $total_elective_courses = mysqli_fetch_array($total_elective_courses_result)[0];
    $completed_courses = mysqli_fetch_array($completed_courses_result)[0];
    
    // Calculate the completion percentage
    $completion_percentage = ($completed_courses / ($total_required_courses + $total_elective_courses)) * 100;

    if ($completion_percentage > 100) {
        $completion_percentage = 100;
    }

    // Display the progress bar
    echo "<div class='progress-container'>";
    echo "<div class='completed' style='width: " . $completion_percentage . "%;'></div>";
    echo "<span class='progress-percentage'>" . round($completion_percentage, 2) . "%</span>";
    echo "</div>";

    // Start the courses container
    echo "<div class='courses-container'>";
    // Retrieve required major courses that have not been taken yet
    $not_taken_sql = "(SELECT course_prefix, course_num, course_name
                        FROM major_minor 
                        WHERE major_name = '$clickedName' AND required IS TRUE
                        ORDER BY course_num)
                      EXCEPT
                      (SELECT course_prefix, course_num, course_name
                        FROM courses_taken 
                        WHERE user_id = '$user_id')";
    $not_taken = mysqli_query($conn, $not_taken_sql);

    while ($not_taken_obj = mysqli_fetch_assoc($not_taken)) {
        $not_taken_str = $not_taken_obj['course_prefix'] . " " . $not_taken_obj['course_num'] . ": " . $not_taken_obj['course_name'];
        echo "<div class='not-taken-block'>";
        echo "<h4>" . $not_taken_str . "</h4>";
        $course_tag = "";
        echo "<span class='need-tag'>" . "Not Taken" . "</span>";
        echo "</div>";
    }

    // Retrieve major courses that have been taken already
    $sql = "SELECT course_prefix, course_num, course_name
                FROM courses_taken
                WHERE user_id = '$user_id' AND major_name = '$clickedName'
                ORDER BY course_num";
    $taken = mysqli_query($conn, $sql);
    
    while ($obj = mysqli_fetch_assoc($taken)) {
        $taken_str = $obj['course_prefix'] . " " . $obj['course_num'] . ": " . $obj['course_name'];
        echo "<div class='taken-block'>";
        echo "<h4>" . $taken_str . "</h4>";
        $course_tag = "";
        echo "<span class='complete-tag'>" . "Completed" . "</span>";
        echo "</div>";
    }
    echo "</div>"; // Close the courses container

    if ($completion_percentage == 100) {
        echo "Congratulations! You have completed this major!";
    }
    else {
        $sql = "SELECT electives_left FROM electives_taken WHERE user_id = '$user_id' AND major_name = '$clickedName'";
        $result = mysqli_query($conn, $sql);
        $ready_for_print = mysqli_fetch_array($result)[0];
        echo "You have "."$ready_for_print"." electives left to take.";
    }
}
?>