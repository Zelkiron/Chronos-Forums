<?php 
require('functions.php');
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
            <span class='header__title'>CHRONOS</span>
            <a id='header__links' href='index.php'>Home</a>
            <a id='header__links' href='new_posts.php'>Recent Posts</a>
            <a id='header__links' href='status_updates.php'>Recent Status Updates</a>
            <a id='header__links' href='members.php'>Member List</a>
            <a id='header__links' href='staff.php'>Staff List</a>
            <a id='header__links' href='about.php'>About Me</a>
            <a id='header__links' href='#' onclick='profile()'>Profile</a>
            <?php
            if(checkIfAdmin($_SESSION['id']) == true) {
                echo "<a id='header__links' href='apanel.php'>Admin Panel</a>";
            }
            ?>
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
                        "<span class='container__main-header'>Create Category</span>
                        <form method='post'>
                        <input type='text' class='default-input' name='catName' placeholder='Name' required autofocus><br>
                        <textarea name='category-description' placeholder='Description' rows='5' cols='40' required></textarea><br>
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
                            $desc = $_POST['category-description'];
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