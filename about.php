<?php 
session_start();
require('Header.php');
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
        <div class='container container--big'>
            <span class='container__main-title'>About Me</span> <br>
            <span class='about'>Hello. It's nice to meet you. I am a 3rd year university student pursuing an undergraduate computer science degree. <br> <br>
                I have shared a passion for web development ever since I was 10 years old, back when I was writing basic HTML websites. <br>
                I started coding in PHP when I was 12, and I had a forum just like this one. <br>
                That forum was a true passion project of mine. One where nothing like best practices and writing clean code mattered. <br>
                It was a time when I wrote code for the sake of it. When I wanted to continue seeing this amazing creation grow before my eyes. <br> <br>
                The Chronos Forums is a homage to that forum, and more. <br>
                It's a time capsule to show me how far I've come, and how far I can grow. <br> <br>
                If I am accepted, I hope I will have the chance to continue my growth on your team.</span>
        </div>
    </body>
</html>