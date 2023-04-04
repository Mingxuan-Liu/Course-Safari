<?php
session_start();

// check whether there is any item being clicked at all
if (isset($_POST['clickedItem'])) {
    // store the clicked item's text in a PHP variable
    $clickedName = $_POST['clickedItem'];
    // store the PHP variable in $_SESSION
    $_SESSION['clickedName'] = $clickedName;

    // Perform your desired actions with the clicked item's text
    echo "Received clicked item: " . $clickedName;
} else {
    echo "No data received";
}
?>