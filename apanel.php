<?php 
session_start();
require('connect.php');
require('Header.php');
if($_SESSION['rank'] < 3) {
    header('Location: 403.html');
}
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
        <div class='container reg-page'>
            <span class='container__main-title'>Admin Panel</span>
            <hr>
            <form method='post'>
                <span class='title container__main-title sub-title'>Create a Category</span><br>
                <input type='text' class='default-input' name='name' placeholder='Name' required /><br>
                <textarea name='desc' placeholder='Description' rows='10' cols='75' required></textarea><br>
                <input type='text' class='default-input' name='visibility' placeholder='Visibility' /><br>
                <input type='submit' class='button' name='create_category' value='Create Category'><br>
            </form>
            <hr>
            <form method='post'>
                <span class='title container__main-title sub-title'>Delete User</span><br>
                <input type='text' class='default-input' name='user_to_delete_id' placeholder='User ID' required /><br> 
                <b style='color: red'>WARNING: THIS CANNOT BE UNDONE</b><br>
                For security reasons, you can only delete users who are one rank smaller than you. <br>
                <input type='submit' class='button' name='delete_user' value='Delete User' /><br>
            </form>
            <hr>
            <form method='post'>
                <span class='title container__main-title sub-title'>Change Rank of User</span><br>
                <input type='text' class='default-input' name='user_to_change_id' placeholder='User ID' required /><br>
                <input type='text' class='default-input' name='rank_to_change_to' placeholder='Rank (Numerical)' required /> <br>
                For security reasons, once again, you can change the rank of users who are originally two ranks smaller than you. <br>
                <input type='submit' class='button' name='change_rank_of_user' value='Promote/Demote User' /><br>
            </form>
            <?php
            function createCategory(PDO $pdo) {
                $name = trim($_POST['name']);
                $desc = trim($_POST['desc']);
                $visibility = 1;
                if(isset($_POST['visibility']) && is_numeric($_POST['visibility'])) {
                    $visibility = $_POST['visibility'];
                }
                $check_for_duplicate_category_query = $pdo->prepare('SELECT `name` FROM `categories` WHERE `name` = :n');
                $check_for_duplicate_category_query->bindParam('n', $name);
                if($check_for_duplicate_category_query->rowCount() == 1) {
                    die('This category already exists.');
                }
                $create_category_query = $pdo->prepare('INSERT INTO `categories` (`name`, `description`, `visibility`) VALUES (:n, :d, :v)');
                $create_category_query->bindParam('n', $name);
                $create_category_query->bindParam('d', $desc);
                $create_category_query->bindParam('v', $visibility);
                if($create_category_query->execute()) {
                    echo('Category successfully created.');
                } else {
                    echo('Something went wrong. Talk to the owner about this.');
                }
            }
            function deleteUser(PDO $pdo) {
                $user_to_delete_id = trim($_POST['user_to_delete_id']);
                if (!is_numeric($user_to_delete_id)) {
                    die('ID must only consist of numbers.');
                }
                $find_user_to_delete_query = $pdo->prepare('SELECT `id` FROM users WHERE id = :i AND `rank` < :admin_rank');
                $find_user_to_delete_query->bindParam('i', $user_to_delete_id);
                $find_user_to_delete_query->bindParam('admin_rank', $_SESSION['rank']);
                $find_user_to_delete_query->execute();

                if($find_user_to_delete_query->rowCount() == 1) {
                    $delete_user_query = $pdo->prepare('DELETE FROM users WHERE id = :i AND `rank` < :admin_rank');
                    $delete_user_query->bindParam('i', $user_to_delete_id);
                    $delete_user_query->bindParam('admin_rank', $_SESSION['rank']);
                    $delete_user_query->execute();

                    $set_auto_increment_to_this_id = $user_to_delete_id - 1;
                    $update_auto_increment_query = $pdo->prepare('ALTER TABLE users AUTO_INCREMENT=:i');
                    $update_auto_increment_query->bindParam('i', $set_auto_increment_to_this_id);
                    $update_auto_increment_query->execute();
 
                    echo('User successfully deleted.');
                } else {
                    die('Either a user with the ID '.$user_to_delete_id.' does not exist, or they are the same/higher rank compared to you.');
                }
            } 
            function changeRankOfUser(PDO $pdo) {
                $user_to_change_id = trim($_POST['user_to_change_id']);
                $rank_to_change_to = trim($_POST['rank_to_change_to']);

                if($user_to_change_id == $_SESSION['id']) {
                    die('You cannot change your own rank!');
                }

                if($rank_to_change_to < -1) {
                    die('That\'s too low of a rank!');
                }

                if($rank_to_change_to == 0) {
                    die('I can\'t let you manually deactivate accounts, for now.');
                }

                if($rank_to_change_to > 4) {
                    die('That\'s too high of a rank!');
                }

                if(($_SESSION['rank'] - $rank_to_change_to) < 1) {
                    die("This user will be too close in rank to you. Please pick a different rank to change them to.");
                }

                $find_user_to_change_query = $pdo->prepare('SELECT `id` FROM users WHERE id = :i AND `rank` < :r');
                $find_user_to_change_query->bindParam('i', $user_to_change_id);
                $find_user_to_change_query->bindParam('r', $_SESSION['rank']);
                $find_user_to_change_query->execute();

                if($find_user_to_change_query->rowCount() == 1) {
                    $change_rank_query = $pdo->prepare('UPDATE users SET `rank` = :r WHERE id = :i');
                    $change_rank_query->bindParam('r', $rank_to_change_to);
                    $change_rank_query->bindParam('i', $user_to_change_id);
                    $change_rank_query->execute();
                    echo('Rank successfully changed.');
                } else {
                    die('Either a user with the ID '.$user_to_change_id.' does not exist, they are the same rank as you, or they are a higher rank than you.');
                }
            }
            if(isset($_POST['create_category'])) {
                createCategory($pdo);
            }
            if(isset($_POST['delete_user'])) {
                deleteUser($pdo);
            }
            if(isset($_POST['change_rank_of_user'])) {
                changeRankOfUser($pdo);
            }
            ?>
        </div>
    </body>
</html>