<?php 
session_start();
require("conn.php");
if (!isset($_SESSION["userid"])) {
   header("location:signin.php");
}
else if(isset($_SESSION["Type"]) && $_SESSION["Type"] == "student" | $_SESSION['Type']=='staff')
{
    header('location:HomePage.php');

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/AdminNav1.css">
</head>
<body>


</body>
</html>