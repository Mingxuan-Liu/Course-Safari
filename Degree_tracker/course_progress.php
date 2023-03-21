<?php
// Reference web design: https://dribbble.com/shots/7169372-Course-Registration-App

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Mingxuan Liu">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This page shows the academic progress of the user.">
        <title>Course Progress</title>
        <link rel="stylesheet" href="progress-style.css" type="text/css">
    </head>

    <body>
        <div class="container">
            <div class="main">
                <a href="../Login_register/user.php" class="back-btn">Home</a>
                <h1>Degree Completion</h1>
                <hr>
                    <?php $user_id = $_SESSION["user_id"]; // obtain the user id from database?>
                    <?php
                        // select all majors of this user has declared from table courses-taken
                        $sql = "SELECT * FROM courses_taken WHERE user_id = '$user_id'";
                        $result = mysqli_query($conn, $sql);
                        // if the user has not declared any major yet, then warn the user
                        if (mysqli_num_rows($result) == 0):
                    ?>
                            <h2>You have not declared your major. Please declare it <a href="../Degree_tracker/declaration.php">here</a>.</h2>
                    <?php
                        else:
                            // if more than one major is declared, then select the major names from the database for users to choose
                            $sql = "SELECT DISTINCT major_name FROM courses_taken WHERE user_id = '$user_id'";
                            $result = mysqli_query($conn, $sql);
                            $major_names = array();
                            while ($obj = mysqli_fetch_assoc($result)) {
                                $major_names[] = $obj['major_name'];
                            }
                        endif;
                    ?>
                    <!-- Create a toggle list that allows the users to click on list items -->
                    <div class="toggled-list">
                        <input type="checkbox" id="toggle-list">
                        <label class="toggled-list-label" for="toggle-list">Select Degree</label>
                        <ul class="toggled-list-content" id="toggled-list-items">
                            <?php
                            for ($x = 0; $x < count($major_names); $x++) {
                                // add major name into the toggle list
                                $temp_major = $major_names[$x];
                                echo "<li>" . $temp_major . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <script>
                        // Create a variable to store the clicked item's text
                        let clicked = "";

                        // Listen for the custom "itemClicked" event
                        document.addEventListener("itemClicked", function (event) {
                            // Update the 'clicked' variable with the clicked item's text
                            clicked = event.detail.text;
                            console.log("Item clicked (from HTML): " + clicked);

                            // Send the clicked item's text to the PHP script using the fetch API
                            fetch("handle_click.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: "clickedItem=" + encodeURIComponent(clicked)
                            })
                            .then(response => response.text())
                            .then(data => {
                                console.log("Response from PHP script: " + data);

                                // Fetch the course information based on the clicked major
                                fetch("get_courses.php?major=" + encodeURIComponent(clicked))
                                .then(response => response.text())
                                .then(courseInfo => {
                                    document.getElementById("clicked-item-output").innerHTML =  courseInfo;
                                })
                                .catch(error => {
                                    console.error("Error fetching course information:", error);
                                });
                            })
                            .catch(error => {
                                console.error("Error sending data to PHP script:", error);
                            });
                        });
                    </script>
                    <!-- Call javascripts to handle the click actions of user -->
                    <script src="scripts.js"></script>
                    <?php
                        // retrieve the name of the list item that was clicked
                        session_start();
                        // make sure the $clickedName variable exists in SESSION (i.e., whether an item was actually clicked)
                        if (isset($_SESSION['clickedName'])) {
                            $clickedName = $_SESSION['clickedName'];
                            // Perform your desired actions with the clicked item's text
                        } else {
                            echo "No clicked item found in session";
                        }
                    ?>
                <hr>
                <div id="clicked-item-output"> 
                    <!-- The content of this section is automatically updated by javascripts whenever a different list item is clicked-->
                </div>
            </div>
        </div>
    </body>
</html>
