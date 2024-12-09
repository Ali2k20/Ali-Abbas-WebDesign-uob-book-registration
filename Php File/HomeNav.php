<?php 


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/homepage.css">
</head>
<body>
<nav>
        <div class="image-container">
           <a href="HomePage.php"> <img src="images/logo.png" alt="Logo"></a>
        </div>
        <div class="SearchBar">
            <form action="roombrowsing.php" method="GET">
                <input type="search" name="query" placeholder="Search for Rooms.....">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="Menu">
            <ul>
            <li><a href="currentbooking.php" class="Book">Your Current Booking</a></li>
                <li>
                    <div class="dropdown">
                        <button class="dropbtn">
                            <img class="profile-picture-update" src="<?php echo $s['pfp']; ?>" alt="Profile Picture class='logo-img'">
                        </button>
                        <div class="dropdown-content">
                            <a href="HomePage.php">Home</a>
                            <a href="ProfileView.php">View Profile</a>
                            <a href="logout.php">Log Out</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</body>
</html>