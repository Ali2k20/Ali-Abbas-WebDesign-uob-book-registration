<?php
session_start();
try {
    require('conn.php');
    $id = $_SESSION["userid"];
$query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
$query1->bindParam(1, $id);
$query1->execute();
$s = $query1->fetch();

$query = $db->prepare("SELECT * FROM rooms ");
$query->execute();

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

    // Store the previous search term
    $_SESSION['presearch'] = $_GET['query'] ;

    // Fetch user details
    $id = $_SESSION["userid"];
$query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
$query1->bindParam(1, $id);
$query1->execute();
$s = $query1->fetch();

$query = $db->prepare("SELECT * FROM rooms ");
$query->execute();

    // Prepare the search query
    $searchQuery = '';
    if (isset($_GET['query'])) {
        $searchQuery = '%' . $_GET['query'] . '%';
        $sql = "SELECT * FROM rooms WHERE room_name LIKE :searchQuery OR equipment LIKE :searchQuery";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':searchQuery', $searchQuery);
        $stmt->execute();
        $rooms = $stmt->fetchAll();
    } else {
        $sql = "SELECT * FROM rooms";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rooms = $stmt->fetchAll();
    }

    $db = null;
} catch (PDOException $e) {
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/homepage.css">
    <title>Room Browsing</title>
</head>

    <!-- Navigation bar -->
    <?php include("homenav.php")    ?>

    <!-- Content -->
    <div class="container">
        <!-- Room Grid Header -->
        <div class="room-header-container">
            <h1>Searching for <?php echo $_GET['query'] ?> Room</h1>
            <hr />
        </div>

        <div class="contains-rooms">
            <div class="room-grid">
                <?php 
                if (count($rooms) > 0) {
                    foreach($rooms as $r) {
                        echo "<div class='broom'>";
                        echo "<div class='images'>";
                        echo "<a href='roomDetail.php?room_id={$r['room_id']}' style='text-decoration: none'><img src='" . $r['roompic'] . "' alt='image'></a>";
                        echo "</div>";
                        echo "<a href='roomDetail.php?room_id={$r['room_id']}' style='text-decoration: none'><b>Room: {$r['room_name']}</b></a>";
                        echo "<b><p>description: " . $r['des'] . "</p></b>";
                        echo "</div>";
                    }
                } else {
                    echo "<h1>No Room Called for '{$_GET['query']}'</h1>";
                }
                ?>
            </div>
        </div>
    </div>
  
   

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 UOB Booking Room. All Rights Reserved.</p>
    </footer>
</body>
</html>
