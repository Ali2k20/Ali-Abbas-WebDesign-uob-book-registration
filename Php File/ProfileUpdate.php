<?php
session_start();
include('conn.php');

if (!isset($_SESSION['userid'])) {
    header("Location: signin.php");
    exit();
}

$id = $_SESSION['userid'];

$query = $db->prepare("SELECT * FROM user WHERE id=?");
$query->bindParam(1, $id);
$query->execute();
$user = $query->fetch();

if (!$user) {
    echo "<div class='message'><p>User not found.</p></div><br>";
    echo "<a href='profile.php'><button class='btn'>Go Back</button></a>";
    exit();
}

if (isset($_POST['submit'])) {
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } else {
        $name = $user['FullName'];
    }

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    } else {
        $email = $user['Email'];
    }

    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }

    if (isset($_POST['confirm_password'])) {
        $confirm_password = $_POST['confirm_password'];
    } else {
        $confirm_password = '';
    }

    if (isset($_FILES['profile_pic'])) {
        $profile_pic = $_FILES['profile_pic'];
    } else {
        $profile_pic = null;
    }

    if ($_SESSION['Type'] == 'student') {
        $emailRegex = '/^[0-9]{3,12}@stu\.uob\.edu\.bh$/';
    } else if ($_SESSION['Type'] == 'staff') {
        $emailRegex = '/^[a-zA-Z]{3,20}@uob\.edu\.bh$/';
    }

    $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,24}$/";

    if (!preg_match($emailRegex, $email)) {
        echo "<div class='message'><p>Please enter a valid email address.</p></div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
    }

    if (!empty($password) && !preg_match($passwordRegex, $password)) {
        echo "<div class='message'><p>Please enter a valid password (8-24 characters, at least one uppercase letter, one lowercase letter, and one special character).</p></div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
    }

    if (!empty($_POST['password']) && $password !== $confirm_password) {
        echo "<div class='message'><p>Passwords do not match.</p></div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
    }

    if ($profile_pic && $profile_pic['error'] === 0) {
        $profile_pic_name = $profile_pic['name'];
        $profile_pic_tmp_name = $profile_pic['tmp_name'];
        $profile_pic_folder = 'images/' . $profile_pic_name;
        move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder);
    } else {
        $profile_pic_folder = $user['pfp'];
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    if (!empty($password)) {
        $update_query = $db->prepare("UPDATE user SET FullName=?, Email=?, Password=?, pfp=? WHERE id=?");
        $update_query->bindParam(1, $name);
        $update_query->bindParam(2, $email);
        $update_query->bindParam(3, $password);
        $update_query->bindParam(4, $profile_pic_folder);
        $update_query->bindParam(5, $id);
    } else {
        $update_query = $db->prepare("UPDATE user SET FullName=?, Email=?, pfp=? WHERE id=?");
        $update_query->bindParam(1, $name);
        $update_query->bindParam(2, $email);
        $update_query->bindParam(3, $profile_pic_folder);
        $update_query->bindParam(4, $id);
    }

    if ($update_query->execute()) {
        echo "<div class='message'><p>Profile Updated Successfully!</p></div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Home</button></a>";
    } else {
        echo "<div class='message'><p>There was an error updating your profile. Please try again.</p></div><br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
    }
} else {
    echo "<div class='message'><p>No data received to update the profile.</p></div><br>";
    echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
}
?>
