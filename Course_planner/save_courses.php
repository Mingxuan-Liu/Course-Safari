<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

require_once '../db_connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courses = json_decode($_POST['courses'], true);

    // Debugging lines
    echo "Courses received:\n";
    print_r($courses);

    $sql_delete = "DELETE FROM schedule_courses WHERE user_id = '$user_id'";

    if ($conn->query($sql_delete) !== TRUE) {
        die("Error deleting courses: " . $conn->error);
    }

    foreach ($courses as $course) {
        $course_code = $course['course_code'];
        $course_num = $course['course_num'];
        $course_name = $course['course_name'];
        $start_time = $course['start_time'];
        $end_time = $course['end_time'];
        $days = $course['days'];
        
        $sql = "INSERT INTO schedule_courses (user_id, course_code, course_num, course_name, start_time, end_time, days)
                VALUES ('$user_id', '$course_code', '$course_num', '$course_name', '$start_time', '$end_time', '$days')
                ON DUPLICATE KEY UPDATE course_name = '$course_name', end_time = '$end_time', days = '$days'";
        
        if ($conn->query($sql) !== TRUE) {
            die("Error: " . $sql . "<br>" . $conn->error);
        }
    }
    
    echo 'Courses saved successfully!';
}

$conn->close();
?>
