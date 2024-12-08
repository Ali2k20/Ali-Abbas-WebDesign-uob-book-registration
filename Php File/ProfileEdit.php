<?php 





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X--Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    
    <title>Change Profile</title>
</head>
<body>
    <?php include("profilenav.php") ;
    $_SESSION['page']='edit';
    if (!isset($_SESSION['activeUser'])) {
        header("Location: signin.php");
        exit();
    }
    if ($_SESSION['Type'] == "Admin") {
        header("location:Admin.php");
    }
    else if($_SESSION['Type']=="student"){
    $emailRegex = '/^[0-9]{3,12}@stu\.uob\.edu\.bh$/';

    }else if($_SESSION['Type']== 'staff'){
        $emailRegex ='/^[a-zA-Z]{3,20}@uob\.edu\.bh$/';
    }
    $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,24}$/";
    
    
    ?>

    <div class="container">
        <div class="box form-box">
            <?php
            if (isset($_POST['submit'])) {
                $email = $_POST['email'];
                if(!empty($_POST['password']))
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                $profile_pic = $_FILES['profile_pic'];
                $id = $_SESSION['userid'];
                
                // Fetch the current user data
                $check = $db->prepare("SELECT * FROM user WHERE id=?");
                $check->bindParam(1, $id);
                $check->execute();
                if($r = $check->fetch()) {
                    if (empty($email)) {
                        $email = $r['Email'];
                    }
                    $currentEmail = $r['Email'];
                    
                   
                    if (empty($_POST['name'])) {
                        $name = $r['FullName'];
                    } else {
                        $name = $_POST['name'];
                    }

                    // Use the existing profile picture if none is uploaded
                    if ($_FILES["profile_pic"]["error"] === UPLOAD_ERR_NO_FILE) {
                        $profile_pic_folder = $r['pfp'];
                    }
                }

                // Validate email format
                if (!preg_match($emailRegex, $email)) {
                    echo "<div class='message'>
                        <p>Please enter a valid email address.</p>
                    </div> <br>";
                    echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
                } 
                // Validate password format if password field is not empty
                elseif (!empty($_POST['password']) && !preg_match($passwordRegex, $password)) {
                    echo "<div class='message'>
                        <p>Please enter a valid password (8-25 characters, at least one uppercase letter, one lowercase letter, and one special character).</p>
                    </div> <br>";
                    echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
                } 
                // Check if passwords match
                elseif (!empty($_POST['password']) && $password !== $confirm_password) {
                    echo "<div class='message'>
                        <p>Passwords do not match.</p>
                    </div> <br>";
                    echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
                } else {
                    // Check if the email already exists in the database
                    if (!empty($_POST['email'])) {
                        $check = $db->prepare("SELECT * FROM user WHERE Email=?");
                        $check->bindParam(1, $email);
                        $check->execute();
                        if ($check->rowCount() > 0) {
                            echo "<div class='message'>
                                <p>The Email: $email is already used so it won't be updated other than the email.</p>
                            </div> <br>";
                            $email = $currentEmail;
                        }
                    }

                    // Handle profile picture upload
                    if ($_FILES["profile_pic"]["error"] == 0) {
                        $profile_pic_name = $profile_pic['name'];
                        $profile_pic_tmp_name = $profile_pic['tmp_name'];
                        $profile_pic_folder = 'images/' . $profile_pic_name;
                        move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder);
                    }

                    // Hash the password if a new password is provided
                    if (!empty($_POST['password'])){
                        $password = password_hash($password, PASSWORD_DEFAULT);
                    }
                    

                    // Update user profile in the database
                    if(!empty($_POST['password'])){
                    $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?, Password=?, pfp=? WHERE id=?");

                    
                    $edit_query->bindParam(1, $name);
                    $edit_query->bindParam(2, $email);
                    $edit_query->bindParam(3, $password);
                    $edit_query->bindParam(4, $profile_pic_folder);
                    $edit_query->bindParam(5, $id);
                    
                }
                    else{
                    $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?,pfp=? WHERE id=?");
                    $edit_query->bindParam(1, $name);
                    $edit_query->bindParam(2, $email);
                  
                    $edit_query->bindParam(3, $profile_pic_folder);
                    $edit_query->bindParam(4, $id);
                    
                    }
                    $edit_query->execute();
                    if ($edit_query) {
                        echo "<div class='message'>
                                <p>Profile Updated!</p>
                            </div> <br>";
                        echo "<a href='profileview.php'><button class='btn'>Go Home</button></a>";
                    }
                }
            } else {
                // Fetch the current user data for the form
                $id = $_SESSION['userid'];
                $query = $db->prepare("SELECT * FROM user WHERE id=?");
                $query->bindParam(1, $id);
                $query->execute();

                while ($result = $query->fetch()) {
                    $name = $result['FullName'];
                    $res_Email = $result['Email'];
                    $res_Profile_Pic = $result['pfp'];
                }
            ?>
            <header class="page-title">Change Profile</header>
            <div class="profile-picture"> <img src="<?php echo $res_Profile_Pic; ?>" alt="Profile Picture"> </div>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="field input profile-picture-update"> 
                    <label for="profile_pic">Update Profile Picture</label>
                    <input type="file" name="profile_pic" id="profile_pic"> 
                </div>
                <div class="field input">
                    <label for="name">Change Full Name</label>
                    <input type="text" name="name" id="name" placeholder="Your Full Name: <?php echo $name; ?>" autocomplete="off">
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder="Your current email: <?php echo $res_Email; ?>" autocomplete="off">
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off">
                </div>

                <div class="field input">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" autocomplete="off">
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Update" style="border-radius: 12px;" required>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>
