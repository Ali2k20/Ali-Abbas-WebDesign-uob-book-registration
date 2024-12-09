<!DOCTYPE html>
<html lang="en">
    <?php
    
    require("conn.php");
    include("TimerDel.php");
    session_start();
    if(isset($_SESSION['userid'])){
    if ($_SESSION['Type']=='student' | $_SESSION['Type']=='staff'){
            header('location:HomePage.php');
            exit();
    }
}
    else{
        header('location:signin.php');
        exit();
    }
    
    
    $sql = "SELECT t.tid, t.start_duration, t.end_duration, r.room_name, r.room_id
    FROM timeslot t
    LEFT JOIN rooms r ON r.room_id = t.room_id
    WHERE NOT EXISTS (
        SELECT 1 
        FROM bookings b 
        WHERE b.tid = t.tid AND b.status = 'Booked')";

    $notBookedSlots = $db->prepare($sql);
    $notBookedSlots->execute();

  
    $sql1 = " SELECT 
            t.tid, 
            t.start_duration, 
            t.end_duration, 
            r.room_name, 
            r.room_id, 
            u.FullName, 
            b.booking_id
        FROM timeslot t
        JOIN bookings b ON b.tid = t.tid
        JOIN rooms r ON r.room_id = b.room_id
        JOIN user u ON u.id = b.user_id
        WHERE b.status = 'Booked';
    ";

    $bookedSlots = $db->prepare($sql1);
    $bookedSlots->execute();

    $id = $_SESSION["userid"];
    $query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
    $query1->bindParam(1, $id);
    $query1->execute();
    $s = $query1->fetch();
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeSlot Management</title>
    <link rel="stylesheet" href="css/AdminManage.css">
    <link rel="stylesheet" href="css/Adminnav.css">
</head>
<body>
<nav>
    <div class="admin-header">
        <a href="AdminPanel.php"><img src="images/logo.png" alt="Admin Logo" class="admin-logo"></a>
        <div id="clock-container">
            <div id="greeting"></div>
            <div id="clock"></div>
        </div>
    </div>

    <div class="admin-menu">
        <ul>
            <li><a href="AdminRoomManage.php" class="admin-booking">TimeSlot Management</a></li>
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

<header>
    <h2>Admin TimeSlot Management</h2>
</header>

<main>
   
    <h2 class="table-header">Available TimeSlots to Edit or Delete</h2>
    <div class="table-container">
        <?php if ($notBookedSlots->rowCount() > 0) { ?>
        <table class="room-table">
            <thead>
                <tr>
                    <th>TimeSlot ID</th>
                    <th>Room Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $notBookedSlots->fetch()) { ?>
                <tr>
                    <td><?php echo $r['tid']; ?></td>
                    <td><?php echo $r['room_name']; ?></td>
                    <td><?php echo date(format: "F j, Y g:i A", timestamp: strtotime($r['start_duration'])); ?></td>
                    <td><?php echo date(format: "F j, Y g:i A", timestamp: strtotime($r['end_duration'])); ?></td>
                    <td>
                    <button class="edit-btn" type="button" onclick="openEditTimeSlotModal('<?php echo $r['tid']; ?>', '<?php echo $r['room_id']; ?>')">Edit</button>

                        <form action="AdminTimeDel.php" method="POST" style="display:inline;">
                            <input type="hidden" name="tid" value="<?php echo $r['tid']; ?>">
                            <input type="hidden" name="rid" value="<?php echo $r['room_id']; ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this time slot?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
                <button class="add-room-btn" onclick="openAddTimeSlotModal()">Add New TimeSlot</button>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="notable"><p>No Available TimeSlots to edit or delete</p></div>
            <button class="add-room-btn" onclick="openAddTimeSlotModal()">Add New TimeSlot</button>
        </div>
        <?php } ?>
        <div id="customAlert" class="custom-alert" style="display:none;">
        <div class="alert-content">
            <span id="alertMessage"></span>
            
        </div>
    </div>
</main>
<main>
   
    <h2 class="table-header">Booked TimeSlots (Cannot Edit or Delete Until Booking is Finished)</h2>
    <div class="table-container">
        <?php if ($bookedSlots->rowCount() > 0) { ?>
        <table class="room-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Booked By</th>
                    <th>Room Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $bookedSlots->fetch()) { ?>
                <tr>
                    <td><?php echo $r['booking_id']; ?></td>
                    <td><?php echo $r['FullName']; ?></td>
                    <td><?php echo $r['room_name']; ?></td>
                    <td><?php echo date(format: "F j, Y g:i A", timestamp: strtotime($r['start_duration'])); ?></td>

                    <td><?php echo date(format: "F j, Y g:i A", timestamp: strtotime($r['end_duration'])); ?></td>

                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="notable"><p>No Booked TimeSlots</p></div>
        </div>
        <?php } ?>
    </div>
    
</main>


<div id="addTimeSlotModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddTimeSlotModal()">&times;</span>
        <h2>Add New TimeSlot</h2>
        <form action="AdminTimeAdd.php" method="POST">
            <label for="room-id">Room ID:</label>
            <select id="room-id" name="room-id" required>
                <?php
                $roomsQuery = $db->prepare("SELECT room_id, room_name FROM rooms");
                $roomsQuery->execute();
                while ($room = $roomsQuery->fetch()) {
                    echo "<option value='{$room['room_id']}'>{$room['room_name']}</option>";
                }
                ?>
            </select>

            <label for="start-time">Start Time:</label>
            <input type="datetime-local" id="start-time" name="start-time" required>

            <label for="end-time">End Time:</label>
            <input type="datetime-local" id="end-time" name="end-time" required>

            <div class="form-buttons">
                <button type="submit">Add TimeSlot</button>
                <button type="button" class="close-modal-btn" onclick="closeAddTimeSlotModal()">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit TimeSlot Modal -->
<div id="editTimeSlotModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditTimeSlotModal()">&times;</span>
        <h2>Edit TimeSlot</h2>
        <form action="AdminTimeEdit.php" method="POST">
            <!-- Hidden input for TimeSlot ID -->
            <input type="hidden" id="edit-tid" name="tid">

            <!-- Hidden input for Room ID -->
            <input type="hidden" id="edit-room-id" name="rid">

            <label for="edit-start-time">Start Time:</label>
            <input type="datetime-local" id="edit-start-time" name="start-time" required>

            <label for="edit-end-time">End Time:</label>
            <input type="datetime-local" id="edit-end-time" name="end-time" required>

            <div class="form-buttons">
                <button type="submit">Save Changes</button>
                <button type="button" class="close-modal-btn" onclick="closeEditTimeSlotModal()">Close</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddTimeSlotModal() {
        document.getElementById('addTimeSlotModal').style.display = 'flex';
    }

    function closeAddTimeSlotModal() {
        document.getElementById('addTimeSlotModal').style.display = 'none';
    }

    function openEditTimeSlotModal(tid, rid) {
    document.getElementById('editTimeSlotModal').style.display = 'flex';
    document.getElementById('edit-tid').value = tid;
    document.getElementById('edit-room-id').value = rid;
}

    function closeEditTimeSlotModal() {
        document.getElementById('editTimeSlotModal').style.display = 'none';
    }
</script>


<script>
function showCustomAlert(message) {
    const alert = document.getElementById('customAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    alertMessage.textContent = message;
    alert.style.display = 'block';
    alert.classList.add('show');

    
    history.pushState(null, '', location.pathname);

    setTimeout(() => {
        closeCustomAlert(); 
    }, 5000);
}

function closeCustomAlert() {
    const alert = document.getElementById('customAlert');
    alert.style.display = 'none';  
    alert.classList.remove('show');
}


    // Show the alert if the message exists and say to it goodbye after 8 seconds from it appearing inshallah it goes well :)
    <?php if (isset($_GET['msg']) && !empty($_GET['msg'])){ ?>
        showCustomAlert("<?php echo $_GET['msg']; ?>");
    <?php }; ?>
</script>
<footer>
        <p>&copy; Made By Mathiam And Sayed jaafar</p>
    </footer>
</body>
</html>
