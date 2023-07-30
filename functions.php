<?php 
//get the rank of the user using the value stored in $row['rank']
function getRank($r) {

    //initalize variable that will be returned when this function is executed
    $return = "";

    //switch case for different possible values (1-4)
    //in the cases, append different possible values of return

    // * appending is not necessary, but it's a good habit to have when using foreach loops *
    switch ($r) {
        case 1:
            $return .= "<div class='rank' id='normal'>Member</div>";
            break;
        case 2: 
            $return .= "<div class='rank' id='mod'>Moderator</div>";
            break;
        case 3:
            $return .= "<div class='rank' id='admin'>Admin</div>";
            break;
        case 4:
            $return .= "<div class='rank' id='owner'>Owner</div>";
            break;
        default: //adding default case just in case something goes wrong
            $return .= "Invalid rank.";
    }

    //return the value 
    return $return;
}
?>