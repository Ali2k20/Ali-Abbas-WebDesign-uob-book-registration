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
        $fetch->bindParam(2, $end_time);  // Check if the new booking's end time conflicts with an existing booking
        $fetch->bindParam(3, $start_time);  // Check if the new booking's start time conflicts with an existing booking
        $fetch->bindParam(4, $start_time);  // Same check for reverse scenario: starts before, ends after
        $fetch->bindParam(5, $end_time);
        $fetch->execute();

        // Debugging: Output the number of rows fetched to ensure the conflict check is working
        //echo "Rows fetched: " . $fetch->rowCount() . "<br>";

        if ($fetch->rowCount() > 0) {
            echo "You already have a booking for this time. Please choose a different time.";
        } else {
            // Step 3: Create the booking (no conflict detected)
            $bookingQuery = "INSERT INTO bookings VALUES (Null,?, ?, ?, NOW(), 'Booked')";

            $insertStmt = $db->prepare($bookingQuery);
            $insertStmt->bindParam(1, $user_id);
            $insertStmt->bindParam(2, $room_id);
            $insertStmt->bindParam(3, $timeSlotId);
                
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
