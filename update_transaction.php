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

// var_dump($_POST);

// traverse all records to check for whether to update or delete
for($i = 0; $i < count($_POST['note']); $i++){
    if(isset($_POST['cdelete'][$i])){
        array_push($mids_to_delete, $_POST['mid'][$i]); // push mids with delete cbox checked
    } else {
        array_push($source_ids, $_POST['sid'][$i]);
        array_push($notes, $_POST['note'][$i]);
        array_push($mids_to_possibly_update, $_POST['mid'][$i]); // push mids that didn't have delete cbox checked
        // will be later used to compare their submitted notes with the database notes, determine if update should occur
    }
}

$mids_delete_length = count($mids_to_delete);
if($mids_delete_length > 0) {
    for($i = 0; $i < $mids_delete_length; $i++){
        $mid_to_delete = $mids_to_delete[$i];
        $deletion_sql = "delete from CPS3740_2022F.Money_coronapi where cid='$customer_id' and mid='$mid_to_delete'";
        $deletion_result = mysqli_query($con, $deletion_sql);

        if($deletion_result){
            echo "<br>The Code $mid_to_delete has been deleted.";
        } else {
            echo "Something is wrong with deletion SQL: " . mysqli_error($con);
        }
    }
    echo "<br>$mids_delete_length records have been deleted.";
} else {
    echo "<br>No records will be deleted.";
}

$mids_update_length = count($mids_to_possibly_update);
$num_records_updated = 0;

for($i = 0; $i < $mids_update_length; $i++){
    $mid_to_possibly_update = $mids_to_possibly_updatep[$i];
    $get_note_for_mid_sql = "select note from CPS3740_2022F.Money_coronapi where cid='$customer_id' and mid='$mid_to_possibly_update'";
    $get_note_for_mid_result = mysqli_query($con, $get_note_for_mid_sql);

    if($get_note_for_mid_result){
        $num_notes = mysqli_num_rows($get_note_for_mid_result);
        if($num_notes > 1) 
            echo "More than one note was returned for this query.";
        else if($num_notes == 0)
            echo "No notes were returned.";
        else {
            // get the note from the db for the corresponding mid
            $db_note = mysqli_fetch_array($get_note_for_mid_result);
            $db_note = $db_note['note'];

            // compare the note submitted in the form and the note from the db
            $update_note = (strcmp($db_note, $notes[$i]) == 0) ? false : true;

            if($update_note){
                $update_sql = "update CPS3740_2022F.Money_coronapi set note='$db_note' where cid='$customer_id' and mid='$mid_to_possibly_update'";
                $update_result = mysqli_query($con, $update_sql);

                if($update_result){
                    echo "<br>The Note for code $mid_to_possibly_update has been updated in the database.";
                    $num_records_updated++;
                } else {
                    echo "Something is wrong with update SQL: " . mysqli_error($con);
                }
            }
        }
    } else {
        echo "Something is wrong with searching for note SQL: " . mysqli_error($con);
    }

}
echo "<br>$num_records_updated records have been updated.";
// var_dump($_POST);

mysqli_close($con);
?>