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

    // Retrieve course information based on the clicked major
    $sql = "SELECT * FROM courses_taken
    WHERE user_id = '$user_id' AND major_name = '$clickedName'
    ORDER BY course_num";
    $result = mysqli_query($conn, $sql);

    while ($obj = mysqli_fetch_assoc($result)) {
        $temp_str = $obj['course_prefix'] . " " . $obj['course_num'] . ": " . $obj['course_name'];
        if ($obj['required'] == 1) {
            $temp_str = $temp_str . " (required)";
        } else {
            $temp_str = $temp_str . " (elective)";
        }
        echo "<h4>" . $temp_str . "</h4>";
    }
}
?>