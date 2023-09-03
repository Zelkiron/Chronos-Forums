<?php 
require('connect.php'); 
require('functions.php');
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
        </div>

        <br>

        <div class='container container--center'>
            <!--<span class='container__main-header'>Register</span>-->
            <h1 class='container__main-header'>Register</h1>

            <form method='post'>
                <input type='email' class='default-input' name='email' placeholder='john@example.com' required autofocus><br>
                <input type='text' class='default-input' name='username' placeholder='Username' required><br>
                <input type='password' class='default-input' name='password' placeholder='Password' required><br>
                <input type ='submit' class='button' name='submit' value='Create Account'><br>
            </form>

            <?php
            try {
            if(isset($_POST['submit'])){
                //grabbing the details from the form
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); //here to prevent sql injections and make sure the email is valid
                $username = str_replace(' ', '', trim($_POST['username'])); //removes whitespaces at the beginning and end
                $password = $_POST['password'];

                $existing_account_query = $pdo->prepare('SELECT * FROM users WHERE username = :u OR email = :e'); //checking if the username or email are in use, do not use the "and" keyword here
                $existing_account_query->bindParam('u', $username);
                $existing_account_query->bindParam('e', $email);
                $existing_account_query->execute();

                //switch statement to make sure that the user does not get any of these cases
                //hence the switch(true), if the user gets any of them true, the code will go straight to that case
                //if not, it will go to the default case, which means that the user did everything right in registration
                //all the unsuccessful cases MUST be above the successful one (default)
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

                    case ($existing_account_query->rowCount() == 1):
                        die('This username/email is already in use.');

                    default: 
                        //the user will be emailed the unhashed activation code
                        //when they click on the link in the email, if the unhashed activation code matches with the hashed activation code retrieved from the database, the user will be activated
                        $unhashed_activation_code = bin2hex(random_bytes(16));
                        $hashed_activation_code = password_hash($hashed_activation_code, PASSWORD_DEFAULT); //using password_hash here for password_verify in activate.php, adds a layer of security
                        
                        $register_user_query = $pdo->prepare('INSERT INTO users (email, username, `password`, `rank`, date_created, activation_code) VALUES (:email, :user, :pass, :r, now(), :code)');
                        $password = password_hash($password, PASSWORD_DEFAULT);

                        $register_user_query->bindParam('email', $email);
                        $register_user_query->bindParam('user', $username);
                        $register_user_query->bindParam('pass', $password);
                        $register_user_query->bindValue('r', 0);
                        $register_user_query->bindValue('code', $hashed_activation_code);

                        //setting up the email to send to the user
                        //the phpmailer setup is in functions.php which is why i required it in this file
                        
                        $subject = "Account Activation";
                        $body = 
                        "<html>
                        <body>
                        Thank you for registering an account with Chronos Forums!<br>
                        <a href='http://localhost:3000/activate.php?email=".$email."&code=".$unhashed_activation_code."'>Click here</a>
                        to activate your account.<br>
                        On our end, we will only use your email for activating your account and resetting your password.<br>
                        We hope you will have a great time here!<br><br>
                        </body>
                        </html>";
                        emailUser($email, $subject, $body);

                        $register_user_query->execute();
                        echo 'Congratulations on creating your account, '.$username.'!<br>
                        To complete the last step of the registration process, check your email.<br>
                        Just in case, check your spam folder and unmark the email as spam to be able to click on the link.<br>
                        If you did not get an email for your activation code, you can try again <a href="reactivate.php" class="noticeable">here.</a>';
                }
            }  
        } catch (Exception $e) {
            die($e); //catch any exception that occurs while modifying this file
        } 
            ?>
        </div>
    </body>
</html>