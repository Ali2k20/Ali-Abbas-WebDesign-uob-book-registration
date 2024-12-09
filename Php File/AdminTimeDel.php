<?php 
require("conn.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tid'])) {
    $tid = $_POST['tid'];
  $sql = "DELETE FROM timeslot WHERE tid = ?";
  $del = $db->prepare($sql);
  $del ->bindParam(1,$tid);
  $del->execute();
  if($del){
    header("location:AdminTimeManage.php?msg=The Room With The TID $tid been Deleted Successfully.");
    exit();
  }
  else{
    header("location:AdminTimeManage.php?msg=Error happend so it didn't got deleted.");
  }






} 

















?>