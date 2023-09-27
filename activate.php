<?php
require('connect.php');
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
            <span class='container__main-title'>Activate Your Account</span>
            <br>
            <?php
            //check if there are values set for the email and code variables in the url, if not, then redirect them to 404 not found page
            if(isset($_GET['email'], $_GET['code'])) {
                $email_from_url = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL); //filter email from url to prevent sql injections
                $code_from_url = $_GET['code'];

                //get the activation code of the user stored in the db
                //no need to get the rank as well because the activation code in db and activation code in the get request will not be equal to each other anyways
                $get_activation_code_query = $pdo->prepare('SELECT activation_code FROM users WHERE email = :e');
                $get_activation_code_query->bindParam('e', $email_from_url);
                $get_activation_code_query->execute();

                //get the activation code from the database
                foreach ($get_activation_code_query->fetchAll() as $row) {
                    $db_activation_code = $row['activation_code'];
                }
                if($get_activation_code_query->rowCount() == 1) {
                    switch (true) {
                        case (!(password_verify($code_from_url, $db_activation_code))):
                            //header('Location: 404.php');
                            die("Invalid activation code. <br> Lost your previous activation code? <a href='reactivate.php' class='noticeable-link'>Get a new one.</a>");

                        default: //activates the account by setting the rank to 1 (normal) and getting rid of the activation code
                            $activate_account_query = $pdo->prepare('UPDATE users SET `rank` = :r, `activation_code` = :a WHERE `email` = :e');
                            $activate_account_query->bindValue('r', 1);
                            $activate_account_query->bindValue('a', '');
                            $activate_account_query->bindParam('e', $email_from_url);
                            $activate_account_query->execute();
                            echo 'Congratulations, your account has been successfully activated!<br>
                            <a href="login.php" class="noticeable-link">Would you like to login</a> now?';
                    }
                } else {
                    //if the email given is not associated with an existing account, redirect to the 404 page
                    header('Location: 404.php');
                }
            } else {
                //if there is no email or no code set, redirect them to the 404 page
                //need to have both email and code
                header('Location: 404.php');
            }
        ?>
        </div>
    </body>
</html>