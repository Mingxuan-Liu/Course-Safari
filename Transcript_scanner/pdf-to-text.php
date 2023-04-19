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
                if (strpos($text, "Course History")) {
                    $text = strstr($text, "Course History");
                    $text = substr($text, 0, strpos($text, "LEGEND"));
                }

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
                    $prefix = preg_replace('/as/', '', $prefix);
                    if (preg_match('/[0-9]{3}[A-Z]/', $num)) {
                        $num = substr($num, 0, 3);
                    }
                    if ((preg_match('/[A-Z]{2,}/', $prefix)) && (preg_match('/^[0-9]{3}$/', $num))) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl7/1J4z7GpdyZ8dJ9TIk1skMjTGmSks1U5i5jkjz5" crossorigin="anonymous">
</head>

<body>
    <div class="container">
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

                <div class="form-container">
                    <form action="pdf-to-text.php" method="post" enctype="multipart/form-data">

                        <div class="temp_upload">
                            <label for="transcript">Transcript PDF</label>
                            <input type="file" name="transcript" id="transcript" placeholder="Select a PDF file" required="">
                        </div>
                        <button type="submit">Upload</button>
                        <button type="reset">Clear</button>
                    </form>

                    <!-- Add a wrapper div for the security statement and title -->
                    <div class="security-statement-wrapper">
                        <!-- Add the lock icon before the security statement title -->
                        <i class="fas fa-lock security-statement-icon"></i>

                        <!-- Add the security statement title here -->
                        <h3 class="security-statement-title">Your Privacy Matters to Us</h3>

                        <!-- Add the updated security statement here -->
                        <p class="security-statement">
                            The security and privacy of your transcripts and personal information are our top priority. Upon uploading your transcript to our website, we only extract the necessary course information for processing and analysis. We do not store or retain any of your personal data, including your transcripts.
                        </p>
                    </div>

                    <!-- Tips container code -->
                    <div class="tips-container">
                        <h2>Where to find & download your PDF transcript?</h2>
                        <ol>
                            <li>Visit Emory OPUS: <a href="https://saprod.emory.edu/">https://saprod.emory.edu/</a></li>
                            <li>Log in using your Emory NetID and password.</li>
                            <img src="opus.png" alt="Step 2">
                        </ol>
                        <!-- First column for option 1 -->
                        <div class="column">
                            <h3>Option 1: Unofficial Transcript</h3>
                            <ul>
                                <li>Click the "Academic Records" tab.</li>
                                <img src="records.png" alt="Step 3">
                                <li>Select "view Unofficial Transcript" and click "Submit" at the top right.</li>
                                <img src="unofficial.png" alt="Step 4">
                                <li>Download the latest transcript from "view All Requested Reports" tab. Note that please download the pdf by selecting "Save as" instead of "Print," because otherwise the pdf will be saved as an image.</li>
                            </ul>
                        </div>
                        <!-- Second column for option 2 -->
                        <div class="column">
                            <h3>Option 2: Degree Tracker Report</h3>
                            <ul>
                                <li>Click the "Academic Progress" tab.</li>
                                <img src="progress.png" alt="Step 3">
                                <li>Select "Degree Audit/Degree Tracker" and click "OK" if prompted.</li>
                                <li>Click "View Report as PDF" to generate a PDF report of degree tracker.</li>
                                <img src="degree_tracker.png" alt="Step 5">
                                <li>Download the report by clicking the download icon or right-clicking and selecting "Save as."</li>
                            </ul>
                        </div>
                        
                    </div>
                    <!-- End of tips container code -->

                </div>

            <?php endif; ?>
            <?php unset($_SESSION["upload_success"]); ?>
        </div>
    </div>
</body>

</html>