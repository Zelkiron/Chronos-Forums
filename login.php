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
        </div>
        <br>
        <div class='container'>
            <span class='title introTitle'>Login</span>
            <form method='post'>
                <input type='text' class='acc' name='username' placeholder='Username' required autofocus><br>
                <input type='password' class='acc' name='password' placeholder='Password' required><br>
                <input type ='submit' class='button' name='submit' value='Login'><br>
                <a href='forgot.php'>Forgot your password?</a><br>
                <a href='forgot.php'>Forgot your username?</a><br>
            </form>
            <?php 
            require('connect.php');
            if(isset($_POST['submit'])) {
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                $query = $pdo->prepare('SELECT * FROM users WHERE username = :u');
                $query->bindParam('u', $username);
                $query->execute();
                foreach($query->fetchAll() as $row) {
                    $rowUser = $row['username'];
                    $row_id = $row['id'];
                    $row_pass = $row['password'];
                    $rank = $row['rank'];
                }
                switch (true) {
                    case (empty($username) || empty($password)): 
                        session_destroy();
                        die('One or more of the required fields were not properly filled out.');
                    case ($query->rowCount() == 0 || password_verify($password, $row_pass) == 0):
                        session_destroy();
                        die('Invalid username/password.');
                    case ($rank == -1):
                        session_destroy();
                        die('Your account has been banned.');
                    case ($rank == 0):
                        session_destroy();
                        die('Your account has not yet been activated. Check your email for the activation link.');
                    default:
                        $_SESSION['name'] = $rowUser;
                        $_SESSION['id'] = $row_id;
                        header('Location: index.php');
                }
                
            }
            if(isset($_SESSION['id'])) {
                die('<style> form { display: none; } </style><br>You are already logged in as <b>'.$_SESSION['name'].'</b>.');
            }
            ?>
        </div>
    </body>
</html>

<!-- $_SESSION['name'] = $rowUser;
$_SESSION['id'] = $row_id;
header('Location: index.php'); -->