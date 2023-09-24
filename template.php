<?php 
require('functions.php');
require('Header.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chronos Forums</title>
        <link rel='stylesheet' href='style.css'>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&display=swap'>
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
            This is a template for the upcoming forum I am going to build.
        </div>
    </body>
</html>