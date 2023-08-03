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
            <span class='title introTitle'>Register</span>
            <form method='post'>
                <input type='email' class='acc' name='email' placeholder='john@example.com' required autofocus><br>
                <input type='text' class='acc' name='username' placeholder='Username' required><br>
                <input type='password' class='acc' name='password' placeholder='Password' required><br>
                <input type ='submit' class='button' name='submit' value='Create Account'><br>
            </form>
            <?php
            require('connect.php');
            if(isset($_POST['submit'])){
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $username = str_replace(' ', '', trim($_POST['username']));
                $password = $_POST['password'];
                $q = $pdo->prepare('SELECT * FROM users WHERE username = :u OR email = :e');
                $q->bindParam('u', $username);
                $q->bindParam('e', $email);
                $q->execute();
                switch (true) {
                    case (empty($email) || empty($username) || empty($password)):
                        die('One or more of the required fields were not properly filled out.');
                    case (!filter_var($email, FILTER_VALIDATE_EMAIL)):
                        die('Email must be in a valid format.');
                    case (strlen($username) >= 21):
                        die('Username cannot be more than 20 characters.');
                    case (strlen($password) < 8):
                        die('Password must be at least 8 characters long.');
                    case (!ctype_alnum($username)):
                        die('Your username should only be letters and numbers.');
                    case ($q->rowCount() == 1):
                        die('This account already exists.');
                    default: 
                        $query = $pdo->prepare('INSERT INTO users (email, username, password, rank, date_created, act_code) VALUES (:email, :user, :pass, :r, now(), :code)');
                        $query->bindParam('email', $email);
                        $query->bindParam('user', $username);
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $query->bindParam('pass', $hash);
                        $query->bindValue('r', 0);
                        $query->bindValue('code', password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT));
                        $query->execute();
                        echo 'Congratulations on creating your account, '.$username.'! 
                        To complete the last step of the registration process, check your email.
                        Just in case, check your spam folder and unmark the email as spam to be able to click on the link.';
                        $email_string = 
                        "<html>
                        <body>
                        Thank you for registering an account with Chronos Forums!<br>
                        <a href='activate.php?email=".$email."&code=".$code."'>Click here</a>
                        to activate your account.<br>
                        On our end, we will only use your email for activating your account and resetting your password.<br>
                        We hope you will have a great time here!<br><br>
                        </body>
                        </html>";
                        $headers = "From: Chronos Team chronosforums@gmail.com\r\n";
                        $headers .= "Reply-To: chronosforums@gmail.com\r\n";
                        $headers .= "Return-Path: chronosforums@gmail.com\r\n";
                        $headers .= "Organization: Chronos Forums";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "X-Priority: 3\r\n";
                        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
                        mail($email, "Account Activation", $email_string, $headers);
                }
            }   
            ?>
        </div>
    </body>
</html>