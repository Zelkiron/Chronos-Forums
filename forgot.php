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
            <a id='header__links' href='new_posts.php'>Recent Topics</a>
            <a id='header__links' href='status_updates.php'>Recent Status Updates</a>
            <a id='header__links' href='members.php'>Member List</a>
            <a id='header__links' href='staff.php'>Staff List</a>
            <a id='header__links' href='about.php'>About Me</a>
            <a id='header__links' href='#' onclick='profile()'>Profile</a>
        </div>
        <br>
        <div class='container content'>
            <span class='container__main-header'>Forgot Your...</span>
            <form method='post'>
                <input type='email' class='default-input' name='email' placeholder='john@example.com' required autofocus><br>
                <input type='radio' name='ifUser' id='alsoUser' value='alsoUser'><label for='alsoUser'>Password & Username</label><br>
                <input type='radio' name='ifUser' id='onlyUser' value='onlyUser'><label for='onlyUser'>Only Username</label><br>
                <input type='submit' class='button' name='submit' value='Submit'>
            </form>
            <?php
            require('connect.php');
            if(isset($_SESSION['id'])) {
                header('Location: changePass.php');
            }
            if(isset($_POST['submit'])) {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $query = $pdo->prepare('SELECT * FROM users WHERE email = :e'); 
                $query->bindParam('e', $email);
                $query->execute();
                $headers = "From: Chronos Team chronosforums@gmail.com\r\n";
                $headers .= "Reply-To: chronosforums@gmail.com\r\n";
                $headers .= "Return-Path: chronosforums@gmail.com\r\n";
                $headers .= "Organization: Chronos Forums";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";
                $headers .= "X-Priority: 3\r\n";
                $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
                switch (true) {
                    case ($ifUser == 'onlyUser'):
                        $username = '';
                        foreach($query->fetchAll() as $row) {
                            $username .= $row['username'];
                        }
                        $emailString = 
                        "Your username is ".$username;
                        mail($email, 'Username Retrieval', $emailString, $headers);
                        die('Check your email for your username.');
                    case ($ifUser == 'alsoUser' || empty($ifUser)):
                        $code = bin2hex(random_bytes(16));
                        $q2 = $pdo->prepare('UPDATE users SET tempCode = :p WHERE email = :e');
                        $q2->bindValue('p', password_hash($code, PASSWORD_DEFAULT));
                        $q2->bindValue('e', $email);
                        $q2->execute();
                        $emailString = 
                        "<html>
                        <body>
                        <a href='http://localhost/PHP%20Projects/Forum/changePass.php?email=".$email."&code=".$code."'>Click here</a>
                        to reset your password.<br>
                        </body>
                        </html>";
                        if($ifUser == 'alsoUser') {
                            $username = '';
                            foreach($query->fetchAll() as $row) {
                                $username .= $row['username'];
                            }
                            $emailString = 
                            "<html>
                            <body>
                            Your username is <b>".$username."</b>.
                            <a href='http://localhost/PHP%20Projects/Forum/changePass.php?email=".$email."&code=".$code."'>Click here</a>
                            to reset your password.<br>
                            </body>
                            </html>";
                            mail($email, "Password Reset/Username Retrieval", $emailString, $headers);
                            die('Follow the instructions that were just sent to your email.');
                        } else {
                            mail($email, "Password Reset", $emailString, $headers);
                            die('Follow the instructions that were just sent to your email.');
                        }
                    case ($email != 'chronosforums@gmail.com'):
                        die('You probably don\'t want to send this email to anyone else.');
                    case (empty($email)):
                        die('Please input an email.');
                }
            }
            ?>
        </div>
    </body>
</html>