<?php
session_start();
require("conn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['userid']; 
    $room_id = $_POST['room_id']; 
    $timeSlotId = $_POST['timeslot'];

    // Step 1: Retrieve the start and end times from the timeslot based on the tid
    $timeSlotQuery = "
        SELECT start_duration, end_duration 
        FROM timeslot 
        WHERE tid = ?
    ";

    $stmt = $db->prepare($timeSlotQuery);
    $stmt->bindParam(1, $timeSlotId);
    $stmt->execute();
    $timeSlot = $stmt->fetch();

    if ($timeSlot) {
        $start_time = $timeSlot['start_duration'];
        $end_time = $timeSlot['end_duration'];

        // Step 2: Check for conflicts with existing bookings for the same user and same time duration
        $c = "
            SELECT * 
            FROM bookings b
            JOIN timeslot t ON b.tid = t.tid
            WHERE b.user_id = ? 
            AND t.start_duration = ? 
            AND t.end_duration = ? 
            AND b.status = 'Booked'
        ";

        $stmt = $db->prepare($c);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $start_time);
        $stmt->bindParam(3, $end_time);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "You already have a booking for this time. Please choose a different time.";
        } else {
          
            
          
                
                // Step 4: Create the booking
                $bookingQuery = "
                    INSERT INTO bookings (user_id, room_id, tid, Date, status)
                    VALUES (?, ?, ?, NOW(), 'Booked')
                ";

                $insertStmt = $db->prepare($bookingQuery);
                $insertStmt->bindParam(1, $user_id);
                $insertStmt->bindParam(2, $room_id);
                $insertStmt->bindParam(3,  $timeSlotId);
                
                if ($insertStmt->execute()) {
                    echo "Booking successful!";
                } else {
                    echo "There was an error with your booking. Please try again.";
                }
            
        }
    } else {
        echo "Invalid timeslot selected.";
    }
} else {
    echo "Invalid request method.";
}
?>
