<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$keyword = isset($_GET['keyword']) && strlen($_GET['keyword']) >= 3 ? $_GET['keyword'] : '';

$sql = "SELECT course_code, course_name, days, start_time, end_time FROM courses";

if ($keyword !== '') {
    $keyword = $conn->real_escape_string($keyword);
    $sql .= " WHERE course_code LIKE '%$keyword%' OR course_name LIKE '%$keyword%'";
}

$result = $conn->query($sql);
$courses = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

echo json_encode($courses);

$conn->close();
?>
