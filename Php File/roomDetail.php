<?php 
session_start();
require("conn.php");
if (!isset($_SESSION['activeUser'])) {
    header("Location: signin.php");
    exit();
}
else if($_SESSION['Type']=='admin')
{
    header('location:AdminPanel.php');
}

$id = $_SESSION["userid"];
$query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
$query1->bindParam(1, $id);
$query1->execute();
$s = $query1->fetch();

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    try {
        $sql = "SELECT * FROM rooms WHERE room_id = ?";
        $stmt1 = $db->prepare($sql);
        $stmt1->bindParam(1, $room_id);
        $stmt1->execute();
        $details = $stmt1->fetch();

        $sql2 = "SELECT * FROM timeslot t WHERE t.room_id = ? AND NOT EXISTS ( SELECT 1 FROM bookings b WHERE b.tid = t.tid 
        AND b.status = 'Booked')";

        $stmt2 = $db->prepare($sql2);
        $stmt2->bindParam(1, $room_id);
        $stmt2->execute();
        $timeslots = $stmt2->fetchAll();

        $db = null;
    } catch(PDOException $e) {
        die($e->getMessage());
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/roomDetail.css">
    <title>Room Details</title>
   
</head>
<body>
<?php include("homenav.php"); 
if(isset($_SESSION['presearch'])){
    $prev = $_SESSION['presearch']; 
 } else $prev = "";?>


    <div class="container1">
      <div class="header-container">
          <?php  if ($details) {
                        echo "Welcome to the Details of the " . $details['room_name'] . " Room";

                    } else {
                        echo "<p>Room details not found.</p>";
                    }
                 ?>
 

       </div>
       


        <div class="room-details">
            <div class="room-image">
                <?php
               

                    if ($details) {
                        echo "<img src='" . $details['roompic'] . "' alt='Room Image'>";
                    } else {
                        echo "<p>Room details not found.</p>";
                        exit();
                    }
                
                ?>
            </div>
            <div class="room-info">
               

                <!-- Table for Room Details -->
                <table>
                    <tr>
                        <th>Capacity</th>
                        <td><?php echo $details['capacity']; ?></td>
                    </tr>
                    <tr>
                        <th>Equipment</th>
                        <td><?php echo $details['equipment']; ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?php echo $details['des']; ?></td>
                    </tr>
                </table>

                <?php
                if (count($timeslots) > 0) {
                    echo "<div class='form-container'>";
                    echo "<form method='POST' action='RoomBooked.php' >";
                    echo "<label for='timeslot'>Please select a time slot: </label>";
                    echo "<select name='timeslot'  class='custom-select'>";
                   ?> <!-- <option disabled selected>Please Choose One</option>-->   <?php
                    foreach ($timeslots as $timeslot) {
                        echo "<option value='" . $timeslot['tid'] . "'>" . $timeslot['start_duration'] . "  -  " . $timeslot['end_duration'] . "</option>";
                    }
                    echo "</select>";
                    echo "<input type='hidden' name='room_id' value='" . $details['room_id'] . "' />";
                    echo "<input type='submit' value='Book' class='btn-submit' />";
                    echo "</form>";
                    echo "</div>";
                } else {
                    echo "<div class='availability'><p>No Time Slots Available. Please come back later.</p></div>";
                }
                ?>
                <a href="roomBrowsing.php?query=<?php echo $prev?>" class="btn-back">Back to Searching Rooms</a>
                <?php 
             if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
                if (!empty($_SESSION['success'])) {
                    echo "<script>alert('" . ($_SESSION['success']) . "');</script>";
                    $_SESSION['success'] = ""; 
                } elseif (!empty($_SESSION['error'])) {
                    echo "<script>alert('" . ($_SESSION['error']) . "');</script>";
                    $_SESSION['error'] = ""; 
                }
            }?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; Made By Mathiam And Sayed jaafar</p>
    </footer>
</body>
</html> 
