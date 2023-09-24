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

        <div class='container container__big'>
            <?php
            //welcome the user to the forum if they are logged in or not regardless
            if(isset($_SESSION['id'])) {
                echo '<h3>Welcome to the forums, '.$_SESSION['username'].'!</h3>';
            } else {
                echo 'Welcome to the forums, Guest! <a class="noticeable-link" href="login.php">Click here</a> to login. <br>';
            }

            //get the name and id of the category itself (category id will be used in topic query)
            //also pritorize categories that everyone can see (staff categories last)
            $category_query = $pdo->prepare("SELECT id, `name` FROM categories WHERE visibility = '1'");
            $category_query->execute();

            //initalize content variable for later
            $content = "";

            foreach ($category_query->fetchAll() as $row) {
                $category_name = $row['name'];
                $category_id = $row['id'];

                //add all the necessary information for the category
                $content .=
                "<div id='category-banner--small'>
                    <a href='category.php?cat=".$category_id."'>".$category_name."</a>
                </div>";

                //get info for topic
                $topic_query = $pdo->prepare("SELECT topic_id, poster_id, `name`, date_new_reply, replies FROM posts WHERE category_id = :i AND post_order = '1' AND visibility = '1' ORDER BY date_new_reply DESC LIMIT 1");
                $topic_query->bindParam('i', $category_id);
                $topic_query->execute();
                
                //if there are no topics in the category
                if($topic_query->rowCount() == 0) {
                    $content .= 
                    "<div id='post-content--small'>There are no topics. Strange!</div>
                    ";
                }

                foreach ($topic_query->fetchAll() as $row) {
                    $topic_id = $row['topic_id'];
                    $poster_id = $row['poster_id'];
                    $topic_name = $row['name'];
                    $last_reply = $row['date_new_reply'];
                    $last_reply = strtotime($last_reply);
                    $replies = $row['replies'];

                    $poster_query = $pdo->prepare("SELECT `id`, `username`, `profile_picture` FROM users WHERE id = :i");
                    $poster_query->bindParam('i', $poster_id);
                    $poster_query->execute();

                    foreach($poster_query->fetchAll() as $row) {
                        $poster_id = $row['id'];
                        $poster_name = $row['username'];
                        $profile_picture = $row['profile_picture'];

                        $content .= 
                        "<div id='post-content--small'>
                            <a href='profile.php?id=".$poster_id."'>
                                <div id='profile-picture--small'>
                                    <image src='".$profile_picture."'></image>
                                </div>
                            </a>

                            <div id='topic-post__info'>
                                <a href='topic.php?id=".$topic_id."' id='topic-post__title'>".$topic_name."</a><br>
                                <a href='profile.php?id=".$poster_id."' id='topic-post__poster'>".$poster_name."</a> | 
                                <span id='post__date'>".date("n/j/y, g:i A", $last_reply)."
                                <br>Replies: ".$replies."</span>
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