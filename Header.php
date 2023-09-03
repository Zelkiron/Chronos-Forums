<?php
class Header {
    private $header = "
    <span class='header__title'>CHRONOS</span>
    <a id='header__links' href='index.php'>Home</a>
    <a id='header__links' href='new_posts.php'>Recent Posts</a>
    <a id='header__links' href='status_updates.php'>Recent Status Updates</a>
    <a id='header__links' href='members.php'>Member List</a>
    <a id='header__links' href='staff.php'>Staff List</a>
    <a id='header__links' href='about.php'>About Me</a>
    <a id='header__links' href='#' onclick='profile()'>Profile</a>";
    function checkIfAdmin() {
        require('connect.php');
        $rank_query = $pdo->prepare("SELECT `rank` FROM users WHERE username = :u");
        $rank_query->bindParam('u', $_SESSION['username']);
        $rank_query->execute();
        if($rank_query->rowCount() == 1) {
            foreach($rank_query->fetchAll() as $row) {
                $rank = $row['rank'];
                if($rank == 3) {
                    $this->header .= "<a id='header__links' href='apanel.php'>Admin Panel</a>";
                }
            }
        }
    }
    function getHeader() {
        return $this->header;
    }
}
?>