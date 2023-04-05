<?php
require_once '../db_connection.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM schedule_courses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$courses = array();

while($row = $result->fetch_assoc()) {
    array_push($courses, $row);
}

header('Content-Type: application/json');
echo json_encode($courses);

$conn->close();
?>
