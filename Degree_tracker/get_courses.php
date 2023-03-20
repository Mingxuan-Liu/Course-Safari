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

    echo "<div class='courses-container'>"; // Start the courses container
    // Retrieve major courses that have not been taken yet

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
        echo "</div>";
    }
    echo "</div>"; // Close the courses container
}
?>