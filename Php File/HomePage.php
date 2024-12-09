<?php
session_start();
require("conn.php");
require("TimerDel.php");
// Redirect Admins to the Admin page
if ($_SESSION['Type'] === "Admin") {
    header("Location: Admin.php");
    exit();
}

// Redirect unauthorized users to the Sign-In page
if ($_SESSION['Type'] !== "student" && $_SESSION['Type'] !== "staff") {
    header("Location: signin.php");
    exit();
}

// Fetch user details
$id = $_SESSION["userid"];
$query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
$query1->bindParam(1, $id);
$query1->execute();
$s = $query1->fetch();

$query = $db->prepare("SELECT * FROM rooms ");
$query->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/homepage.css">
    <title>Home Page</title>
    <style>
        
    </style>
</head>
<body>
    <nav>
        <div class="image-container">
           <a href="HomePage.php"> <img src="images/logo.png" alt="Logo"></a>
        </div>
        <div class="SearchBar">
            <form action="roombrowsing.php" method="GET">
                <input type="search" name="query" placeholder="Search...">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="Menu">
            <ul>
                <li><a href="currentbooking.php" class="Book">Your Current Booking</a></li>
                <li>
                    <div class="dropdown">
                        <button class="dropbtn">
                            <img class="profile-picture-update" src="<?php echo $s['pfp']; ?>" alt="Profile Picture class='logo-img'">
                        </button>
                        <div class="dropdown-content">
                            <a href="HomePage.php">Home</a>
                            <a href="ProfileView.php">View Profile</a>
                           
                            <a href="logout.php">Log Out</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="Rooms">
        <div class="Room-container">
            <h1>Welcome to UOB Booking Room Site, 
                <?php 
                echo $_SESSION['Type'] === "student" ? "Student " : "Instructor ";
                echo $s['FullName'] ; 
                ?>
            </h1>
        </div>
    </div>

    <div class="room-grid-wrapper">
        <!-- Header Container -->
        <div class="room-header-container">
            <h1>Rooms</h1>
            <hr />
        </div>
        <div class="contains-rooms">
            <div class="room-grid">
                <?php 
                    while ($row = $query->fetch()) {
                        echo "<div class='broom'>";
                        echo "<div class='images'>";
                        echo "<a href='roomDetail.php?room_id={$row['room_id']}' style='text-decoration: none'><img src='" . $row['roompic'] . "' alt='image'></a>";
                        echo "</div>";
                        echo "<a href='roomDetail.php?room_id={$row['room_id']}' style='text-decoration: none'><b>Room: {$row['room_name']}</b></a>";
                        echo "<b><p>description: " . $row['des'] . "</p></b>";
                        echo "</div>";
                    }
                ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; Made By Mathiam And Sayed jaafar</p>
    </footer>
</body>
</html>
