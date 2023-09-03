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
        </div>
        <br>
        <div class='container container--center'>
            <span class='container__main-header'>Login</span>
            <form method='post'>
                <input type='text' class='default-input' name='username' placeholder='Username' required autofocus><br>
                <input type='password' class='default-input' name='password' placeholder='Password' required><br>
                <input type ='submit' class='button' name='submit' value='Login'><br>
                <a href='forgot.php'>Forgot your password?</a><br>
                <a href='forgot.php'>Forgot your username?</a><br>
            </form>
            <?php 
            require('connect.php');
            try {
            if(isset($_POST['submit'])) {
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                $user_query = $pdo->prepare('SELECT * FROM users WHERE username = :u');
                $user_query->bindParam('u', $username);
                $user_query->execute();
                foreach($user_query->fetchAll() as $row) {
                    $db_username = $row['username'];
                    $db_id = $row['id'];
                    $db_password = $row['password'];
                    $db_rank = $row['rank'];
                }
                switch (true) {
                    case (empty($username) || empty($password)): 
                        session_destroy();
                        die('One or more of the required fields were not properly filled out.');
                    case ($user_query->rowCount() == 0 || password_verify($password, $db_password) == 0):
                        session_destroy();
                        die('Invalid username/password.');
                    case ($db_rank == -1):
                        session_destroy();
                        die('Your account has been banned.');
                    case ($db_rank == 0):
                        session_destroy();
                        die('Your account has not yet been activated. Check your email for the activation link.');
                    default:
                        $_SESSION['username'] = $db_username;
                        $_SESSION['id'] = $db_id;
                        $_SESSION['rank'] = $db_rank;
                        header('Location: index.php');
                }
                
            }
            if(isset($_SESSION['id'])) {
                die('<style> form { display: none; } </style><br>You are already logged in as <b>'.$_SESSION['username'].'</b>.');
            }
        } catch (Exception $e) {
            die($e);
        }
            ?>
        </div>
    </body>
</html>

<!-- $_SESSION['name'] = $db_username;
$_SESSION['id'] = $db_id;
header('Location: index.php'); -->