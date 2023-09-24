<?php 
session_start();
require('Post.php');
require('Header.php');
require('functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
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
            if(isset($_GET['id'])) {
                //prevent xss attacks
                $get_topic_id_from_url = htmlspecialchars($_GET['id']);

                //initalize content variable
                $content = "";

                ##############
                # TOPIC POST #
                # TOPIC POST #
                # TOPIC POST #
                ##############

                $category_id = 0;

                //query for topic post information
                $topic_post_query = $pdo->prepare("SELECT category_id, poster_id, `name`, content, reputation, date_created, date_edited, replies, is_locked, post_order FROM posts WHERE topic_id = :i AND post_order = '1'");
                $topic_post_query->bindParam('i', $get_topic_id_from_url);
                $topic_post_query->execute();

                //get all the TOPIC information
                foreach($topic_post_query->fetchAll() as $row) {
                    $category_id = $row['category_id'];
                    $poster = $row['poster_id'];
                    $post_name = $row['name'];
                    $post_content = $row['content'];
                    $post_rep = $row['reputation'];
                    $date_created = $row['date_created'];
                    $date_edited = $row['date_edited'];
                    $replies = $row['replies'];
                    $is_locked = $row['is_locked'];

                    //convert timestamp to integer for date() function
                    $date_created = strtotime($date_created);
                    $date_edited = strtotime($date_edited);
                }

                //got id stored in $topic-post__poster var, use for getting info about the OP
                $get_topic_poster_query = $pdo->prepare("SELECT username, profile_picture, `rank`, reputation, number_of_posts FROM users WHERE id = :i");
                $get_topic_poster_query->bindParam('i', $poster);
                $get_topic_poster_query->execute();

                //get the rest of the info about the OP
                foreach($get_topic_poster_query->fetchAll() as $row) {
                    $poster_name = $row['username'];
                    $poster_rank = $row['rank'];
                    $poster_profile_picture = $row['profile_picture'];
                    $poster_reputation = $row['reputation'];
                    $poster_number_of_posts = $row['number_of_posts'];
                
                    //now append all the necessary variables to the post 
                    $content .= 
                    "<span class='container__main-header'>".$post_name."</span><br>
                    <div id='post'>
                        <div id='user-information'>
                            <div id='profile-picture--big'>
                                <image src='".$poster_profile_picture."'></image>
                                <br>
                                ".$poster_name."
                                <br>
                                ".convertRankToTitle($poster_rank)."
                                ".$poster_number_of_posts." posts
                                <br>
                                ".$poster_reputation." reputation
                            </div>
                        </div>
                        <div id='post-content'>
                        <span id='topic-date'>Created on ".date("n/j/Y, g:i A", $date_created)." | ".$post_rep." reputation</span><br>
                            ".$post_content."
                        </div>
                    </div> <br>";
                }

                ###########
                # REPLIES # 
                # REPLIES #
                # REPLIES #
                ###########

                //get all the replies for this topic
                $get_topic_replies_query = $pdo->prepare("SELECT poster_id, post_order, content, reputation, date_created, date_edited FROM posts WHERE topic_id = :i AND visibility = '1' AND post_order > 1 ORDER BY date_created");
                $get_topic_replies_query->bindParam('i', $get_topic_id_from_url);
                $get_topic_replies_query->execute(); 

                //get all the REPLY information

                ## IMPORTANT ##
                //NEST ALL THIS INTO THE REPLY FOREACH BECAUSE WE ARE LOOPING AROUND ALL THE POSSIBLE REPLIES

                $most_recent_post_order = 1;

                foreach($get_topic_replies_query->fetchAll() as $row) {
                    $reply_poster = $row['poster_id'];
                    $reply_post_order = $row['post_order'];
                    $reply_content = $row['content'];
                    $reply_reputation = $row['reputation'];
                    $reply_date_created = $row['date_created'];
                    $reply_last_edited = $row['date_edited'];

                    $most_recent_post_order = max($most_recent_post_order, $reply_post_order);

                    //set replydate to int 
                    $reply_date_created = strtotime($reply_date_created);
                
                    $reply_poster_query = $pdo->prepare("SELECT id, username, profile_picture, `rank`, reputation, number_of_posts FROM users WHERE id = :i");
                    $reply_poster_query->bindParam('i', $reply_poster);
                    $reply_poster_query->execute();

                    //get the rest of the info about the OP
                    foreach($reply_poster_query->fetchAll() as $row) {
                        $reply_poster_id = $row['id'];
                        $reply_poster_name = $row['username'];
                        $reply_poster_rank = $row['rank'];
                        $reply_poster_profile_picture = $row['profile_picture'];
                        $reply_poster_reputation = $row['reputation'];
                        $reply_poster_number_of_posts = $row['number_of_posts'];
                        
                        //now append all the necessary variables to the post 
                        $content .=
                        "<div id='post'>
                            <div id='user-information'>
                                <a href='profile.php?id=".$reply_poster_id."'>
                                    <div id='profile-picture--big'>
                                        <image src='".$reply_poster_profile_picture."'></image>
                                    </div>
                                    <br>
                                    ".$reply_poster_name."
                                </a>
                                <br>
                                ".convertRankToTitle($reply_poster_rank)."
                                ".$reply_poster_number_of_posts." posts
                                <br>
                                ".$reply_poster_reputation." reputation
                            </div>
                            <div id='post-content'>
                                <span id='topic-date'>Created on ".date("n/j/Y, g:i A", $reply_date_created)."</span><br>
                                    ".$reply_content."
                                <hr>
                                <div id='post__footer'>
                                    ".$reply_reputation." reputation | 
                                </div>
                            </div>
                        </div> 
                        <br>";
                    }
                }

                ################
                # SUBMIT REPLY #
                # SUBMIT REPLY #  
                # SUBMIT REPLY #
                ################

                //see if the topic is locked, if not show reply box if user is logged in 
                if($is_locked == 1) {
                    $content .= "<br>This topic is currently locked and not accepting replies.";
                } else {
                    if(isset($_SESSION['id'])) {
                        //echo the form so the user can post a reply
                        $content .= 
                        "<form method='post'>
                        <textarea name='content' placeholder='Tell the world what you have to say!'
                        rows='10' cols='100' required></textarea><br>
                        <input type='submit' name='submit' class='button' value='Create Post'></form>";
                        if(isset($_POST['submit'])) {
                            //update post order
                            $most_recent_post_order += 1;

                            //get content
                            $post_content = $_POST['content']; 

                            $post = new Post('1', $get_topic_id_from_url, $_SESSION['id'], $most_recent_post_order, $post_content);
                            $post->createPost();
                            header('Location: topic.php?id='.$get_topic_id_from_url.'');
                        }
                    } else {
                        $content .= "<br>You need to be <a href='login.php'>logged in</a> to post a reply.";
                    }
                } 
                //now show what we have stored in $content so far
                echo $content;
            } else {
                //redirect to 404 page 
                header("Location: 404.html");
            }
            ?>
        </div>
    </body>
</html>