<?php
//connects to database
global $pdo;
$dsn = "mysql:host=localhost; dbname=forum";
$pdo = new PDO($dsn, "root", "password");
if(!($pdo)) {
    die('Database connection failed. Contact the owner if this keeps happening: '.$pdo->ErrorInfo);
}

//does a query to see if the user is a high enough rank to see the admin panel (3)
function checkIfAdmin($user_session_id) {
    require('connect.php');

    $rank_query = $pdo->prepare("SELECT `rank` FROM users WHERE username = :u");
    $rank_query->bindParam('u', $user_session_id);
    $rank_query->execute();

    if($rank_query->rowCount() == 1) {
        foreach($rank_query->fetchAll() as $row) {
            $rank = $row['rank'];

            if($rank >= 3) {
                return true;
            } else {
                return false;
            }
        }
    }
}

use PHPMailer\PHPMailer\PHPMailer;
//get the rank of the user using the value stored in $row['rank']
function convertRankToTitle($r) {

    //initalize variable that will be returned when this function is executed
    $titleFromRankNumber = "";

    //switch case for different possible values (1-4)
    //in the cases, append different possible values of return

    // * appending is not necessary, but it's a good habit to have when using foreach loops *
    switch ($r) {
        case -1:
            $titleFromRankNumber .= "<div class='rank' id='banned'>Banned</div>";
            break;
        case 0: //account that not been activated from email yet
            $titleFromRankNumber .= "<div class='rank'>Not Activated</div>";
            break;
        case 1: //normal
            $titleFromRankNumber .= "<div class='rank' id='normal'>Member</div>";
            break;
        case 2: //moderator
            $titleFromRankNumber .= "<div class='rank' id='mod'>Moderator</div>";
            break;
        case 3: //admin
            $titleFromRankNumber .= "<div class='rank' id='admin'>Admin</div>";
            break;
        case 4: //owner
            $titleFromRankNumber .= "<div class='rank' id='owner'>Owner</div>";
            break;
        default: //adding default case just in case something goes wrong
            $titleFromRankNumber .= "<div class='rank'>Invalid Rank</div>";
    }

    //return the value 
    return $titleFromRankNumber;
}

//function that sets up phpmailer to send the user an email
function emailUser($user_email, $subject, $body) {
    require('vendor/autoload.php'); //include require statements in the functions themselves

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'chronosforums@gmail.com';
    $mail->Password = 'egeereskrkhqlccy';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('chronosforums@gmail.com');
    $mail->addAddress($user_email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send() or die('Could not send mail. Contact the owner if this keeps happening: '.$mail->ErrorInfo);
}
?>