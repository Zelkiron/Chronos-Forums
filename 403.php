<?php
session_start();
require('Header.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Chronos Forums</title>
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&amp;display=swap">
    </head> 

    <body>
        <div id='header'>
            <?php 
            $header = new Header();
            echo $header->getHeader();
            ?>
        </div>
            <br>
        <div class='container'>
            <span class="title container__main-title">403 Insufficient Permission</span>
            <br>
            You do not have permission to see what is displayed on this page. 
            <br>
            You will be redirected back to the homepage in five seconds. 
            <script>window.setTimeout("location=('index.php');", "5000");</script>
        </div>
    </body>
</html>