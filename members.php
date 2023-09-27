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
        <div class='container container--big'>
            <span class='container__main-title'>Member List</span>
            <?php
            $get_all_member_information_query = $pdo->prepare('SELECT `id`, `username`, `rank`, `date_created`, `number_of_posts`, `topics`, `reputation`, `profile_picture` FROM users ORDER BY `id` DESC');
            $get_all_member_information_query->execute();
            
            foreach($get_all_member_information_query->fetchAll() as $row) {
                $member_id = $row['id'];
                $username = $row['username'];
                $rank = $row['rank'];
                $date_created = $row['date_created'];
                $date_created = strtotime($date_created);
                $number_of_posts = $row['number_of_posts'];
                $topics = $row['topics'];
                $reputation = $row['reputation'];
                $profile_picture = $row['profile_picture'];

                echo 
                "<div id='member-list'>
                    <a href='profile.php?id=".$member_id."'>
                        <div id='profile-picture--small'>
                            <image src='".$profile_picture."'></image>
                        </div>
                    </a>

                    <div id='member-list__member-info'>
                        <a href='profile.php?id=".$member_id."' id='member-list__username'>".$username."</a>
                        ".convertRankToTitle($rank)."
                        Joined ".date("n/j/y, g:i A", $date_created)."
                    </div>
                </div>";
            }
            ?>
        </div>
    </body>
</html>