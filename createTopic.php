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
            <span class='container__main-header'>Create Topic</span><br>
            <?php 
            include('connect.php');
            if(isset($_SESSION['id'])) {
                $echo = 
                "<form method='post'>
                    <input type='text' class='post' name='name' placeholder='Name of Topic' required autofocus><br><br>
                    <select name='cat' required>
                    <option value='0'>Select Category</option>";
                $catQuery = $pdo->prepare("SELECT * FROM categories");
                $catQuery->execute();
                foreach ($catQuery->fetchAll() as $row) {
                    $category_name = $row['name'];
                    $category_id = $row['id'];
                    $echo .= "<option value='".$category_id."'>".$category_name."</option>";
                }
                $echo .= "</select><br><br><textarea name='content' placeholder='Tell the world what you have to say!'
                rows='17.5' cols='100' required></textarea><br><br>
                <input type='submit' name='submit' value='Create Topic'></form>";
                echo $echo;
                if(isset($_POST['submit'])) {
                    $poster_id = $_SESSION['id'];
                    $name = $_POST['name'];
                    $cat = $_POST['cat'];
                    $content = $_POST['content'];
                    switch (true) {
                        case (empty($name) || $category_id == 0 || empty($content)):
                            die("One or more of the required fields were not properly filled out.");
                        case (strlen($name) > 100):
                            die("The name of your topic cannot exceed 100 characters.");
                        case ($cat == 1):
                            $isLocked = 1;
                    }
                    $new_topic_id = 0;
                    $topic_query = $pdo->prepare('SELECT topic_id FROM posts ORDER BY topic_id DESC LIMIT 1');
                    $topic_query->execute();
                    foreach($topic_query->fetch() as $row) {
                        $new_topic_id = $row['topic_id'];
                    }
                    $new_topic_id++;
                    $createTopic = $pdo->prepare("INSERT INTO posts (category_id, topic_id, poster_id, post_order, `name`, content, date_created, date_new_reply, visibility, isLocked) 
                                                  VALUES (:category_id, :topic_id, :poster_id, :topic_name, :content, now(), now(), '1', '0')");

                    $createTopic->bindParam('category_id', $cat);
                    $createTopic->bindParam('topic_id', $new_topic_id);
                    $createTopic->bindParam('poster_id', $poster_id);
                    $createTopic->bindParam('topic_name', $name);
                    $createTopic->bindParam('content', $content);

                    $createTopic->execute();

                    $updateCat = $pdo->prepare("UPDATE categories SET topics = topics + 1 AND posts = posts + 1 WHERE id = :i");
                    $updateCat->bindParam('i', $cat);
                    $updateCat->execute();
                    header('Location: topic.php?id='.$new_topic_id.'');
                }
            } else {
                echo "Oops! You need to be <a href='login.php'>logged in</a> to use this!";
            }
            ?>
        </div>
    </body>
</html>