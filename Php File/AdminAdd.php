<?php
require("conn.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room-name'])) {
    $roomName = $_POST['room-name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equ'];
    $description = $_POST['des'];
    
    
    // Handle the file upload
    if (isset($_FILES['room-pic'])) {
       
        $targetFile = "images/" . basename($_FILES["room-pic"]["name"]);
        move_uploaded_file($_FILES["room-pic"]["tmp_name"], $targetFile);
        $roomPic = $targetFile;
    } 

    // Insert into rooms table
    $stmt = $db->prepare("INSERT INTO rooms VALUES (Null,?, ?, ?, ?,?)");
    $stmt->bindParam(1, $roomName);
    $stmt->bindParam(2, $capacity);
    $stmt->bindParam(3, $capacity);
    $stmt->bindParam(4, $description);
    $stmt->bindParam(5, $roomPic);
    $stmt->execute();

   

    if($stmt){
        header("location:example.php?msg=The Room $roomName Has Been SuccessFully Added");
    }
}
?>
