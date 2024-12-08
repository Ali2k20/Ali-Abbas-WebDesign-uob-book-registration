<?php
session_start();
require('conn.php');

// Room Management - Add, Edit, Delete Room
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_room'])) {
        $room_name = $_POST['room_name'];
        $capacity = $_POST['capacity'];
        $equipment = $_POST['equipment'];
        $des = $_POST['description'];
        $roompic = $_POST['roompic'];

        $sql = "INSERT INTO rooms (room_name, capacity, equipment, des, roompic) 
                VALUES ('$room_name', '$capacity', '$equipment', '$des', '$roompic')";
        if ($db->query($sql) === TRUE) {
            echo "<script>alert('Room added successfully');</script>";
        } else {
           
        }
    }

    // Edit Room
    if (isset($_POST['edit_room'])) {
        $room_id = $_POST['room_id'];
        $room_name = $_POST['room_name'];
        $capacity = $_POST['capacity'];
        $equipment = $_POST['equipment'];
        $des = $_POST['description'];
        $roompic = $_POST['roompic'];

        $sql = "UPDATE rooms SET room_name='$room_name', capacity='$capacity', equipment='$equipment',
                des='$des', roompic='$roompic' WHERE room_id='$room_id'";
        if ($db->query($sql) === TRUE) {
            echo "<script>alert('Room updated successfully');</script>";
        } else {
           
        }
    }

    // Add Time Slot
    if (isset($_POST['add_timeslot'])) {
        $room_id = $_POST['room_id'];
        $start_duration = $_POST['start_duration'];
        $end_duration = $_POST['end_duration'];

        $sql = "INSERT INTO timeslot (room_id, start_duration, end_duration)
                VALUES ('$room_id', '$start_duration', '$end_duration')";
        if ($db->query($sql) === TRUE) {
            echo "<script>alert('Time slot added successfully');</script>";
        } else {
            
        }
    }
}

// Delete Room
if (isset($_GET['delete_id'])) {
    $room_id = $_GET['delete_id'];
    $sql = "DELETE FROM rooms WHERE room_id='$room_id'";
    if ($db->query($sql) === TRUE) {
        echo "<script>alert('Room deleted successfully');</script>";
    } else {
        
    }
}

// Fetch Rooms
$rooms_sql = "SELECT * FROM rooms";
$rooms_result = $db->query($rooms_sql);

// Fetch Time Slots
$timeslot_sql = "SELECT ts.*, r.room_name FROM timeslot ts 
                 JOIN rooms r ON ts.room_id = r.room_id";
$timeslot_result = $db->query($timeslot_sql);

// Fetch Bookings
$booking_sql = "SELECT b.booking_id, u.FullName, r.room_name, b.Date, b.status
                FROM bookings b
                JOIN user u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.room_id";
$booking_result = $db->query($booking_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Navigation -->
    <div class="top-nav">
        <a href="index.php">Dashboard</a>
        <a href="rooms.php">Rooms</a>
        <a href="timeslot.php">Time Slots</a>
        <a href="bookings.php">Bookings</a>
    </div>

    <h1>Admin Dashboard</h1>

    <!-- Room Management Section -->
    <h2>Manage Rooms</h2>
    <form method="POST">
        <input type="text" name="room_name" placeholder="Room Name" required><br>
        <input type="number" name="capacity" placeholder="Capacity" required><br>
        <textarea name="equipment" placeholder="Equipment"></textarea><br>
        <textarea name="description" placeholder="Room Description"></textarea><br>
        <input type="text" name="roompic" placeholder="Room Picture URL" required><br>
        <button type="submit" name="add_room">Add Room</button>
    </form>

    <h3>Existing Rooms</h3>
    <table>
        <thead>
            <tr>
                <th>Room Name</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $rooms_result->fetch()): ?>
                <tr>
                    <td><?php echo $row['room_name']; ?></td>
                    <td><?php echo $row['capacity']; ?></td>
                    <td>
                        <a href="edit_room.php?id=<?php echo $row['room_id']; ?>">Edit</a> |
                        <a href="?delete_id=<?php echo $row['room_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Time Slot Management Section -->
    <h2>Manage Time Slots</h2>
    <form method="POST">
        <select name="room_id" required>
            <option value="">Select Room</option>
            <?php while ($room = $rooms_result->fetch_assoc()): ?>
                <option value="<?php echo $room['room_id']; ?>"><?php echo $room['room_name']; ?></option>
            <?php endwhile; ?>
        </select><br>

        <input type="datetime-local" name="start_duration" required><br>
        <input type="datetime-local" name="end_duration" required><br>
        <button type="submit" name="add_timeslot">Add Time Slot</button>
    </form>

    <h3>Existing Time Slots</h3>
    <table>
        <thead>
            <tr>
                <th>Room Name</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $timeslot_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['room_name']; ?></td>
                    <td><?php echo $row['start_duration']; ?></td>
                    <td><?php echo $row['end_duration']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Booking Management Section -->
    <h2>Manage Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User Name</th>
                <th>Room Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $booking_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['FullName']; ?></td>
                    <td><?php echo $row['room_name']; ?></td>
                    <td><?php echo $row['Date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <?php if ($row['status'] != 'Cancelled'): ?>
                            <a href="?cancel_id=<?php echo $row['booking_id']; ?>">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

h1, h2, h3 {
    color: #333;
}

form {
    margin-bottom: 20px;
}

input, textarea, select {
    padding: 8px;
    margin: 5px;
    width: 100%;
    max-width: 300px;
}

button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: left;
}

th {
    background-color: #f8f8f8;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

</style>