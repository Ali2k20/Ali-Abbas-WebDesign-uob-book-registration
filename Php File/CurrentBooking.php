<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Booking</title>
    <link rel="stylesheet" href="css/bookC.css">
</head>
<body>
    <?php include("booknav.php"); ?>

    <div class="header">
        <h1>Your Current Booking</h1>
    </div>

    <div class="container">
        <div class="orders-list">
            <?php
            try {
                $result = $db->prepare("
                     SELECT 
        b.booking_id AS BookingID,
        b.status AS Status,
        b.Date AS BookingDate,
        r.room_name AS RoomName,
        t.start_duration AS StartDate,
        t.end_duration AS EndDate,
        u.FullName AS FullName  -- Select the user's FullName
    FROM 
        bookings b
    JOIN 
        rooms r ON b.room_id = r.room_id
    JOIN 
        timeslot t ON b.tid = t.tid
    JOIN 
        user u ON b.user_id = u.id  -- Join the user table using the user_id from bookings
    WHERE 
        b.user_id = ?
    ORDER BY 
        b.Date DESC
");
                $result->bindParam(1, $id);
                $result->execute();
            } catch (PDOException $ex) {
                echo "Error occurred!";
                die($ex->getMessage());
            }
            ?>

            <?php
            if ($result->rowCount() > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Booking ID</th>
                        <th>Booked By</th>
                        <th>Booking Date</th>
                        <th>Room Name</th>
                        <th>Start Duration</th>
                        <th>End Duration</th>
                        <th>Status</th>
                      </tr>";
                while ($row = $result->fetch()) {
                    echo "<tr>";
                    echo "<td>" . $row["BookingID"] . "</td>";
                    echo "<td>" . $row["FullName"] . "</td>";
                    echo "<td>" . $row["BookingDate"] . "</td>";
                    echo "<td>" . $row["RoomName"] . "</td>";
                    echo "<td>" . $row["StartDate"] . "</td>";
                    echo "<td>" . $row["EndDate"] . "</td>";
                    ?> <td>
                    <?php echo $row["Status"]; ?> 
                    <form action="BookDel.php" method="POST" style="display:inline;">
                        <input type="hidden" name="bid" value="<?php echo $row['BookingID']; ?>">
                        <input type="hidden" name="name" value="<?php echo $row['RoomName'];?>">
                        <input type="submit" value="Cancel" class="cancel-button">
                    </form>
                </td>
                <?php
                   
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No current bookings found.</p>";
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; Made Maithem and Sayed jaafar</p>
    </footer>
    <script>
        window.onload = function() {
            <?php
            if (isset($_SESSION['msg'])) {
                echo 'alert("' . $_SESSION['msg'] . '");';
                unset($_SESSION['msg']);
                echo 'window.location.href = "currentBooking.php";';
            }
            ?>
        }
    </script>
</body>
</html>
