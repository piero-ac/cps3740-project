<?php
// check if user id exists
if(!isset($_COOKIE['userid'])) { 
    echo "Please login first!"; 
    die;
}

include "dbconfig.php";
// connection to database
$con = mysqli_connect($host, $username, $password, $dbname) 
    or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_errno());

$customer_id = $_POST['cid'][0]; // all cids will be the same
$notes = array();
$source_ids = array();
$mids_to_delete = array(); // will store all the transactions ids to delete
$mids_to_possibly_update = array(); // will store all the transcation ids to check if note message has changed


// traverse all records to check for whether to update or delete
for($i = 0; $i < count($_POST['note']); $i++){
    if($_POST['cdelete'][$i] == 'Y'){
        array_push($mids_to_delete, $_POST['mid'][$i]); 
    } else {
        array_push($source_ids, $_POST['sid'][$i]);
        array_push($notes, $_POST['note'][$i]);
        array_push($mids_to_possibly_update, $_POST['mid'][$i]);
    }
}

$mids_delete_length = count($mids_delete_length);
$mids_update_length = count($mids_to_possibly_update);
echo "Will delete $mids_delete_length records from the table.";
echo "Will check $mids_update_length records for possible update.";

// var_dump($_POST);


?>