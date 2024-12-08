<?php 
date_default_timezone_set('Asia/Bahrain');
$currentTime = date("Y-m-d H:i:s");
require("conn.php");

$query = "
    SELECT b.booking_id, t.tid
    FROM bookings b
    JOIN timeslot t ON b.tid = t.tid
    WHERE t.end_duration < ? AND b.status = 'Booked'
";


$r = $db->prepare($query);

$r->bindParam(1, $currentTime);


$r->execute();


if ($r->rowCount() > 0) {
    
    while ($row = $r->fetch()) {
        $bid = $row['booking_id'];
        $tid = $row['tid'];

        // Perform deletion for the booking and timeslot
        $deleteBookingQuery = "DELETE FROM bookings WHERE booking_id = ?";
        $deleteTimeslotQuery = "DELETE FROM timeslot WHERE tid = ?";
        
        // Delete the booking
        $deleteBooking = $db->prepare($deleteBookingQuery);
        $deleteBooking->bindParam(1, $bid);
        $deleteBooking->execute();
        
        // Delete the timeslot
        $deleteTimeslot = $db->prepare($deleteTimeslotQuery);
        $deleteTimeslot->bindParam(1, $tid);
        $deleteTimeslot->execute();

       
    }
} 



$deltid = "SELECT t.tid
    FROM timeslot t
    WHERE t.end_duration < ? 
    AND NOT EXISTS (
        SELECT 1 
        FROM bookings b 
        WHERE b.tid = t.tid
    )
";

$s = $db->prepare($deltid);
$s->bindParam(1, $currentTime);
$s->execute();

if ($s->rowCount() > 0) {
    while ($row = $s->fetch()) {
        $tid = $row['tid'];

        // Delete the timeslot if there is no booking
        $deleteTimeslotQuery = "DELETE FROM timeslot WHERE tid = ?";
        $deleteTimeslot = $db->prepare($deleteTimeslotQuery);
        $deleteTimeslot->bindParam(1, $tid);
        $deleteTimeslot->execute();

        
    }
} 

?>
