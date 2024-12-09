<?php
require("conn.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room-name'])) {
    $roomName = $_POST['room-name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['Equ'];
    $description = $_POST['des'];
    
    $sql = "SELECT * FROM rooms WHERE room_name = ?";
    $rfind = $db->prepare($sql);
    $rfind ->bindParam(1, $roomName, );
    $rfind -> execute();
    if( $rfind ->rowCount() > 0 ) {
         
        header("location: AdminRoomManage.php?msg=The Room Name $roomName Already Exists Please Try another name!");
        exit();
    } 
    else{

    if (isset($_FILES['room-pic']) && $_FILES['room-pic']['error'] == 0) {
        $targetFile = "images/" . basename($_FILES["room-pic"]["name"]);

        // Check if the file is a valid image
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $targetFile)) {
            if (move_uploaded_file($_FILES["room-pic"]["tmp_name"], $targetFile)) {
                $roompic = $targetFile; // Use the new file path
            }
        }  else {header("location:AdminRoomManage.php?msg=wrong form it should be jpg or jpeg or png or gif or webp only accepted please try again");
            exit();}
    } else{
        header("location:AdminRoomManage.php?msg=The Room $roomName Has Been SuccessFully Added");
        exit();
    }

  

    // Insert into rooms table
    $insertroom = $db->prepare("INSERT INTO rooms VALUES (Null,?, ?, ?, ?,?)");
    $insertroom->bindParam(1, $roomName);
    $insertroom->bindParam(2, $capacity);
    $insertroom->bindParam(3, $equipment);
    $insertroom->bindParam(4, $description);
    $insertroom->bindParam(5, $roompic);
    $insertroom->execute();

   

    if($insertroom){
        header("location:AdminRoomManage.php?msg=The Room $roomName Has Been SuccessFully Added");
        exit();
    }
}
}



?>
