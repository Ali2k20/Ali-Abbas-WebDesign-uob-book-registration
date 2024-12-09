<?php 
require("conn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room-id'])) {
    $rid = $_POST['room-id'];
    $rname = $_POST['room-name'];
    $cap = $_POST['capacity'];
    $eqi = $_POST['eqi'];
    $des = $_POST['description'];

    // Get current room details
    $sql = "SELECT * FROM rooms WHERE room_id = ?";
    $editroom = $db->prepare($sql);
    $editroom->bindParam(1, $rid);
    $editroom->execute();
    $r = $editroom->fetch();

    if ($r) {
        // Use current room values if inputs are empty
        if (empty($_POST['room-name'])) {
            $rname = $r['room_name'];
        }
        if (empty($_POST['capacity'])) {
            $cap = $r['capacity'];
        }
        if (empty($_POST['eqi'])) {
            $eqi = $r['equipment'];
        }
        if (empty($_POST['description'])) {
            $des = $r['des'];
        }

        // Handle file upload if a new image is selected
        if (isset($_FILES['room-pic']) && $_FILES['room-pic']['error'] == 0) {
            $fileTmpPath = $_FILES['room-pic']['tmp_name'];
            $fileName = $_FILES['room-pic']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = $rid . '.' . $fileExtension; // Generate a new filename based on room ID
            $destPath = "images/" . $newFileName;

            // Check if the file is a valid image
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $destPath)) {
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $roompic = $destPath; // Use the new file path
                }
            } else {
                $msg1 ="only the Picture is in the wrong form it should be jpg or jpeg or png or gif or webp only accepted please try again or your profile wont be updated";
                $roompic = $r['roompic'];
            }
                
            
        } else {
            // Use the existing image if no new image is uploaded
            $roompic = $r['roompic'];
        }

        // Update the room details
        $sql1 = "UPDATE rooms 
                 SET room_name = ?, capacity = ?, equipment = ?, des = ?, roompic = ? 
                 WHERE room_id = ?";

        $updateRoom = $db->prepare($sql1);
        $updateRoom->bindParam(1, $rname);
        $updateRoom->bindParam(2, $cap);
        $updateRoom->bindParam(3, $eqi);
        $updateRoom->bindParam(4, $des);
        $updateRoom->bindParam(5, $roompic);
        $updateRoom->bindParam(6, $rid);
        $updateRoom->execute();
        if($updateRoom){
            header("location:AdminRoomManage.php?msg=Room Named {$r['room_name']} Been Updated Successfully . $msg1    ");
            exit();
        }
    } else {
        echo "Room not found. $rid";
    }
} else {
    echo "Invalid request.";
}
?>
