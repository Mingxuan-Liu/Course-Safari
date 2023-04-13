<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link rel="stylesheet" href="user.css">
  <title>Welcome to Course Safari</title>
</head>
<body>
    <div class="main">
      <h1>Welcome to Course Safari, <?php echo $_SESSION["username"]; ?></h1>
      <p>Your dashboard for all things</p>
      <div class="buttons">
        <a href="../Info_Update/update-info.php" class="btn btn-big btn-circle">
          <i class="fas fa-user"></i>
          <p>User info</p>
        </a>
        <a href="../Degree_tracker/declaration.php" class="btn btn-big btn-circle">
          <i class="fas fa-graduation-cap"></i>
          <p>Major & Minor Declaration</p>
        </a>
        <a href="../add_drop/add_drop_courses.php" class="btn btn-big btn-circle">
          <i class="fas fa-graduation-cap"></i>
          <p>Courses add/drop</p>
        </a>
        <a href="../Course_planner/course-planner.php" class="btn btn-big btn-circle">
          <i class="fas fa-calendar-alt"></i>
          <p>Course planner</p>
        </a>
      </div>
      <div class="logout-section">
        <a href="welcome.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          <logout>Log out</logout>
        </a>
      </div>
    </div>
</body>
</html>
