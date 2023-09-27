<?php 
session_start();
require('Header.php');
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
            <?php
            $header = new Header();
            echo $header->getHeader();
            ?>
        </div>
            <br>
        <div class='container container--big'>
            <?php
            try {
            if(isset($_GET['id'])) {
                $content = "";

                $url_profile_id = htmlspecialchars($_GET['id']);

                $check_if_user_exists_query = $pdo->prepare("SELECT * FROM users WHERE id = :i");
                $check_if_user_exists_query->bindParam('i', $url_profile_id);
                $check_if_user_exists_query->execute();

                if($check_if_user_exists_query->rowCount() == 0) {
                    header('Location: 404.php');
                }

                foreach ($check_if_user_exists_query->fetchAll() as $row) {
                    $db_id = $row['id'];
                    $db_username = $row['username'];
                    $db_date_last_seen = $row['date_last_seen'];
                    $db_is_online = $row['is_online'];
                    $db_profile_picture = $row['profile_picture'];

                    $db_date_last_seen = strtotime($db_date_last_seen);

                    $online_status = ""; //going to be updated dynamically based on the user's status

                    if($is_online == 0) {
                        $online_status = 'Last Seen <br> '.date("n/j/Y", $date_created).'';
                    } else {
                        $online_status = 'Online!';
                    }

                    $content .= "<span class='container__main-title'>".$db_username."</span> <hr>";

                    if($db_id == $_SESSION['id']) {
                        $content .= "<a href='editProfile.php?id=".$db_id."'><button class='button'>Edit Your Profile</button></a>";
                    } else {
                        $content .= 'This is '.$db_username.'\'s profile. This feature overall is currently a work in progress.';
                    }

                    $content .= "
                    <div id='profile-information'>
                        <div id='profile-picture--big'>
                            <image src='".$db_profile_picture."'></image>
                        </div>
                    </div>
                    ";
                }

                echo $content;
            } else {
                header("Location: 404.php");
            } 
        } catch (Exception $e) {
            die($e);
        }
            ?>
        </div>
    </body>
</html>