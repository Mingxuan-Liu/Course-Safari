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

if (in_array('econ', $tags)) {
    $tag_conditions[] = "course_code LIKE 'ECON%'";
}

if (in_array('eng', $tags)) {
    $tag_conditions[] = "course_code LIKE 'ENG%'";
}

if (in_array('psyc', $tags)) {
    $tag_conditions[] = "course_code LIKE 'PYSC%'";
}

if (in_array('ant', $tags)) {
    $tag_conditions[] = "course_code LIKE 'ANT%'";
}

if (in_array('chem', $tags)) {
    $tag_conditions[] = "course_code LIKE 'CHEM%'";
}

if (in_array('pols', $tags)) {
    $tag_conditions[] = "course_code LIKE 'POLS%'";
}

if (in_array('qtm', $tags)) {
    $tag_conditions[] = "course_code LIKE 'QTM%'";
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

$requiredCourses = [];
if (in_array('required', $tags)) {
    $sql_major = "SELECT DISTINCT major_name FROM courses_taken WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $sql_major);
    $major_names = array();
    while ($obj = mysqli_fetch_assoc($result)) {
        $major_names[] = $obj['major_name'];
    }

    foreach($major_names as $major) {
        $sql_required = "SELECT course_prefix, course_num FROM major_minor WHERE major_name = '$major' AND required IS TRUE";
        $result = mysqli_query($conn, $sql_required);

        while ($obj = mysqli_fetch_assoc($result)) {
            $requiredCourses[] = $obj;
        }
    }
}

$electiveCourses = [];
if (in_array('elective', $tags)) {
    $sql_major = "SELECT DISTINCT major_name FROM courses_taken WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $sql_major);
    $major_names = array();
    while ($obj = mysqli_fetch_assoc($result)) {
        $major_names[] = $obj['major_name'];
    }

    foreach($major_names as $major) {
        $sql_elective = "SELECT course_prefix, course_num FROM major_minor WHERE major_name = '$major' AND required IS FALSE";
        $result = mysqli_query($conn, $sql_elective);

        while ($obj = mysqli_fetch_assoc($result)) {
            $electiveCourses[] = $obj;
        }
    }
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
    if (in_array('required', $tags) && in_array('elective', $tags)) {
        // no course is both required and elective
    }
    elseif (in_array('required', $tags)) {
        while ($row = $result->fetch_assoc()) {
            foreach ($requiredCourses as $requiredCourse) {
                
                if ($row["course_code"] == $requiredCourse["course_prefix"] && substr($row["course_num"], 0, 3) == $requiredCourse["course_num"]) {
                    $courses[] = $row;
                    break;
                }
            }
        }
    }
    elseif (in_array('elective', $tags)){
        while ($row = $result->fetch_assoc()) {
            foreach ($electiveCourses as $electiveCourse) {
                
                if ($row["course_code"] == $electiveCourse["course_prefix"] && substr($row["course_num"], 0, 3) == $electiveCourse["course_num"]) {
                    $courses[] = $row;
                    break;
                }
            }
        }
    }
    else {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}

$_SESSION["courses"] = $courses;

echo json_encode($courses);

$conn->close();
?>
