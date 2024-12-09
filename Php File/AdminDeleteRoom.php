<?php 
require('conn.php');
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_id'])) {
     $roomid = $_POST['room_id'];
     $sql = "DELETE FROM rooms WHERE room_id = ? ";
     
     $del = $db->prepare($sql);
     $del ->bindParam(1,$roomid);
     $del->execute();

      if($del){
        header("location:AdminRoomManage.php?msg=Room with ID $roomid has been successfully deleted from the system.");
        exit();
      }

  } 





?>