<?php
    session_start();
    require_once '../db_connection.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){ 
        // If file is uploaded 
        if(!empty($_FILES["transcript"]["name"])){ 
            // File upload path 
            $fileName = basename($_FILES["transcript"]["name"]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
             
            // Allow certain file formats 
            $allowTypes = array('pdf');
            if(in_array($fileType, $allowTypes)){

                $user_id = $_SESSION["user_id"];

                $sql = "SELECT * FROM courses_taken WHERE user_id = '$user_id'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $sql = "DELETE FROM courses_taken WHERE user_id = '$user_id'";
                    mysqli_query($conn, $sql);
                }
            
                $sql = "SELECT * FROM electives_taken WHERE user_id = '$user_id'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $sql = "DELETE FROM electives_taken WHERE user_id = '$user_id'";
                    mysqli_query($conn, $sql);
                }

                // include pdf parser and parse the file
                require '../pdfparser/alt_autoload.php-dist';

                $file = $_FILES["transcript"]["tmp_name"];

                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file);

                $text = $pdf->getText();

                // clean up the text from file
                $text = strstr($text, "Course History");
                $text = substr($text, 0, strpos($text, "LEGEND"));

                $textArray = preg_split("/[\s,]+/", $text);
                $textArray = preg_grep ('/[A-Z]{2,}|[0-9]{3}/', $textArray);

                // further clean up the texts and put the courses into 2D array
                $tempArray = array();
                while ($prefix = current($textArray)) {
                    $num = next($textArray);
                    if ($prefix == "IP" || $prefix == "EN" || $prefix == "TE" || $prefix == "TR" || $prefix == "OT") {
                        continue;
                    }
                    if ($index = strpos($prefix, "_OX")) {
                        $prefix = substr($prefix, 0, $index);
                    }
                    if (preg_match('/[0-9]{3}[A-Z]/', $num)) {
                        $num = substr($num, 0, 3);
                    }
                    if ((preg_match('/[A-Z]{2,}/', $prefix)) && (preg_match('/[0-9]{3}/', $num))) {
                        $temp_str = $prefix.$num;
                        if (!in_array($temp_str, $tempArray)) {
                            array_push($tempArray, $temp_str);
                        }
                    }
                }

                if ($_SESSION['primary_major'] != "None") {
                    $major_name =  $_SESSION['primary_major'];
                    $sql = "SELECT CONCAT(UPPER(course_prefix), course_num) AS course 
                            FROM major_minor WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $temp_courses = array();
                    while ($obj = mysqli_fetch_assoc($result)) {
                        array_push($temp_courses, $obj['course']);
                    }
                    foreach ($tempArray as $item) {
                        if (in_array($item, $temp_courses)) {
                            $temp_prefix = substr($item, 0, -3);
                            $temp_num = intval(substr($item, -3));
                            $sql = "SELECT DISTINCT course_name, required
                                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                                    AND major_name = '$major_name'";
                            $result = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            $temp_name = $result['course_name'];
                            $temp_required = $result['required'];
                            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
                            mysqli_query($conn, $sql);
                        }
                    }

                    $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $total_electives = mysqli_fetch_array($result)[0];
                    $sql = "SELECT COUNT(*) FROM courses_taken 
                            WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
                    $result = mysqli_query($conn, $sql);
                    $electives_taken = mysqli_fetch_array($result)[0];
                    $ready_for_insert = $total_electives - $electives_taken;
                    $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                            VALUES ('$user_id', '$major_name', '$ready_for_insert')";
                    mysqli_query($conn, $sql);
                }

                if ($_SESSION['secondary_major'] != "None") {
                    $major_name =  $_SESSION['secondary_major'];
                    $sql = "SELECT CONCAT(UPPER(course_prefix), course_num) AS course 
                            FROM major_minor WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $temp_courses = array();
                    while ($obj = mysqli_fetch_assoc($result)) {
                        array_push($temp_courses, $obj['course']);
                    }
                    foreach ($tempArray as $item) {
                        if (in_array($item, $temp_courses)) {
                            $temp_prefix = substr($item, 0, -3);
                            $temp_num = intval(substr($item, -3));
                            $sql = "SELECT DISTINCT course_name, required
                                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                                    AND major_name = '$major_name'";
                            $result = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            $temp_name = $result['course_name'];
                            $temp_required = $result['required'];
                            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
                            mysqli_query($conn, $sql);
                        }
                    }

                    $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $total_electives = mysqli_fetch_array($result)[0];
                    $sql = "SELECT COUNT(*) FROM courses_taken 
                            WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
                    $result = mysqli_query($conn, $sql);
                    $electives_taken = mysqli_fetch_array($result)[0];
                    $ready_for_insert = $total_electives - $electives_taken;
                    $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                            VALUES ('$user_id', '$major_name', '$ready_for_insert')";
                    mysqli_query($conn, $sql);
                }

                if ($_SESSION['minor'] != "None") {
                    $major_name =  $_SESSION['minor'];
                    $sql = "SELECT CONCAT(UPPER(course_prefix), course_num) AS course 
                            FROM major_minor WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $temp_courses = array();
                    while ($obj = mysqli_fetch_assoc($result)) {
                        array_push($temp_courses, $obj['course']);
                    }
                    foreach ($tempArray as $item) {
                        if (in_array($item, $temp_courses)) {
                            $temp_prefix = substr($item, 0, -3);
                            $temp_num = intval(substr($item, -3));
                            $sql = "SELECT DISTINCT course_name, required
                                    FROM major_minor WHERE course_prefix = '$temp_prefix' AND course_num = '$temp_num'
                                    AND major_name = '$major_name'";
                            $result = mysqli_query($conn, $sql);
                            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            $temp_name = $result['course_name'];
                            $temp_required = $result['required'];
                            $sql = "INSERT INTO courses_taken (user_id, major_name, course_prefix, course_num, course_name, required) 
                                    VALUES ('$user_id', '$major_name', '$temp_prefix', '$temp_num', '$temp_name', '$temp_required')";
                            mysqli_query($conn, $sql);
                        }
                    }

                    $sql = "SELECT num_electives FROM electives WHERE major_name = '$major_name'";
                    $result = mysqli_query($conn, $sql);
                    $total_electives = mysqli_fetch_array($result)[0];
                    $sql = "SELECT COUNT(*) FROM courses_taken 
                            WHERE user_id = '$user_id' AND major_name = '$major_name' AND required IS FALSE";
                    $result = mysqli_query($conn, $sql);
                    $electives_taken = mysqli_fetch_array($result)[0];
                    $ready_for_insert = $total_electives - $electives_taken;
                    $sql = "INSERT INTO electives_taken (user_id, major_name, electives_left)
                            VALUES ('$user_id', '$major_name', '$ready_for_insert')";
                    mysqli_query($conn, $sql);
                }

                $_SESSION["upload_success"] = true;
            }
            else{ 
                $errorMessage = '<p>Sorry, only PDF file is allowed to upload.</p>'; 
            } 
        }
        else{ 
            $errorMessage = '<p>Please upload your transcript.</p>'; 
        } 
    } 
?>


<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Tianjun Zhong">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page allows users to check their taken courses by uploading their transcript.">
    <title>Scan Transcript</title>
    <link rel="stylesheet" href="pdf_to_text_style.css" type="text/css">
</head>

<body>
    <div class = "container">
        <div class="main">
            <?php if (isset($_SESSION["upload_success"])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h1>Upload Successful!</h1>
                    <a href="../Info_Update/course_progress.php" class="btn">Check Your Academic Progress</a>
                </div>
            <?php else: ?>
                <h1>Upload Transcript</h1>

                <?php if (!empty($errorMessage)) { ?>
                    <p><?php echo $errorMessage; ?>
                <?php } ?>

                <form action="pdf-to-text.php" method="post" enctype="multipart/form-data">

                    <div class="temp_upload">
                        <label for="transcript">Transcript PDF</label>
                        <input type="file" name="transcript" id="transcript" placeholder="Select a PDF file" required="">
                    </div>
                    <button type="submit">Upload</button>
                    <button type="reset">Clear</button>
                </form>
            <?php endif; ?>
            <?php unset($_SESSION["upload_success"]); ?>
        </div>
    </div>
</body>

</html>