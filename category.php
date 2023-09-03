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
        <div class='container big-page'>
            <?php 
            include('connect.php');
            try {
            if (isset($_GET['cat'])) {
                //get cat id from url and sanitize just in case to prevent xss attacks
                $url_cat_id = htmlspecialchars($_GET['cat']);

                //query for category information
                $get_category_info_query = $pdo->prepare("SELECT `name`, `desc`, topics, posts FROM categories WHERE id = :i");
                $get_category_info_query->bindParam('i', $url_cat_id);
                $get_category_info_query->execute();
                
                //if the category is not found, redirect to 404
                if($get_category_info_query->rowCount() == 0) {
                    header('Location: 404.html');
                }

                //intialize content variable
                $content = "";

                //get everything needed from the categories table
                foreach($get_category_info_query->fetchAll() as $row) {
                    $category_name = $row['name'];
                    $category_description = $row['desc'];
                    $cat_num_topics = $row['topics'];
                    $cat_num_replies = $row['replies'];
                    
                    //append category variables to content variable
                    $content .= "<span class='container__main-header'>".$category_name."</span>
                    <br><span id='catDesc'>".$category_description."</span><br><br>This category has ".$cat_num_topics." topics and ".$cat_num_replies." replies.<br>
                    <a href='createTopic.php'><button class='button'>Create a Topic</button></a><hr><br>";
                }

                //query for topics in this category
                $topic_query = $pdo->prepare("SELECT topic_id, poster_id, `name`, date_created, replies, rep FROM posts WHERE$url_cat_id = :c AND visibility = '1' AND post_order = '1' ORDER BY date_new_reply DESC");
                $topic_query->bindParam('c', $url_cat_id);
                $topic_query->execute();

                //get everything needed from topics table
                foreach($topic_query->fetchAll() as $row) {
                    $topic_id = $row['topic_id'];
                    $poster_id = $row['poster_id'];
                    $topic_name = $row['name'];
                    $topic_date = $row['date_created'];
                    $topic_replies = $row['replies'];
                    $topic_rep = $row['rep'];

                    //convert topic_date to integer for date() function
                    $topic_date = strtotime($topic_date);

                     //query for information about the topic poster 
                    $poster_query = $pdo->prepare("SELECT username, pfp FROM users WHERE id = :i");
                    $poster_query->bindParam('i', $poster_id);
                    $poster_query->execute();

                    //get everything we need about the poster
                    foreach($poster_query->fetchAll() as $row) {
                        $poster_name = $row['username'];
                        $poster_pfp = $row['pfp'];
                        
                        $content .= "<div id='post-content--small'>
                        <a href='profile.php?id=".$poster_id."'>
                            <div id='profile-picture--small'>
                                <image src='".$poster_pfp."'></image>
                            </div>
                        </a>

                        <div id='topic-post__info'>
                            <a href='topic.php?id=".$topic_id."' id='topic-post__title'>".$topic_name."</a><br>
                            <a href='profile.php?id=".$poster_id."' id='topic-post__poster'>".$poster_name."</a> | 
                            <span id='post__date'>".date("n/j/y, g:i A", $topic_date)."
                            <br>Replies: ".$topic_replies." | Reputation: ".$topic_rep."</span>
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
        } catch (Exception $e) {
            die($e);
        }
            ?> 
        </div>
    </body>
</html>