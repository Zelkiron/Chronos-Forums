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
            $header->checkIfAdmin();
            echo $header->getHeader();
            ?>
        </div>
            <br>
        <div class='container reg-page'>
            <span class='container__main-header'>Admin Panel</span>
            <hr>
            <form method='post'>
                <span class='title container__main-header sub-title'>Create a Category</span><br>
                <input type='text' class='default-input' name='name' placeholder='Name' required /><br>
                <textarea name='desc' placeholder='Description' rows='10' cols='75' required></textarea><br>
                <input type='text' class='default-input' name='visibility' placeholder='Visibility' /><br>
                <input type ='submit' class='button' name='create_cat' value='Create Category'><br>
                <?php
                if(isset($_POST['create_cat'])) {
                    $name = trim($_POST['name']);
                    $desc = trim($_POST['desc']);
                    $visibility = 1;
                    if(isset($_POST['visibility']) && is_numeric($_POST['visibility'])) {
                        $visibility = $_POST['visibility'];
                    }
                    $checkDuplicate = $pdo->prepare('SELECT `name` FROM `categories` WHERE `name` = :n');
                    $checkDuplicate->bindParam('n', $name);
                    if($checkDuplicate->rowCount() == 1) {
                        die('This category already exists.');
                    }
                    $insert = $pdo->prepare('INSERT INTO `categories` (`name`, `desc`, `visibility`) VALUES (:n, :d, :v)');
                    $insert->bindParam('n', $name);
                    $insert->bindParam('d', $desc);
                    $insert->bindParam('v', $visibility);
                    $insert->execute();
                    echo('Category successfully created.');
                }
                ?>
            </form>
        </div>
    </body>
</html>