<?php 
session_start(); 
require('connect.php');
require('Header.php');
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
            <span class='container__main-header'>Create Topic</span><br>
            <?php 
            include('connect.php');
            if(isset($_SESSION['id'])) {
                $echo = 
                "<form method='post'>
                    <input type='text' class='default-input' name='name' placeholder='Name of Topic' required autofocus><br><br>
                    <select name='category_id' required>
                    <option value='0'>Select Category</option>";
                $get_category_info_query = $pdo->prepare("SELECT * FROM categories WHERE visibility <= :user_rank");
                $get_category_info_query->bindParam('user_rank', $_SESSION['rank']);
                $get_category_info_query->execute();
                foreach ($get_category_info_query->fetchAll() as $row) {
                    $category_name = $row['name'];
                    $category_id = $row['id'];
                    $echo .= "<option value='".$category_id."'>".$category_name."</option>";
                }
                $echo .= "</select><br><br><textarea name='content' placeholder='Tell the world what you have to say!'
                rows='17.5' cols='100' required></textarea><br><br>
                <input type='submit' class='button' name='submit' value='Create Topic'></form>";
                echo $echo;
                if(isset($_POST['submit'])) {
                    $poster_id = $_SESSION['id'];
                    $poster_name = $_POST['name'];
                    $post_category_id = $_POST['category_id'];
                    $content = $_POST['content'];
                    switch (true) {
                        case (empty($poster_name) || $category_id == 0 || empty($content)):
                            die("One or more of the required fields were not properly filled out.");
                        case (strlen($poster_name) > 100):
                            die("The name of your topic cannot exceed 100 characters.");
                        case ($post_category_id == 1):
                            $isLocked = 1;
                    }
                    $new_topic_id = 0;
                    $topic_query = $pdo->prepare('SELECT topic_id FROM posts ORDER BY topic_id DESC LIMIT 1');
                    $topic_query->execute();
                    foreach($topic_query->fetch() as $row) {
                        $topic_id = $row['topic_id'];
                        $new_topic_id = max($topic_id, $new_topic_id);
                    }
                    $new_topic_id += 1;
                    $createTopic = $pdo->prepare("INSERT INTO posts (category_id, topic_id, poster_id, post_order, `name`, content, date_created, date_new_reply) 
                                                  VALUES (:category_id, :topic_id, :poster_id, '1', :topic_name, :content, now(), now())");
                    $createTopic->bindParam('category_id', $post_category_id);
                    $createTopic->bindParam('topic_id', $new_topic_id);
                    $createTopic->bindParam('poster_id', $poster_id);
                    $createTopic->bindParam('topic_name', $poster_name);
                    $createTopic->bindParam('content', $content);

                    $createTopic->execute();

                    $updateCat = $pdo->prepare("UPDATE categories SET topics = topics + 1 AND posts = posts + 1 WHERE id = :i");
                    $updateCat->bindParam('i', $post_category_id);
                    $updateCat->execute();

                    $update_user_info = $pdo->prepare('UPDATE users SET number_of_posts = number_of_posts + 1 WHERE id = :i');
                    $update_user_info->bindParam('i', $_SESSION['id']);
                    $update_user_info->execute();

                    header('Location: topic.php?id='.$new_topic_id.'');
                }
            } else {
                echo "Oops! You need to be <a href='login.php'>logged in</a> to use this!";
            }
            ?>
        </div>
    </body>
</html>