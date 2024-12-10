<?php 


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


if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submit'])) {
    
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    }
    $confirm_password = $_POST['confirm_password'];
    $profile_pic = $_FILES['profile_pic'];
    $id = $_SESSION['userid'];

  
    $check = $db->prepare("SELECT * FROM user WHERE id=?");
    $check->bindParam(1, $id);
    $check->execute();
    if ($r = $check->fetch()) {
        $currentEmail = $r['Email'];

        if (empty($_POST['name'])) {
            $name = $r['FullName'];
        } else {
            $name = $_POST['name'];
        }

        
    }

    if(!empty($_POST['email'])){
        $email = $_POST['email'];

    }else {$email = $r['Email']; }
    if (!preg_match($emailRegex, $email)) {
        $_SESSION['error1'] = "Please enter a valid email address.";
        header("Location: profileview.php");
        exit();
        
    } 


    
     if (!empty($_POST['password']) && !preg_match($passwordRegex, $password)) {
        $_SESSION['error1'] = "Please enter a valid password (8-25 characters, at least one uppercase letter, one lowercase letter, and one special character).";
        header("Location: profileview.php");
        exit();
    } 
   
    else if (!empty($_POST['password']) && $password !== $confirm_password) {
        $_SESSION['error1'] = "Passwords do not match.";
        header("Location: profileview.php");
        exit();
    } else {
      
        if (!empty($_POST['email'])) {
            $check = $db->prepare("SELECT * FROM user WHERE Email=?");
            $check->bindParam(1, $email);
            $check->execute();
            if ($check->rowCount() > 0) {
                $_SESSION['error1'] = "The Email: $email is already in use. It won't be updated.";
                $email = $currentEmail;
            }
        }

        
        if ($_FILES["profile_pic"]["error"] == 0) {
            $profile_pic_name = $profile_pic['name'];
            $profile_pic_tmp_name = $profile_pic['tmp_name'];
            
            
            if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $profile_pic_name)) {
                $_SESSION['error1'] = "Please upload a valid image file (JPG, JPEG, PNG, GIF, WEBP).";
                header("Location: profileview.php");
                exit();
            }
        
            
            $profile_pic_folder = 'images/' . $profile_pic_name;
            if (move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder)) {
                $roompic = $profile_pic_folder; 
            } else {
                $_SESSION['error1'] = "There was an error uploading the profile picture.";
                header("Location: profileview.php");
                exit();
            }
        } else {
            
            $profile_pic_folder = $r['pfp'];
        }

       
        if (!empty($_POST['password'])) {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        
        if (!empty($_POST['password'])) {
          
            $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?, Password=?, pfp=? WHERE id=?");
            $edit_query->bindParam(1, $name);
            $edit_query->bindParam(2, $email);
            $edit_query->bindParam(3, $password);
            $edit_query->bindParam(4, $profile_pic_folder);
            $edit_query->bindParam(5, $id);
        } else {
            
            $edit_query = $db->prepare("UPDATE user SET FullName=?, Email=?, pfp=? WHERE id=?");
            $edit_query->bindParam(1, $name);
            $edit_query->bindParam(2, $email);
            $edit_query->bindParam(3, $profile_pic_folder);
            $edit_query->bindParam(4, $id);
        }

       
        $edit_query->execute();
        if ($edit_query) {
            $_SESSION['success1'] = "Profile Updated!";
            header("Location: profileview.php");
            exit();
        }
    }
} else {
    $_SESSION['error1'] = "Error occurred during update.";
    header("Location: profileview.php");
    exit();
}
?>
