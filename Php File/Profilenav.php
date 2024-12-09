    <?php 
    session_start();
    require("conn.php");
    $id = $_SESSION['userid'];
    $query1 = $db->prepare("SELECT * FROM user where id =? ");
    $query1->bindParam(1,$id);
    $query1->execute();
    $s = $query1->fetch();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="css/profilenav1.css">
    </head>
    <body>
        <nav>
            <div class="image-container">
            <a href="HomePage.php"> <img src="images/logo.png" alt="Logo"></a>
            </div>
            <h1>Profile View Page </h1>
            <div class="Menu">
                <ul>
                  
                    <li>
                        <div class="dropdown">
                            <button class="dropbtn">
                                <img class="profile" src="<?php echo $s['pfp']; ?>" alt="Profile Picture class='logo-img'">
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