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
            require('connect.php');
            if(isset($_SESSION['id'])) {
                echo "<p class='title introTitle'>Change Password</p>
                <form method='post'>
                    <input type='password' class='acc' name='password' placeholder='New Password' required autofocus><br>
                    <input type='submit' class='button' name='submit'>
                </form>";
                if(isset($_POST['submit'])) {
                    $password = $_POST['password'];
                    $q = $pdo->prepare('UPDATE users SET password = :p WHERE id = :i');
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $q->bindParam('p', $password);
                    $q->bindParam('i', $SESSION['id']);
                    echo 'Your password was successfully changed!';
                }
            } else {
                if(isset($_GET['email'], $_GET['code'])) {
                    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
                    $code = htmlspecialchars($_GET['code']);
                    $query = $pdo->prepare('SELECT * FROM users WHERE email = :e');
                    $query->bindParam('e', $email);
                    $query->execute();
                    $tempCode = '';
                    foreach($query->fetchAll() as $row) {
                        $tempCode .= $row['tempCode'];
                    }
                    switch (true) {
                        case ($query->rowCount() == 1 && password_verify($code, $tempCode) == 1):
                            echo "<p class='title introTitle'>Change Password</p>
                                <form method='post'>
                                    <input type='password' class='acc' name='password' placeholder='New Password' required autofocus><br>
                                    <input type='submit' class='button' name='submit'>
                                </form>";
                                if(isset($_POST['submit'])) {
                                    $password = $_POST['password'];
                                    $hash = password_hash($password, PASSWORD_DEFAULT);
                                    $q = $pdo->prepare('UPDATE users SET password = :p, tempCode = null WHERE email = :e');
                                    $q->bindParam('p', $hash);
                                    $q->bindParam('e', $email);
                                    $q->execute();
                                    echo 'Your password was successfully changed!';
                                }
                        default: 
                            die("<p class='title introTitle'>404 Not Found</p>");
                    }
                } else {
                    die("<p class='title introTitle'>404 Not Found</p>");
                }
            }
            ?>
        </div>
    </body>
</html>