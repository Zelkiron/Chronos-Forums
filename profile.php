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
            require("connect.php");
            require("functions.php");

            if(isset($_GET['id'])) {
                $profile_id = htmlspecialchars($_GET['id']);

                $userQuery = $pdo->prepare("SELECT * FROM users WHERE id = :i");
                $userQuery->bindParam('u', $profile_id);
                $userQuery->execute();

                if ($profile_id == $_SESSION['id']) {
                    $
                }
                switch (true) {
                    case ($userQuery->rowCount() == 0):
                        header("Location: 404.html");
                }

            } else {
                header("Location: 404.html");
            } 
            ?>
        </div>
    </body>
</html>