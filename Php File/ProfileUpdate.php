<?php 
// 
// Ensure session is already started in 'profilenav.php'
session_start();
require("conn.php");

if (!isset($_SESSION['activeUser'])) {
    header("Location: signin.php");
    exit();
}

if ($_SESSION['Type'] == "Admin") {
    header("location:Admin.php");
    exit();
}

  if ($_SESSION['Type'] == "student") {
    $emailRegex = '/^[0-9]{3,12}@stu\.uob\.edu\.bh$/';
} else if ($_SESSION['Type'] == 'staff') {
    $emailRegex = '/^[a-zA-Z]{3,20}@uob\.edu\.bh$/';
}

$passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,24}$/";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submit'])) {
    
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    }
    $confirm_password = $_POST['confirm_password'];
    $profile_pic = $_FILES['profile_pic'];
    $id = $_SESSION['userid'];

    // Fetch the current user data from the database
    $check = $db->prepare("SELECT * FROM user WHERE id=?");
    $check->bindParam(1, $id);
    $check->execute();
    if ($r = $check->fetch()) {
        $currentEmail = $r['Email'];

        // Set the name and profile pic if not changed
        if (empty($_POST['name'])) {
            $name = $r['FullName'];
        } else {
            $name = $_POST['name'];
        }

        if ($_FILES["profile_pic"]["error"] === UPLOAD_ERR_NO_FILE) {
            $profile_pic_folder = $r['pfp'];  // Use the existing profile picture if no new one uploaded
        }
    }

    // Validate the email format
    if(!empty($_POST['email'])){
        $email = $_POST['email'];

    }else {$email = $r['Email']; }
    if (!preg_match($emailRegex, $email)) {
        echo "<div class='message'>
                <p>Please enter a valid email address . </p>
              </div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
        
    } 


    // Validate password format if password is provided
     if (!empty($_POST['password']) && !preg_match($passwordRegex, $password)) {
        echo "<div class='message'>
                <p>Please enter a valid password (8-25 characters, at least one uppercase letter, one lowercase letter, and one special character).</p>
              </div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
    } 
    // Check if passwords match
    else if (!empty($_POST['password']) && $password !== $confirm_password) {
        echo "<div class='message'>
                <p>Passwords do not match.</p>
              </div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
    } else {
        // Check if the email already exists in the database
        if (!empty($_POST['email'])) {
            $check = $db->prepare("SELECT * FROM user WHERE Email=?");
            $check->bindParam(1, $email);
            $check->execute();
            if ($check->rowCount() > 0) {
                echo "<div class='message'>
                        <p>The Email: $email is already in use. It won't be updated.</p>
                      </div><br>";
                $email = $currentEmail;
            }
        }

        // Handle profile picture upload if a new file is selected
        if ($_FILES["profile_pic"]["error"] == 0) {
            $profile_pic_name = $profile_pic['name'];
            $profile_pic_tmp_name = $profile_pic['tmp_name'];
            $profile_pic_folder = 'images/' . $profile_pic_name;
            move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder);
        }

        // Hash password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Prepare the SQL query to update user data
        if (!empty($_POST['password'])) {
            // If password is updated, include it in the query
            $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?, Password=?, pfp=? WHERE id=?");
            $edit_query->bindParam(1, $name);
            $edit_query->bindParam(2, $email);
            $edit_query->bindParam(3, $password);
            $edit_query->bindParam(4, $profile_pic_folder);
            $edit_query->bindParam(5, $id);
        } else {
            // If password is not updated, exclude it from the query
            $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?, pfp=? WHERE id=?");
            $edit_query->bindParam(1, $name);
            $edit_query->bindParam(2, $email);
            $edit_query->bindParam(3, $profile_pic_folder);
            $edit_query->bindParam(4, $id);
        }

        // Execute the update query
        $edit_query->execute();
        if ($edit_query) {
            echo "<div class='message'>
                    <p>Profile Updated!</p>
                  </div><br>";
            echo "<a href='profileview.php'><button class='btn'>Go Home</button></a>";
        }
    }
} else {
    echo "<div class='message'>
    <p>Error</p>
  </div><br>";
echo "<a href='profileview.php'><button class='btn'>Go Home</button></a>";
}
?>
