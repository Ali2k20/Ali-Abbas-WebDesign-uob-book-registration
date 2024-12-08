

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
 <?php include("profilenav.php"); 
 $_SESSION['page']='view';
 if (!isset($_SESSION['userid'])) {
    header("Location: signin.php");
    exit();
}

$id = $_SESSION['userid'];
$query = $db->prepare("SELECT FullName,Email, pfp,Type FROM user WHERE id=?");
$query->bindParam(1, $id);
$query->execute();
$user = $query->fetch();

if (!$user) {
    echo "<div class='message'>
        <p>User not found.</p>
    </div> <br>";
    echo "<a href='profile.php'><button class='btn'>Go Back</button>";
    exit();
}
$name= $user["FullName"];
$email = $user['Email'];
$profile_pic = $user['pfp'];
$Type= $user['Type'];
 
 
 
 
 
 
 
 
 
 
 
 
 
 ?>
    <div class="container">
        <header class="page-title">Profile</header>
        <div class="profile-picture">
            <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
        </div>
        <div class="profile-details">
        <p><strong>Full Name:</strong> <?php echo $name; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Role:</strong> <?php echo $Type; ?></p>
        </div>
        <a href="profileedit.php?a=edit"><button class="btn">Edit Profile</button></a>
        
    </div>
</body>
<style>
</style>
</html>