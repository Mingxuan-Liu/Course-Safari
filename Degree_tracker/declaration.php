<?php
session_start();

if (isset($_SESSION["error"])) {
    unset($_SESSION["error"]);
}

function array_has_dupes($array) {
    return count($array) !== count(array_unique($array));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['primary_major'] = $_POST["primary_major"];
    $_SESSION['secondary_major'] = $_POST["secondary_major"];
    $_SESSION['minor'] = $_POST["minor"];

    $primary_subject_name = substr($_SESSION['primary_major'], 6, 100);
    $secondary_subject_name = substr($_SESSION['secondary_major'], 6, 100);
    $minor_subject_name = substr($_SESSION['minor'], 9, 100);
    $declare_names = [];
    if (strlen($primary_subject_name) > 2) {
        array_push($declare_names, $primary_subject_name);
    }
    if (strlen($secondary_subject_name) > 2) {
        array_push($declare_names, $secondary_subject_name);
    }
    if (strlen($minor_subject_name) > 2) {
        array_push($declare_names, $minor_subject_name);
    }

    if ($_SESSION['primary_major'] == "None" && $_SESSION['secondary_major'] == "None") {
        $_SESSION["error"] = "non_declaration";
    }
    elseif ($_SESSION['primary_major'] == "None" && $_SESSION['secondary_major'] != "None") {
        $_SESSION["error"] = "incorrect_format";
    }
    elseif (array_has_dupes($declare_names)) {
        $_SESSION['error'] = "duplicated_declaration";
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
                    header("Location: options.html");
                else:
            ?>
                <a href="../Login_register/user.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h1>Major/Minor(s) Declaration</h1>
                <?php
                    if (isset($_SESSION['error']) && $_SESSION['error'] == "non_declaration"):
                ?>
                    <p class="error">Declaration Failed! You must declare at least one major.</p>
                <?php
                    elseif (isset($_SESSION['error']) && $_SESSION['error'] == "incorrect_format"):
                ?>
                    <p class="error">Declaration Failed! You must declare primary major first.</p>
                <?php
                    elseif (isset($_SESSION['error']) && $_SESSION['error'] == "duplicated_declaration"):
                ?>
                    <p class="error">Declaration Failed! You must declare different majors.</p>
                <?php
                    endif;
                ?>
                <form action="declaration.php" method="post">
                    <div class="declare-name">Primary Major</div>
                    <div class="drop-down">
                        <select name="primary_major">
                            <option value="None" selected>None</option>
                            <option value="BS in Applied Mathematics & Statistics">BS in Applied Mathematics & Statistics</option>
                            <option value="BS in Mathematics">BS in Mathematics</option>
                            <option value="BA in Mathematics">BA in Mathematics</option>
                            <option value="BA in Political Science">BA in Political Science</option>
                            <option value="BS in Computer Science">BS in Computer Science</option>
                            <option value="BA in Computer Science">BA in Computer Science</option>
                            <option value="BA in Economics">BA in Economics</option>
                            <option value="BA in History">BA in History</option>
                            <option value="BA in Spanish">BA in Spanish</option>
                            <option value="BA in Japanese">BA in Japanese</option>
                        </select>
                    </div>
                    <div class="declare-name">Secondary Major</div>
                    <div class="drop-down">
                        <select name="secondary_major">
                            <option value="None" selected>None</option>
                            <option value="BS in Applied Mathematics & Statistics">BS in Applied Mathematics & Statistics</option>
                            <option value="BS in Mathematics">BS in Mathematics</option>
                            <option value="BA in Mathematics">BA in Mathematics</option>
                            <option value="BA in Political Science">BA in Political Science</option>
                            <option value="BS in Computer Science">BS in Computer Science</option>
                            <option value="BA in Computer Science">BA in Computer Science</option>
                            <option value="BA in Economics">BA in Economics</option>
                            <option value="BA in History">BA in History</option>
                            <option value="BA in Spanish">BA in Spanish</option>
                            <option value="BA in Japanese">BA in Japanese</option>
                        </select>
                    </div>
                    <div class="declare-name">Minor</div>
                    <div class="drop-down">
                        <select name="minor">
                            <option value="None" selected>None</option>
                            <option value="Minor in Mathematics">Minor in Mathematics</option>
                            <option value="Minor in Political Science">Minor in Political Science</option>
                            <option value="Minor in Computer Science">Minor in Computer Science</option>
                            <option value="Minor in Economics">Minor in Economics</option>
                            <option value="Minor in History">Minor in History</option>
                            <option value="Minor in Spanish">Minor in Spanish</option>
                            <option value="Minor in Japanese">Minor in Japanese</option>
                            <option value="Minor in Korean">Minor in Korean</option>
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
