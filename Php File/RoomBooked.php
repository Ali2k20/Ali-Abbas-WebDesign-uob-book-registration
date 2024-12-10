<?php
session_start();
require("conn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_id'])) {
    $user_id = $_SESSION['userid']; 
    $room_id = $_POST['room_id']; 
    $timeSlotId = $_POST['timeslot'];

    // Step 1: Retrieve the start and end times from the timeslot based on the tid
    $timeSlotQuery = "
        SELECT start_duration, end_duration 
        FROM timeslot 
        WHERE tid = ?
    ";

    $fetch = $db->prepare($timeSlotQuery);
    $fetch->bindParam(1, $timeSlotId);
    $fetch->execute();
    $timeSlot = $fetch->fetch();

    if ($timeSlot) {
        $start_time = $timeSlot['start_duration'];
        $end_time = $timeSlot['end_duration'];

        // Step 2: Check for conflicts with the user's existing bookings across all rooms
        $conflictQuery = "
            SELECT * 
            FROM bookings b
            JOIN timeslot t ON b.tid = t.tid
            WHERE b.user_id = ? 
            AND b.status = 'Booked'
            AND (
                (t.start_duration < ? AND t.end_duration > ?)  -- New booking starts before and ends after
                OR 
                (t.start_duration < ? AND t.end_duration > ?)  -- New booking overlaps in reverse (ends before and starts after)
            )
        ";

        $fetch = $db->prepare($conflictQuery);
        $fetch->bindParam(1, $user_id);
        $fetch->bindParam(2, $end_time);  
        $fetch->bindParam(3, $start_time);  
        $fetch->bindParam(4, $start_time); 
        $fetch->bindParam(5, $end_time);
        $fetch->execute();

       

        if ($fetch->rowCount() > 0) {
            echo "You already have a booking for this time. Please choose a different time.";
            $_SESSION['error'] = "You already have a booking for this time. Please choose a different time.";
            header("Location: roomdetail.php?room_id=" . $room_id . "&message=" . $_SESSION['error']);
            exit();
        } else {
            
            $bookingQuery = "INSERT INTO bookings VALUES (Null,?, ?, ?, NOW(), 'Booked')";

            $insertStmt = $db->prepare($bookingQuery);
            $insertStmt->bindParam(1, $user_id);
            $insertStmt->bindParam(2, $room_id);
            $insertStmt->bindParam(3, $timeSlotId);
                
            if ($insertStmt->execute()) {
               
                $_SESSION['success'] = "Booking successful!";
                header("Location: roomdetail.php?room_id=" . $room_id . "&message=" . $_SESSION['success']);
                exit();
            } else {
                // also this extra but i already calculated all the posblities that it woudlnt happen
                $_SESSION['error'] = "There was an error with your booking. Please try again.";
                header("Location: roomdetail.php?room_id=" . $room_id . "&message=" . $_SESSION['error']);
                exit();
            }
        }
    } else {
        // If the timeslot is invalid this is extra from me but i dont think we need this
        $_SESSION['error'] = "Invalid timeslot selected.";
        header("Location: roomdetail.php?room_id=" . $room_id . "&message=" . $_SESSION['error']);
        exit();
    }
} else {  // same for this sayed extra from me 
    $_SESSION['error'] = "Invalid request method.";
    header("Location: roomdetail.php?room_id=" . $room_id . "&message=" . $_SESSION['error']);
    exit();
}
?>
