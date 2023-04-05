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
$tags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : [];

$sql = "SELECT course_code, course_name, days, start_time, end_time FROM courses";

$where_conditions = [];

if ($keyword !== '') {
    $keyword = $conn->real_escape_string($keyword);
    $where_conditions[] = "course_code LIKE '%$keyword%' OR course_name LIKE '%$keyword%'";
}

if (in_array('math', $tags)) {
    $where_conditions[] = "course_code LIKE 'MATH%'";
}

if (in_array('morning', $tags)) {
    $where_conditions[] = "TIME(start_time) < '12:00:00'";
}

if (count($where_conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
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
