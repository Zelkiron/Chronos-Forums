<?php session_start(); ?>
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
            <span class='title'>CHRONOS</span>
            <a id='headerLink' href='index.php'>Home</a>
            <a id='headerLink' href='new_posts.php'>Recent Posts</a>
            <a id='headerLink' href='status_updates.php'>Recent Status Updates</a>
            <a id='headerLink' href='members.php'>Member List</a>
            <a id='headerLink' href='staff.php'>Staff List</a>
            <a id='headerLink' href='about.php'>About Me</a>
            <a id='headerLink' href='#' onclick='profile()'>Profile</a>
        </div>

        <br>

        <div class='container bigPage'>
            <?php 
            include('connect.php');

            //get the name and id of the category itself (category id will be used in topic query)
            //also pritorize categories that everyone can see (staff categories last)
            $catQuery = $pdo->prepare("SELECT id, name FROM categories WHERE visibility = '1'");
            $catQuery->execute();

            //initalize content variable for later
            $content = "";

            foreach ($catQuery->fetchAll() as $row) {
                $catName = $row['name'];
                $cat_id = $row['id'];

                //add all the necessary information for the category
                $content .=
                "<div id='smallCatBanner'>
                    <a href='category.php?cat=".$cat_id."'>".$catName."</a>
                </div>";

                //get info for topic
                $topicQuery = $pdo->prepare("SELECT id, poster_id, name, dateCreated, replies FROM topics WHERE cat_id = :i AND visibility = '1' ORDER BY dateNewReply DESC LIMIT 1");
                $topicQuery->bindParam('i', $cat_id);
                $topicQuery->execute();

                foreach ($topicQuery->fetchAll() as $row) {
                    $topic_id = $row['id'];
                    $poster_id = $row['poster_id'];
                    $topicName = $row['name'];
                    $topicDate = $row['dateCreated'];
                    $topicDate = strtotime($topicDate);
                    $topicReplies = $row['replies'];

                    $posterQuery = $pdo->prepare("SELECT * FROM users WHERE id = :i");
                    $posterQuery->bindParam('i', $poster_id);
                    $posterQuery->execute();

                    foreach($posterQuery->fetchAll() as $row) {
                        $posterName = $row['username'];
                        $pfp = $row['pfp'];

                        $content .= 
                        "<div id='smallPostContent'>
                        <a href='profile.php?username=".$posterName."'>
                            <div id='small_pfp'>
                                <image src='".$pfp."'></image>
                            </div>
                        </a>

                        <div id='topicInfo'>
                            <a href='topic.php?id=".$topic_id."' id='topicTitle'>".$topicName."</a><br>
                            <a href='profile.php?username=".$posterName."' id='topicPoster'>".$posterName."</a> | 
                            <span id='date'>".date("n/j/y, g:i A", $topicDate)."
                            <br>Replies: ".$topicReplies."</span>
                        </div>
                        </div>";
                    }
                }
            }

            echo $content;

            ?>
        </div>
    </body>
</html>