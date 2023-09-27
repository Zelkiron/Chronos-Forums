<?php
session_start();
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
        <div class='container container--center'>
            <span class='container__main-title'>Login</span>
            <form method='post'>
                <input type='text' class='default-input' name='username' placeholder='Username' required autofocus><br>
                <input type='password' class='default-input' name='password' placeholder='Password' required><br>
                <input type ='submit' class='button' name='submit' value='Login'><br>
                <a href='forgot.php'>Forgot your password?</a><br>
                <a href='forgot.php'>Forgot your username?</a><br><br>
                If you don't have an account, please <a href='register.php' class='noticeable-link'>register</a> for one here. <br><br>
                Lost your previous activation code? <a href='reactivate.php' class='noticeable-link'>Get a new one.</a>
            </form>
            <?php 
            require('connect.php');
            if(isset($_POST['submit'])) {
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                $validate_user_query = $pdo->prepare('SELECT * FROM users WHERE username = :u');
                $validate_user_query->bindParam('u', $username);
                $validate_user_query->execute();

                $db_id; //declare outside, used later for updating user last login date

                foreach($validate_user_query->fetchAll() as $row) {
                    $db_username = $row['username'];
                    $db_id = $row['id'];
                    $db_password = $row['password'];
                    $db_rank = $row['rank'];
                    $db_is_online = $row['is_online'];
                }
                switch (true) {
                    case (empty($username) || empty($password)): 
                        session_destroy();
                        die('One or more of the required fields were not properly filled out.');
                    case ($validate_user_query->rowCount() == 0 || password_verify($password, $db_password) == 0):
                        session_destroy();
                        die('Invalid username/password.');
                    case ($db_rank == -1):
                        session_destroy();
                        die('Your account has been banned.');
                    case ($db_rank == 0):
                        session_destroy();
                        die('Your account has not yet been activated. Check your email for the activation link.');
                    default:
                        $upate_last_joined_query = $pdo->prepare('UPDATE users SET date_last_seen = now() AND is_online = 1 WHERE id = :i');
                        $update_last_joined_query->bindParam('i', $db_id);
                        $update_last_joined_query->execute();

                        $_SESSION['username'] = $db_username;
                        $_SESSION['id'] = $db_id;
                        $_SESSION['rank'] = $db_rank;
                        header('Location: index.php');
                }
                
            }
            if(isset($_SESSION['id'])) {
                die('<style> form { display: none; } </style><br>You are already logged in as <b>'.$_SESSION['username'].'</b>.');
            }
            ?>
        </div>
    </body>
</html>