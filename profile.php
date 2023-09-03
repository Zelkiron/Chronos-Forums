<?php 
session_start();
require("connect.php");
require("functions.php");
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
            <span class='header__title'>CHRONOS</span>
            <a id='header__links' href='index.php'>Home</a>
            <a id='header__links' href='new_posts.php'>Recent Posts</a>
            <a id='header__links' href='status_updates.php'>Recent Status Updates</a>
            <a id='header__links' href='members.php'>Member List</a>
            <a id='header__links' href='staff.php'>Staff List</a>
            <a id='header__links' href='about.php'>About Me</a>
            <a id='header__links' href='#' onclick='profile()'>Profile</a>
        </div>
            <br>
        <div class='container'>
            <?php
            try {
            if(isset($_GET['id'])) {
                $url_profile_id = htmlspecialchars($_GET['id']);

                $check_if_user_exists_query = $pdo->prepare("SELECT * FROM users WHERE id = :i");
                $check_if_user_exists_query->bindParam('i', $url_profile_id);
                $check_if_user_exists_query->execute();

                if($check_if_user_exists_query->rowCount() == 0) {
                    header('Location: 404.html');
                }

                if($url_profile_id == $_SESSION['id']) {
                    echo 'Welcome to your profile, '.$_SESSION['username'].'! This is currently a work in progress.';
                }

                foreach ($check_if_user_exists_query->fetchAll() as $row) {
                    $db_username = $row['username'];
                    echo 'This is '.$db_username.'\'s profile.';
                }

                //i think i had plans with this switch, which is why i will keep it here for now 
                //but i may delete later (i added the default case later on when i realized that was the reason the code was breaking)
                /* switch (true) {
                    case ($check_if_user_exists_query->rowCount() == 0):
                        header("Location: 404.html");
                    default:
                        die('L');
                } */

            } else {
                header("Location: 404.html");
            } 
        } catch (Exception $e) {
            die($e);
        }
            ?>
        </div>
    </body>
</html>