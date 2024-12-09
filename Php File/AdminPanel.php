<?php session_start();
require("conn.php");
require("TimerDel.php");
// redirect non user to sigin
if (!isset($_SESSION['userid'])) {
    header("Location: signin.php");
    exit();
}

// Redirect unauthorized users to the homepage.
if(isset($_SESSION['Type'])){
if ($_SESSION['Type'] == "student" | $_SESSION['Type'] == "staff") {
    header("Location: HomePage.php");
    exit();
}

}
$queryRooms = $db->prepare("SELECT * FROM rooms");
$queryRooms->execute();
$rooms = $queryRooms->fetchAll();

$id = $_SESSION["userid"];
$query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
$query1->bindParam(1, $id);
$query1->execute();
$s = $query1->fetch();

$user = $db->prepare("SELECT * FROM user ");
$user->execute();
$usercount = $user->fetchAll();
$bsql = "SELECT 
    bookings.booking_id AS bid, 
    user.FullName AS name, 
    rooms.room_name AS rname, 
    timeslot.start_duration AS dstart, 
    timeslot.end_duration AS dend
FROM 
    bookings 
JOIN 
    user ON bookings.user_id = user.id 
JOIN 
    rooms ON bookings.room_id = rooms.room_id 
JOIN 
    timeslot ON bookings.tid = timeslot.tid

";
$queryBookings = $db->prepare($bsql);
                $queryBookings->execute();
                $bookings = $queryBookings->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/AdminNav.css">
    <link rel="stylesheet" href="css/AdminPanel.css">
    <title>Document</title>
</head>
<>
<nav>
<div class="admin-header">
    <a href="AdminPanel.php"><img src="images/logo.png" alt="Admin Logo" class="admin-logo"></a>
</div>

<div class="admin-menu">
    <ul>
        <li><a href="AdminRoomManage.php" class="admin-booking">Room Managment</a></li>
        <li><a href="AdminTimeManage.php" class="admin-booking">Room TimeSlot Managment</a></li>
        <li>
            <div class="admin-dropdown">
                <button class="admin-dropbtn">
                    <img class="admin-profile-picture" src="<?php echo $s['pfp']; ?>" alt="Admin Profile Picture">
                </button>
                <div class="admin-dropdown-content">
                    
                    <a href="AdminPanel.php">Dashboard</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </li>
    </ul>
</div>
</nav>

<div class="container">
<div class="welcome-header">
    <h1>Admin Dashboard</h1>
</div>

<div class="dashboard-overview">
    <div class="stat-box">
        <h3>Total Users</h3>
        <p><?php echo count($usercount)  ?></p>
    </div>
    <div class="stat-box">
        <h3>Total Rooms</h3>
        <p><?php echo count($rooms); ?></p>
    </div>
    <div class="stat-box">
        <h3>Total Booking</h3>
        <p><?php echo count($bookings); ?></p>
    </div>
</div>




<!-- Rooms and Booking Summary Section this for the heading u know what i mean -->
<div class="summary-header">
    <h2>Rooms and Booking Summary</h2>
</div>
<div class="summary-content">
    <!-- Rooms Table -->
    <div class="summary-box">
        <h3>Rooms</h3>
        <?php if(count($rooms)>0){ ?>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Room ID</th>
                    <th>Room Name</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Description</th>
                    <th>Room View</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch rooms from the database which i called it itcollege 
                
                $queryRooms = $db->prepare("SELECT * FROM rooms");
                $queryRooms->execute();
                $rooms = $queryRooms->fetchAll();

                foreach ($rooms as $room) {
                    echo "<tr>";
                    echo "<td>" . $room['room_id'] . "</td>";
                    echo "<td>" . $room['room_name'] . "</td>";
                    echo "<td>" . $room['capacity'] . "</td>";
                    echo "<td>" . $room['equipment'] . "</td>";
                    echo "<td>" . $room['des'] . "</td>";
                    echo "<td><img src='" . $room['roompic'] . "' /></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <?php } else{  ?>
    <div class="no-records-message">
        <p >There are no available rooms to book at the moment.</p>
    </div>
<?php } ?>
    </div>

    <!-- Bookings Table -->
    <div class="summary-box">
        <h3>Bookings</h3>
        <?php if(count($bookings) > 0){ ?>
        <table class="summary-table">
            
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Room Name</th>
                    <th>Booked By</th>
                    <th>start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch bookings from the database
                

                foreach ($bookings as $booking) {
                    echo "<tr>";
                    echo "<td>" . $booking['bid'] . "</td>";
                    echo "<td>" . $booking['rname'] . "</td>";
                    echo "<td>" . $booking['name'] . "</td>";
                    echo "<td>" . $booking['dstart'] . "</td>";
                    echo "<td>" . $booking['dend'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <?php } else { ?>
    <div class="no-records-message">
        <p >No Booked Rooms to display.</p>
    </div>
<?php } ?>
    </div>
</div>
</div>
<footer>
        <p>&copy; Made By Mathiam And Sayed jaafar</p>
    </footer>
</body>
</html>


