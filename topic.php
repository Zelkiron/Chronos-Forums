<?php session_start(); ?>
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
            include('functions.php');

            if(isset($_GET['id'])) {
                //prevent xss attacks
                $get_id = htmlspecialchars($_GET['id']);

                //initalize content variable
                $content = "";

                ##############
                # TOPIC POST #
                # TOPIC POST #
                # TOPIC POST #
                ##############

                //query for topic post information
                $topicQuery = $pdo->prepare("SELECT id, poster_id, name, content, dateCreated, dateEdited, isLocked FROM topics WHERE id = :i");
                $topicQuery->bindParam('i', $get_id);
                $topicQuery->execute();

                //get all the TOPIC information
                foreach($topicQuery->fetchAll() as $row) {
                    $topicPoster = $row['poster_id'];
                    $topicName = $row['name'];
                    $topicContent = $row['content'];
                    $topicDate = $row['dateCreated'];
                    $topicEdited = $row['dateEdited'];
                    $isLocked = $row['isLocked'];

                    //convert timestamp to integer for date() function
                    $topicDate = strtotime($topicDate);
                }

                    //got id stored in $topicPoster var, use for getting info about the OP
                    $posterQuery = $pdo->prepare("SELECT username, pfp, rank FROM users WHERE id = :i");
                    $posterQuery->bindParam('i', $topicPoster);
                    $posterQuery->execute();

                    //get the rest of the info about the OP
                    foreach($posterQuery->fetchAll() as $row) {
                        $posterName = $row['username'];
                        $posterRank = $row['rank'];
                        $poster_pfp = $row['pfp'];
                        $posterRank = $row['rank'];
                    
                        //now append all the necessary variables to the post 
                        $content .= 
                        "<span class='title introTitle'>".$topicName."</span><br>
                        <div id='post'>
                            <div id='userInfo'>
                                <div id='big_pfp'>
                                    <image src='".$poster_pfp."'></image>
                                    <br>
                                    ".$posterName."
                                    <br>
                                    ".getRank($posterRank)."
                                </div>
                            </div>
                            <div id='postContent'>
                            <span id='topicDate'>Created on ".date("n/j/y, g:i A", $topicDate)."</span><br>
                                ".$topicContent."
                            </div>
                        </div> <br>";
                    }

                    ###########
                    # REPLIES # 
                    # REPLIES #
                    # REPLIES #
                    ###########

                    //get all the replies for this topic
                    $replyQuery = $pdo->prepare("SELECT poster_id, content, rep, dateCreated, dateEdited FROM replies WHERE topic_id = :i AND visibility = '1' ORDER BY dateCreated");
                    $replyQuery->bindParam('i', $get_id);
                    $replyQuery->execute(); 

                    //get all the REPLY information

                    ## IMPORTANT ##
                    //NEST ALL THIS INTO THE REPLY FOREACH BECAUSE WE ARE LOOPING AROUND ALL THE POSSIBLE REPLIES

                    foreach($replyQuery->fetchAll() as $row) {
                        $replyPoster = $row['poster_id'];
                        $replyContent = $row['content'];
                        $replyRep = $row['rep'];
                        $replyDate = $row['dateCreated'];
                        $replyEdited = $row['dateEdited'];

                        //set replydate to int 
                        $replyDate = strtotime($replyDate);
                    
                        $rPosterQuery = $pdo->prepare("SELECT username, pfp, rank FROM users WHERE id = :i");
                        $rPosterQuery->bindParam('i', $replyPoster);
                        $rPosterQuery->execute();

                        //get the rest of the info about the OP
                        foreach($rPosterQuery->fetchAll() as $row) {
                            $rPosterName = $row['username'];
                            $rPosterRank = $row['rank'];
                            $rPoster_pfp = $row['pfp'];
                            $rPosterRank = $row['rank'];
                            
                            //now append all the necessary variables to the post 
                            $content .=
                            "<div id='post'>
                                <div id='userInfo'>
                                    <div id='big_pfp'>
                                        <image src='".$rPoster_pfp."'></image>
                                        <br>
                                        ".$rPosterName."
                                        <br>
                                        ".getRank($rPosterRank)."
                                    </div>
                                </div>
                                <div id='postContent'>
                                    <span id='topicDate'>Created on ".date("n/j/y, g:i A", $replyDate)."</span><br>
                                    ".$replyContent."
                                </div>
                            </div> <br>";
                        }
                    }

                    ################
                    # SUBMIT REPLY #
                    # SUBMIT REPLY #  
                    # SUBMIT REPLY #
                    ################

                    //see if the topic is locked, if not show reply box if user is logged in 
                    if($isLocked == 1) {
                        $content .= "<br>This topic is currently locked and not accepting replies.";
                    } else {
                        if(isset($_SESSION['id'])) {
                            //echo the form so the user can post a reply
                            $content .= 
                            "<br><br><form method='post'>
                            <textarea name='content' placeholder='Tell the world what you have to say!'
                            rows='10' cols='100' required></textarea><br><br>
                            <input type='submit' name='submit' value='Create Post'></form>";
                            if(isset($_POST['submit'])) {

                                //get content
                                $postContent = $_POST['content'];

                                //prepare query for inserting reply
                                $postQuery = $pdo->prepare("INSERT INTO replies (topic_id, poster_id, content, rep, dateCreated) VALUES (:t, :p, :c, '0', now())");
                                $postQuery->bindParam('t', $get_id);
                                $postQuery->bindParam('p', $_SESSION['id']);
                                $postQuery->bindParam('c', $postContent);
                                $postQuery->execute();

                                //refresh the page so the user can see the reply
                                header("Refresh: 0");
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