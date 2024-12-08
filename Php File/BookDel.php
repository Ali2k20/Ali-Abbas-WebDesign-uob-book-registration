<?php 
session_start();
  require("conn.php");

  if(isset($_POST['bid'])){
    $bid = $_POST['bid'];
    $name = $_POST['name'];

  $CancelSql =" DELETE FROM bookings WHERE booking_id = ?";
  $a = $db ->prepare($CancelSql);
  $a->bindparam(1, $bid);
  $a->execute();
  if($a){
    $_SESSION['msg']='The Booking ID :' .$bid .",  With The Room Name $name Benn Successfully Canceled";
    header('location:currentBooking.php');
    exit();
  }


  } else{
    if($_SESSION['Type']=="student" | $_SESSION['Type']=="staff"){
        header("location:currentbooking.php");
        exit();
    }
    else if($_SESSION['Type']=="Admin")
    {
        header("AdminPanel.php");
           exit();
    }
    else{
        header('location:signin.php');
        exit();
    }
  }






?>