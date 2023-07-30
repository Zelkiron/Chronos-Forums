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
            <span class='title introTitle'>Create Topic</span><br>
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
                    $catName = $row['name'];
                    $cat_id = $row['id'];
                    $echo .= "<option value='".$cat_id."'>".$catName."</option>";
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
                    $isLocked = 0;
                    switch (true) {
                        case (empty($name) || $cat_id == 0 || empty($content)):
                            die("One or more of the required fields were not properly filled out.");
                        case (strlen($name) > 100):
                            die("The name of your topic cannot exceed 100 characters.");
                        case ($cat == 1):
                            $isLocked = 1;
                    }
                    $createTopic = $pdo->prepare("INSERT INTO topics (cat_id, poster_id, name, content, dateCreated, dateNewReply, visibility, isLocked) VALUES (:c, :p, :n, :t, now(), now(), '1', :l)");
                    $createTopic->bindParam('c', $cat);
                    $createTopic->bindParam('p', $poster_id);
                    $createTopic->bindParam('n', $name);
                    $createTopic->bindParam('t', $content);
                    $createTopic->bindParam('l', $isLocked);
                    $createTopic->execute();
                    $updateCat = $pdo->prepare("UPDATE categories SET topics = topics + 1 AND replies = replies + 1 WHERE id = :i");
                    $updateCat->bindParam('i', $cat);
                    $updateCat->execute();
                    echo "Topic successfully created!";
                }
            } else {
                echo "Oops! You need to be <a href='login.php'>logged in</a> to use this!";
            }
            ?>
        </div>
    </body>
</html>