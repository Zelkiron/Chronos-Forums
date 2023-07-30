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
            if(!isset($_GET['email'], $_GET['code'])) {
                echo '<span class="title introTitle">404 Not Found</span>';
            } else {
                $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
                $code = htmlspecialchars($_GET['code']);
                $query = $pdo->prepare('SELECT * FROM users WHERE email = :e');
                $query->bindParam('e', $email);
                $query->execute();
                $rank = 0;
                $act_code = '';
                foreach ($query->fetchAll() as $row) {
                    $rank += $row['rank'];
                    $act_code .= $row['actCode'];
                }
                if($query->rowCount() == 1) {
                    switch (true) {
                        case ($rank > 0):
                            die('Your account is already activated!');
                        case ($rank == 0 && password_verify($code, $act_code)):
                            $query2 = $pdo->prepare('UPDATE users SET rank = "1" WHERE email = :e');
                            $query2->bindParam('e', $email);
                            $query2->execute();
                            echo 'Congratulations, your account has been successfully activated! Would you like to 
                            <a href="login.php">login</a> now?';
                    }
                }
            }
        ?>
        </div>
    </body>
</html>