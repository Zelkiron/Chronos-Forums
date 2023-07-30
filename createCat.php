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
        <div class='container'>
            <?php
            include('connect.php');
            if(isset($_SESSION['id'])) {
                $user_id = $_SESSION['id'];
                $dbUser = $pdo->prepare('SELECT * FROM users WHERE id = :i');
                $dbUser->bindParam('i', $user_id);
                $dbUser->execute();
                $rank = 0;
                foreach ($dbUser->fetchAll() as $row) {
                    $rank += $row['rank'];
                }
                switch (true) {
                    case ($rank < 3):
                        die('You are not a high enough rank to access this page!');
                    case ($rank == 4): 
                        echo
                        "<span class='title introTitle'>Create Category</span>
                        <form method='post'>
                        <input type='text' class='acc' name='catName' placeholder='Name' required autofocus><br>
                        <textarea name='catDesc' placeholder='Description' rows='5' cols='40' required></textarea><br>
                        <select name='visibility' required>
                            <option value='0'>Visibility</option>
                            <option value='1'>Everyone</option>
                            <option value='2'>Moderators and Up</option>
                            <option value='3'>Only Admins</option>
                            <option value='0'>Not Visible</option>
                        </select><br>
                        <input type='submit' class='button' name='submit' value='Submit'>
                        </form>";
                        if(isset($_POST['submit'])) {
                            $name = $_POST['catName'];
                            $desc = $_POST['catDesc'];
                            $rank = $_POST['visibility'];
                            $search = $pdo->prepare('SELECT * FROM categories WHERE name = :n');
                            $search->bindParam('n', $name);
                            $search->execute();
                            switch (true) {
                                case (empty($name) || empty($desc) || empty($rank)):
                                    die('One of the required fields was left blank.');
                                case ($search->rowCount() == 1):
                                    die('This category already exists!');
                            }
                            $insert = $pdo->prepare("INSERT INTO categories (name, `desc`, topics, posts, visibility) VALUES (:n, :d, '0', '0', :r)");
                            $insert->bindParam('n', $name);
                            $insert->bindParam('d', $desc);
                            $insert->bindParam('r', $rank);
                            $insert->execute();
                            echo 'Category successfully created!';
                        }
                }
            } else {
                echo 'You do not have access to this page!';
            }
            ?>
        </div>
    </body>
</html>