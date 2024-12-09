<?php 
require("conn.php");
if($_SERVER['REQUEST_METHOD']=="POST" && isset( $_POST["tid"]) && isset($_POST['rid']) ){
   $tid = $_POST['tid'];
   $dstart = $_POST['start-time']; //inshallah 100% done
   $dend = $_POST['end-time'];

   $sql = "SELECT t.tid, t.start_duration, t.end_duration FROM timeslot t WHERE t.room_id = ?
   AND ((t.start_duration < ? AND t.end_duration > ?) )";
   
   $tConflict= $db->prepare($sql);
   $tConflict ->bindParam(1,$rid);
   $tConflict -> bindParam(2,$dstart);
   $tConflict->bindParam(3,$dend);
   $tConflict -> execute();

   if($tConflict->rowCount()> 0){
    header("location:AdminTimeManage.php?msg=Error: The room with ID $rid is already booked for the selected time slot. Please choose a different time.");
    exit();
}
else{
    $updateSql = "UPDATE timeslot SET start_duration = ?, end_duration = ? WHERE tid = ?";
    $tUpdate = $db ->prepare($updateSql);
    $tUpdate ->bindParam(1,$dstart);
    $tUpdate ->bindParam(2,$dend);
    $tUpdate ->bindParam(3,$tid);
    $tUpdate ->execute();
    if($tUpdate){
        header("location:AdminTimeManage.php?msg=Success: The timeslot has been successfully updated to start at $dstart and end at $dend.");
        exit();
    }

}

}




?>