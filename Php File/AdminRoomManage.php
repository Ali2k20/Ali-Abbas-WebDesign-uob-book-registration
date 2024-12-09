<!DOCTYPE html>
<html lang="en">
    <?php
    require("conn.php");
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

    $sql = "SELECT r.room_id, r.room_name, r.capacity, r.equipment, r.des, r.roompic
    FROM rooms r
    WHERE NOT EXISTS (
        SELECT 1
        FROM bookings b
        WHERE b.room_id = r.room_id AND b.status = 'Booked')
    ";

    $notBooked = $db->prepare($sql);
    $notBooked->execute();

    $sql1 = "SELECT 
            r.room_id, 
            r.room_name, 
            r.capacity, 
            r.equipment, 
            r.des, 
            r.roompic, 
            t.start_duration, 
            t.end_duration, 
            u.FullName, 
            b.booking_id
        FROM rooms r
        JOIN bookings b ON b.room_id = r.room_id
        JOIN timeslot t ON t.tid = b.tid
        JOIN user u ON u.id = b.user_id
        WHERE b.status = 'Booked';
    ";

    $Booked = $db->prepare($sql1);
    $Booked->execute();

    $id = $_SESSION["userid"];
    $query1 = $db->prepare("SELECT * FROM user WHERE id = ?");
    $query1->bindParam(1, $id);
    $query1->execute();
    $s = $query1->fetch();
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="css/AdminManage.css">
    <link rel="stylesheet" href="css/Adminnav.css">
</head>
<body>
<nav>
    <div class="admin-header">
        <a href="AdminPanel.php"><img src="images/logo.png" alt="Admin Logo" class="admin-logo"></a>
    </div>
    <div class="admin-menu">
        <ul>
            <li><a href="AdminTimeManage.php" class="admin-booking">Room TimeSlot Management</a></li>
            <li>
                <div class="admin-dropdown">
                    <button class="admin-dropbtn">
                        <img class="admin-profile-picture" src="<?php echo $s['pfp']; ?>" alt="Admin Profile Picture">
                    </button>
                    <div class="admin-dropdown-content">
                        <a href="HomePage.php">Dashboard</a>
                        <a href="logout.php">Log Out</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>

    <header>
        <h2>Admin Room Management</h2>
    </header>

    <main>
        <h2 class="table-header">Available Rooms to edit and delete</h2>
        <div class="table-container">
        <div id="customAlert" class="custom-alert" style="display:none;">
        <div class="alert-content">
            <span id="alertMessage"></span>
            </div>
        </div>
        <?php if($notBooked->rowCount() > 0){ ?>
        <table class="room-table">
            <thead>
                <tr>
                    <th>Room View</th>
                    <th>Room ID</th>
                    <th>Room Name</th>
                    <th>Capacity</th>
                    <th>Eqipment</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r =$notBooked->fetch()){ ?>
                <tr>
                  <td><img src="<?php echo $r['roompic'] ?>" alt="error"></td>
                <?php 
                    echo "<td>{$r['room_id']}</td>";
                    echo "<td>{$r['room_name']}</td>";
                    echo "<td>{$r['capacity']}</td>";
                    echo "<td>{$r['equipment']}</td>";
                    echo "<td>{$r['des']}</td>";
                    ?>
                      <td>
                          <button class="edit-btn" type="button" onclick="openEditRoomModal('<?php echo $r['room_id']; ?> ')">Edit</button>
                          <form action="AdminDeleteRoom.php" method="POST" style="display:inline;">
                            <input type="hidden" name="room_id" value="<?php echo $r['room_id']; ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this room?') ">Delete</button>
                          </form>
                      </td>
                </tr>
             <?php } ?><button class="add-room-btn" onclick="openAddRoomModal()">Add New Room</button>
            </tbody>
        </table><?php } else { ?> <div class="notable"><p>There is no Rooms to edit or delete</p></div><button class="add-room-btn" onclick="openAddRoomModal()">Add New Room</button></div> <?php }?>
    </main>

   <main>
     <h2 class="table-header">Rooms that are Booked and Cannot Be Edited or Deleted Until the Booking is Finished</h2>
     <div class="table-container">
    <?php if ($Booked->rowCount() > 0) { ?>
    <table class="room-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Booked By</th>
                <th>Room ID</th>
                <th>Room Name</th>
                <th>Capacity</th>
                <th>Equipment</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $Booked->fetch()) { ?>
            <tr>
                <?php echo "<td>{$r['booking_id']}</td>";
                echo "<td>{$r['room_name']}</td>";
                echo "<td>{$r['room_id']}</td>";
                echo "<td>{$r['room_name']}</td>";
                echo "<td>{$r['capacity']}</td>";
                echo "<td>{$r['equipment']}</td>";
                echo "<td>{$r['des']}</td>";
                ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?> <div class="notable"><p>There is no booked Rooms </p></div> </div> <?php }?> 
</main>

    <div id="addRoomModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddRoomModal()">&times;</span>
            <h2>Add New Room</h2>
            <form action="AdminAdd.php" method="POST" enctype="multipart/form-data">
                <label for="room-name">Room Name:</label>
                <input type="text" id="room-name" name="room-name" required>
                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" required>
                <label for="Equipment">Equipment</label>
                <textarea id="equipment" name="Equ" required></textarea>
                <label for="description">Description:</label>
                <textarea id="description" name="des" required></textarea>
                <label for="room-pic">Room Picture:</label>
                <input type="file" id="room-pic" name="room-pic" required>
                <div class="form-buttons">
                    <button type="submit">Add Room</button>
                    <button type="button" class="close-modal-btn" onclick="closeAddRoomModal()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editRoomModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditRoomModal()">&times;</span>
            <h2>Edit Room</h2>
            <form action="AdminEditRoom.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit-room-id" name="room-id">
                <label for="edit-room-pic">Room Picture:</label>
                <input type="file" id="edit-room-pic" name="room-pic">
                <label for="edit-room-name">Room Name:</label>
                <input type="text" id="edit-room-name" name="room-name">
                <label for="edit-capacity">Capacity:</label>
                <input type="number" id="edit-capacity" name="capacity">
                <label for="edit-Equipment">Equipment:</label>
                <textarea id="edit-eqipment" name="eqi"></textarea>
                <label for="edit-description">Description:</label>
                <textarea id="edit-description" name="description"></textarea>
                <div class="form-buttons">
                    <button type="submit">Save Changes</button>
                    <button type="button" class="close-modal-btn" onclick="closeEditRoomModal()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'flex';
        }

        function closeAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'none';
        }

        function closeEditRoomModal() {
            document.getElementById('editRoomModal').style.display = 'none';
        }

        function showCustomAlert(message) {
            const alert = document.getElementById('customAlert');
            const alertMessage = document.getElementById('alertMessage');
            alertMessage.textContent = message;
            alert.style.display = 'block';
            alert.classList.add('show');
            history.pushState(null, '', location.pathname);
            setTimeout(() => {
                closeCustomAlert(); 
            }, 8000);
        }

        function closeCustomAlert() {
            const alert = document.getElementById('customAlert');
            alert.style.display = 'none';  
            alert.classList.remove('show');
        }

        <?php if (isset($_GET['msg']) && !empty($_GET['msg'])){ ?>
            showCustomAlert("<?php echo $_GET['msg']; ?>");
        <?php }; ?>

        function openEditRoomModal($roomid) {
            document.getElementById('editRoomModal').style.display = 'flex';
            document.getElementById('edit-room-id').value = $roomid;
        }
    </script>
    
     <footer>
        <p>&copy; Made By Mathiam And Sayed jaafar</p>
    </footer>
</body>
</html>
