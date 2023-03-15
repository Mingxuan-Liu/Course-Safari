
<?php
// establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// retrieve courses scheduled by the user from the course_scheduled table
$sql = "SELECT * FROM course_scheduled";
$result = $conn->query($sql);

// create the timetable
$timetable = array(
  "Monday" => array(
    "8:00 AM" => "",
    "9:00 AM" => "",
    "10:00 AM" => "",
    "11:00 AM" => "",
    "12:00 PM" => "",
    "1:00 PM" => "",
    "2:00 PM" => "",
    "3:00 PM" => "",
    "4:00 PM" => "",
    "5:00 PM" => "",
    "6:00 PM" => "",
    "7:00 PM" => ""
  ),
  "Tuesday" => array(
    "8:00 AM" => "",
    "9:00 AM" => "",
    "10:00 AM" => "",
    "11:00 AM" => "",
    "12:00 PM" => "",
    "1:00 PM" => "",
    "2:00 PM" => "",
    "3:00 PM" => "",
    "4:00 PM" => "",
    "5:00 PM" => "",
    "6:00 PM" => "",
    "7:00 PM" => ""
  ),
  "Wednesday" => array(
    "8:00 AM" => "",
    "9:00 AM" => "",
    "10:00 AM" => "",
    "11:00 AM" => "",
    "12:00 PM" => "",
    "1:00 PM" => "",
    "2:00 PM" => "",
    "3:00 PM" => "",
    "4:00 PM" => "",
    "5:00 PM" => "",
    "6:00 PM" => "",
    "7:00 PM" => ""
  ),
  "Thursday" => array(
    "8:00 AM" => "",
    "9:00 AM" => "",
    "10:00 AM" => "",
    "11:00 AM" => "",
    "12:00 PM" => "",
    "1:00 PM" => "",
    "2:00 PM" => "",
    "3:00 PM" => "",
    "4:00 PM" => "",
    "5:00 PM" => "",
    "6:00 PM" => "",
    "7:00 PM" => ""
  ),
  "Friday" => array(
    "8:00 AM" => "",
    "9:00 AM" => "",
    "10:00 AM" => "",
    "11:00 AM" => "",
    "12:00 PM" => "",
    "1:00 PM" => "",
    "2:00 PM" => "",
    "3:00 PM" => "",
    "4:00 PM" => "",
    "5:00 PM" => "",
    "6:00 PM" => "",
    "7:00 PM" => ""
  ),
);


// populate the timetable with the scheduled courses
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $course_num = $row["course_num"];
    $course_name = $row["course_name"];
    $subject_name = $row["subject_name"];
    $course_weekday = $row["course_weekday"];
    $course_time = $row["course_time"];
    
    // determine the time slot of the course
    $start_time = $course_time;
    $end_time = date("g:i A", strtotime($start_time) + 60 * 60);
    
    // add the course to the timetable
    $timetable[$course_weekday][$start_time] = $course_num;
    $timetable[$course_weekday][$end_time] = $course_name . " (" . $subject_name . ")";
  }
}

// close database connection
$conn->close();
?>

<?php
session_start();

// establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// retrieve list of available courses from the course_list table
$sql = "SELECT * FROM course_list";
$result = $conn->query($sql);

if (isset($_POST['search'])) {
  $search_query = $_POST['search'];
  // retrieve list of available courses matching search query
  $sql = "SELECT * FROM course_list WHERE course_num LIKE '%$search_query%' OR course_name LIKE '%$search_query%' OR subject_name LIKE '%$search_query%'";
  $result = $conn->query($sql);
} else {
  // retrieve list of all available courses
  $sql = "SELECT * FROM course_list";
  $result = $conn->query($sql);
}

// add selected courses to the course_scheduled table
if (isset($_POST["submit"])) {
    $courses = $_POST["courses"];
    foreach ($courses as $course_id) {
      // check if course is already scheduled
      $sql = "SELECT * FROM course_scheduled WHERE course_id = $course_id";
      $result = $conn->query($sql);
      if ($result->num_rows == 0) {
        // if course is not already scheduled, add it to the table
        $sql = "SELECT * FROM course_list WHERE course_id = $course_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $course_num = $row["course_num"];
        $course_name = $row["course_name"];
        $subject_name = $row["subject_name"];
        $course_weekday = $row["course_weekday"];
        $course_time = $row["course_time"];
        $sql = "INSERT INTO course_scheduled (course_num, course_name, subject_name, course_weekday, course_time) VALUES ('$course_num', '$course_name', '$subject_name', '$course_weekday', '$course_time')";
        $conn->query($sql);
      }
    }
    header("Location: course-planner.php");
    exit();
  }
  

// close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <title>Time Table</title>
  <link rel="stylesheet" href="time-table_style.css">
</head>
<body>
  <h1>Course Planner</h1>
  
  <form method="post">
  <h2 class="search-header">Search Courses</h2>
  <div class="search-container">
    <input type="text" placeholder="Search.." name="search" class="search-input">
    <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
  </div>

    <h2>Select Courses</h2>
    <?php
    // display list of available courses
    echo "<ul class='course-list'>";
    while ($row = $result->fetch_assoc()) {
      echo "<li>";
      echo "<input type='checkbox' name='courses[]' value='" . $row["course_id"] . "'>";
      echo "<div class='course-details'>";
      echo "<div class='course-header'>";
      echo "<strong>" . $row["course_num"] . "</strong>: " . $row["course_name"];
      echo "</div>";
      echo "<div class='course-info'>";
      echo "<div class='subject-name'>" . $row["subject_name"] . "</div>";
      echo "<div class='course-time'>" . $row["course_weekday"] . " " . $row["course_time"] . "</div>";
      echo "</div>";
      echo "</div>";
      echo "</li>";
    }
    echo "</ul>";
    ?>
    <input type="submit" name="submit" value="Submit">
  </form>

<h1>Time Table</h1>
  
  <?php
  // display the timetable
  echo "<table class='timetable'>";
  echo "<tr><th>Time</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr>";
  $times = array(
    "8:00 AM", "9:00 AM", "10:00 AM", "11:00 AM",
    "12:00 PM", "1:00 PM", "2:00 PM", "3:00 PM",
    "4:00 PM", "5:00 PM", "6:00 PM", "7:00 PM"
  );
  foreach ($times as $time) {
    echo "<tr><td>" . $time . "</td>";
    foreach ($timetable as $day => $courses) {
      echo "<td>" . $courses[$time] . "</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
  ?>
  
</body>
</html>
