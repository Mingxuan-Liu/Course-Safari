<?php
session_start();
$userId = $_SESSION['user_id'];

header('Content-Type: application/json');

require_once '../db_connection.php';

$keyword = isset($_GET['keyword']) && strlen($_GET['keyword']) >= 2 ? $_GET['keyword'] : '';
$tags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : [];
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : '';

$sql = "SELECT course_code, course_num, course_name, days, start_time, end_time, professor, professor_rate, professor_lev_diff";

$tag_conditions = [];

if (in_array('cs', $tags)) {
    $tag_conditions[] = "course_code LIKE 'CS%'";
}

if (in_array('math', $tags)) {
    $tag_conditions[] = "course_code LIKE 'MATH%'";
}

if (in_array('morning', $tags)) {
    $tag_conditions[] = "TIME(start_time) < '12:00:00'";
}

if (in_array('afternoon', $tags)) {
    $tag_conditions[] = "TIME(start_time) > '12:00:00'";
}

if (in_array('100-level', $tags)) {
    $tag_conditions[] = "course_num LIKE '1__%'";
}

if (in_array('200-level', $tags)) {
    $tag_conditions[] = "course_num LIKE '2__%'";
}

if (in_array('300-level+', $tags)) {
    $tag_conditions[] = "(course_num LIKE '3__%' OR course_num LIKE '4__%' OR course_num LIKE '5__%' OR course_num LIKE '6__%' OR course_num LIKE '7__%')";
}


// Add a derived column to count the number of matched filters
if (count($tag_conditions) > 0) {
    $sql .= ", (" . implode(" + ", array_map(function ($condition) {
        return "CASE WHEN " . $condition . " THEN 1 ELSE 0 END";
    }, $tag_conditions)) . ") AS matched_filters";
}

$sql .= " FROM courses";

$where_conditions = [];

if ($keyword !== '') {
    $keyword = $conn->real_escape_string($keyword);
    $where_conditions[] = "CONCAT(course_code, course_num, course_name) LIKE '%$keyword%'";
}

if (count($tag_conditions) > 0) {
    $where_conditions[] = implode(" AND ", $tag_conditions);
}

if (count($where_conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

// Add a sort condition based on the provided sortBy parameter
if ($sortBy === 'professor_lev_diff') {
    $sql .= " ORDER BY professor_lev_diff ASC";
} elseif ($sortBy === 'professor_rate') {
    $sql .= " ORDER BY professor_rate DESC";
} else {
    $sql .= " ORDER BY course_code ASC, course_num ASC";
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
