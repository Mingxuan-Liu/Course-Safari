<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){ 
        // If file is uploaded 
        if(!empty($_FILES["transcript"]["name"])){ 
            // File upload path 
            $fileName = basename($_FILES["transcript"]["name"]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
             
            // Allow certain file formats 
            $allowTypes = array('pdf'); 
            if(in_array($fileType, $allowTypes)){ 
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
                $courseArray = array();
                while ($prefix = current($textArray)) {
                    $num = next($textArray);
                    if ($prefix == "IP" || $prefix == "EN" || $prefix == "TE" || $prefix == "TR" || $prefix == "OT") {
                        unset($prefix);
                    }
                    if ($index = strpos($prefix, "_OX")) {
                        $prefix = substr($prefix, 0, $index);
                    }
                    if (preg_match('/[0-9]{3}[A-Z]/', $num)) {
                        $num = substr($num, 0, 3);
                    }
                    if ((preg_match('/[A-Z]{2,}/', $prefix)) && (preg_match('/[0-9]{3}/', $num))) {
                        $course = array($prefix, $num);
                        array_push($courseArray, $course);
                    }
                }
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
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
    <div class = "container">
        <div class="main">
            <h1>Upload Transcript</h1>

            <?php if (!empty($errorMessage)) { ?>
                <p><?php echo $errorMessage; ?>
            <?php } ?>

            <form action="pdf-to-text.php" method="post" enctype="multipart/form-data">

                <p>
                    <label for="transcript">Transcript PDF</label>
                    <input type="file" name="transcript" id="transcript" placeholder="Select a PDF file" required="">
                </p>
                <br>
                <button type="submit">Upload</button>
                <button type="reset">Clear</button>
            </form>
        </div>

        <div class = "printOutArea">
            <h1>Print Out Area</h1>
            <?php if (sizeof($courseArray) > 0) { ?>
                <p><?php 
                    // print the 2D course array
                    print_r($courseArray); 

                    echo nl2br("\n");
                    echo nl2br("\n");
                    echo nl2br("\n");

                    // print the courses line by line
                    $i = 1;
                    foreach($courseArray as $course) {
                        echo $course[0]." ".$course[1];
                        echo nl2br("\n");
                    }
                ?></p>
            <?php } ?>
        </div>
    </div>
</body>

</html>