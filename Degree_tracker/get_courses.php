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
$user_id = $_SESSION["user_id"];

if (isset($_GET["major"])) {
    $clickedName = $_GET["major"];
    echo "<h2>" . "Course Requirements" . "</h2>";
    // Calculate the total number of courses and the number of completed courses
    $total_courses_sql = "SELECT COUNT(*) FROM major_minor WHERE major_name = '$clickedName'";
    $completed_courses_sql = "SELECT COUNT(*) FROM courses_taken WHERE user_id = '$user_id' AND major_name = '$clickedName'";

    $total_courses_result = mysqli_query($conn, $total_courses_sql);
    $completed_courses_result = mysqli_query($conn, $completed_courses_sql);

    $total_courses = mysqli_fetch_array($total_courses_result)[0];
    $completed_courses = mysqli_fetch_array($completed_courses_result)[0];
    // Calculate the completion percentage
    $completion_percentage = 0;
    if ($total_courses > 0) {
        $completion_percentage = ($completed_courses / $total_courses) * 100;
    }
    // Display the progress bar
    echo "<div class='progress-container'>";
    echo "<div class='completed' style='width: " . $completion_percentage . "%;'></div>";
    echo "<span class='progress-percentage'>" . round($completion_percentage, 2) . "%</span>";
    echo "</div>";

    // Start the courses container
    echo "<div class='courses-container'>";
    // Retrieve major courses that have not been taken yet
    $not_taken_sql = "SELECT major_minor.*
                      FROM major_minor
                      LEFT JOIN courses_taken ON major_minor.course_name = courses_taken.course_name
                      WHERE major_minor.major_name = '$clickedName' AND courses_taken.course_name IS NULL";
    $not_taken = mysqli_query($conn, $not_taken_sql);
    while ($not_taken_obj = mysqli_fetch_assoc($not_taken)) {
        $not_taken_str = $not_taken_obj['course_prefix'] . " " . $not_taken_obj['course_num'] . ": " . $not_taken_obj['course_name'];
        echo "<div class='not-taken-block'>";
        echo "<h4>" . $not_taken_str . "</h4>";
        $course_tag = "";
        if ($not_taken_obj['required'] == 1) {
            $course_tag = "Required";
            echo "<span class='require-tag'>" . $course_tag . "</span>";
        } else {
            $course_tag = "Elective";
            echo "<span class='elective-tag'>" . $course_tag . "</span>";
        }
        echo "<span class='need-tag'>" . "Still Needed" . "</span>";
        echo "</div>";
    }

    // Retrieve course information based on the clicked major
    $sql = "SELECT * FROM courses_taken
            WHERE user_id = '$user_id' AND major_name = '$clickedName'
            ORDER BY course_num";
    $taken = mysqli_query($conn, $sql);
    
    while ($obj = mysqli_fetch_assoc($taken)) {
        $taken_str = $obj['course_prefix'] . " " . $obj['course_num'] . ": " . $obj['course_name'];
        echo "<div class='taken-block'>";
        echo "<h4>" . $taken_str . "</h4>";
        $course_tag = "";
        if ($obj['required'] == 1) {
            $course_tag = "Required";
            echo "<span class='require-tag'>" . $course_tag . "</span>";
        } else {
            $course_tag = "Elective";
            echo "<span class='elective-tag'>" . $course_tag . "</span>";
        }
        echo "<span class='complete-tag'>" . "Completed" . "</span>";
        echo "</div>";
    }
    echo "</div>"; // Close the courses container
}
?>