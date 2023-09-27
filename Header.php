<?php
require_once('ConnectToDatabase.php');
class Header {
    private $header;
    private $pdo;

    function __construct() {
        $this->header = "<span class='header__title'>CHRONOS</span>
        <a id='header__links' href='index.php'>Home</a>
        <a id='header__links' href='about.php'>About Me</a>
        <a id='header__links' href='members.php'>Member List</a>
        <a id='header__links' href='staff.php'>Staff List</a>
        <a id='header__links' href='new_posts.php'>Recent Posts</a>
        <a id='header__links' href='status_updates.php'>Recent Status Updates</a>";

        $db = new ConnectToDatabase();
        $this->pdo = $db->connect();
    }

    private function appendUserProfile() {
        if (isset($_SESSION['id'])) {
            $this->header .= "<a id='header__links' href='profile.php?id=".$_SESSION['id']."'>Profile</a>";
        }
    }

    private function checkIfAdmin($user_id) {
        $rank_query = $this->pdo->prepare("SELECT `rank` FROM users WHERE id = :user_id");
        $rank_query->bindParam('user_id', $user_id);
        $rank_query->execute();
        if($rank_query->rowCount() == 1) {
            foreach($rank_query->fetchAll() as $row) {
                $rank = $row['rank'];
                if($rank >= 3) {
                    $this->header .= "<a id='header__links' href='apanel.php'>Admin Panel</a>";
                }
            }
        }
    }
    private function displayRegisterIfGuest() {
        if (!isset($_SESSION['id'])) {
            $this->header .= "<a id='header__links' href='register.php'>Register</a>";
        }
    }
    function getHeader() {
        $this->appendUserProfile();
        $this->checkIfAdmin($_SESSION['id']);
        $this->displayRegisterIfGuest();
        return $this->header;
    }
}
?>