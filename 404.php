<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Chronos Forums</title>
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&amp;display=swap">
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
            <a id='header__links' href='#' onclick='profile()'>Profile</a>
        </div>
            <br>
        <div class='container'>
            <span class="title container__main-title">404 Not Found</span>
            <br>
            The content here is not found.
            <br>
            You will be redirected back to the homepage in five seconds. 
            <script>window.setTimeout("location=('index.php');", "5000");</script>
        </div>
    </body>
</html>