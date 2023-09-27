<?php
session_start();
require('functions.php');
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
        <div class='container'>
            <?php 
            $update_user_information = $pdo->prepare('UPDATE users SET is_online = 0 AND date_last_seen = now() WHERE id = :i');
            $update_user_information->bindParam('i', $_SESSION['id']);
            $update_user_information->execute();
            session_destroy(); 
            header('Location: login.php'); ?>
        </div>
    </body>
</html>