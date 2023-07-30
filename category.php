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
            if (isset($_GET['cat'])) {
                //get cat id from url and sanitize just in case to prevent xss attacks
                $cat_id = htmlspecialchars($_GET['cat']);

                //query for category
                $catQuery = $pdo->prepare("SELECT name, `desc`, topics, replies FROM categories WHERE id = :i");
                $catQuery->bindParam('i', $cat_id);
                $catQuery->execute();

                //intialize content variable
                $content = "";

                //get everything needed from the categories table
                foreach($catQuery->fetchAll() as $row) {
                    $catName = $row['name'];
                    $catDesc = $row['desc'];
                    $catNumTopics = $row['topics'];
                    $catNumReplies = $row['replies'];
                    
                    //append category variables to content variable
                    $content .= "<span class='title introTitle'>".$catName."</span>
                    <br><span id='catDesc'>".$catDesc."</span><br><br>This category has ".$catNumTopics." topics and ".$catNumReplies." replies.<br>
                    <a href='createTopic.php'><button class='button'>Create a Topic</button></a><hr><br>";
                }

                //query for topics in this category
                $topicQuery = $pdo->prepare("SELECT id, poster_id, name, dateCreated, replies FROM topics WHERE cat_id = :c AND visibility = '1' ORDER BY dateNewReply DESC");
                $topicQuery->bindParam('c', $cat_id);
                $topicQuery->execute();

                //get everything needed from topics table
                foreach($topicQuery->fetchAll() as $row) {
                    $topic_id = $row['id'];
                    $poster_id = $row['poster_id'];
                    $topicName = $row['name'];
                    $topicDate = $row['dateCreated'];
                    $topicReplies = $row['replies'];

                    //convert topicDate to integer for date() function
                    $topicDate = strtotime($topicDate);

                     //query for information about the topic poster 
                    $posterQuery = $pdo->prepare("SELECT username, pfp FROM users WHERE id = :i");
                    $posterQuery->bindParam('i', $poster_id);
                    $posterQuery->execute();

                    //get everything we need about the poster
                    foreach($posterQuery->fetchAll() as $row) {
                        $posterName = $row['username'];
                        $pfp = $row['pfp'];
                        
                        $content .= "<div id='smallPostContent'>
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

                //echo finished content variable
                echo $content;
            } else {
                //head to this please i'm too lazy to print 404 not found everytime
                header("Location: 404.html");
            } 
            ?> 
        </div>
    </body>
</html>