<?php
session_start();

if (isset($_SESSION["error"])) {
    unset($_SESSION["error"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['primary_major'] = $_POST["primary_major"];
    $_SESSION['secondary_major'] = $_POST["secondary_major"];
    $_SESSION['minor'] = $_POST["minor"];

    if ($_SESSION['primary_major'] == "None" && $_SESSION['secondary_major'] == "None") {
        $_SESSION["error"] = "non_declaration";
    }
    elseif ($_SESSION['primary_major'] == "None" && $_SESSION['secondary_major'] != "None") {
        $_SESSION["error"] = "incorrect_format";
    }

    if (!isset($_SESSION["error"])) {
        $_SESSION["declare_success"] = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link rel="stylesheet" href="degree_tracker_style.css">
  <title>Declare to Course Safari</title>
</head>
<body>
    <div class="container">
        <div class="main">
            <?php
                if (isset($_SESSION["declare_success"])):
                    header("Location: survey.php");
                else:
            ?>
                <a href="../Login_register/user.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h1>Major/Minor(s) Declaration</h1>
                <?php
                    if ($_SESSION['error'] == "non_declaration"):
                ?>
                    <p class="error">Declaration Failed! You must declare at least one major.</p>
                <?php
                    elseif ($_SESSION['error'] == "incorrect_format"):
                ?>
                    <p class="error">Declaration Failed! You must declare primary major first.</p>
                <?php
                    endif;
                ?>
                <form action="declaration.php" method="post">
                    <div class="declare-name">Primary Major</div>
                    <div class="drop-down">
                        <select name="primary_major">
                            <option value="None" selected>None</option>
                            <option value="BA in Computer Science">BA in Computer Science</option>
                            <option value="BS in Computer Science">BS in Computer Science</option>
                            <option value="BA in Economics">BA in Economics</option>
                            <option value="BA in History">BA in History</option>
                        </select>
                    </div>
                    <div class="declare-name">Secondary Major</div>
                    <div class="drop-down">
                        <select name="secondary_major">
                            <option value="None" selected>None</option>
                            <option value="BA in Computer Science">BA in Computer Science</option>
                            <option value="BS in Computer Science">BS in Computer Science</option>
                            <option value="BA in Economics">BA in Economics</option>
                            <option value="BA in History">BA in History</option>
                        </select>
                    </div>
                    <div class="declare-name">Minor</div>
                    <div class="drop-down">
                        <select name="minor">
                            <option value="None" selected>None</option>
                            <option value="Minor in Computer Science">Minor in Computer Science</option>
                            <option value="Minor in Economics">Minor in Economics</option>
                            <option value="Minor in History">Minor in History</option>
                        </select>
                    </div>
                    <input type="submit" value="Declare" class="btn">
                </form>
            <?php
                endif;
                unset($_SESSION["error"]);
                unset($_SESSION["declare_success"]);
            ?>
        </div>
    </div>
</body>
</html>
