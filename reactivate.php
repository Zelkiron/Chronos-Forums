<?php
require('Header.php');
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
            <?php 
            $header = new Header();
            echo $header->getHeader();
            ?>
        </div>
            <br>
        <div class='container container--center'>
            <span class='container__main-title'>Reactivate Account</span>
            <form method='post'>
                <input type='email' class='default-input' name='email' placeholder='Email' />
                <input type='submit' class='button' name='submit' value='New Activation Code' />
            </form>
            <?php 
            if(isset($_POST['submit'])) {
                $form_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $get_rank_query = $pdo->prepare('SELECT `rank` FROM users WHERE email = :email');
                $get_rank_query->bindParam('email', $form_email);
                $get_rank_query->execute();
                foreach($get_rank_query->fetchAll() as $row) {
                    if ($row['rank'] > 0) {
                        die('Your email is already activated!');
                    }
                }
                if($get_rank_query->rowCount() == 0) {
                    die('Email not found. Please try again with a valid email.');
                }
                $update_activation_code_query = $pdo->prepare('UPDATE users SET activation_code = :activation_code WHERE email = :email AND `rank` = 0');

                $unhashed_activation_code = bin2hex(random_bytes(16));
                $hashed_activation_code = password_hash($unhashed_activation_code, PASSWORD_DEFAULT);

                $update_activation_code_query->bindParam('activation_code', $hashed_activation_code);
                $update_activation_code_query->bindParam('email', $form_email);

                $subject = "New Activation Code - Account Activation";
                $body = 
                "<html>
                <body>
                Thank you for registering an account with Chronos Forums.<br>
                <a href='http://localhost:3000/activate.php?email=".$form_email."&code=".$unhashed_activation_code."'>Here is your new activation code.</a><br>
                On our end, we will only use your email for activating your account and resetting your password.<br>
                We hope you will have a great time here!<br><br>
                </body>
                </html>";

                if($update_activation_code_query->execute()) {
                    emailUser($form_email, $subject, $body);
                    echo('Your new activation code has been emailed successfully. <br> If it doesn\'t arrive in your inbox, take care to check your spam folder.');
                }
            }
            ?>
        </div>
    </body>
</html>