<?php
session_start();
include('conn.php');

if (!isset($_SESSION['userid'])) {
    header("Location: signin.php");
    exit();
}

$id = $_SESSION['userid'];
$emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_pic = $_FILES['profile_pic'];

    if (!preg_match($emailRegex, $email)) {
        echo "<div class='message'>
                <p>Please enter a valid email address.</p>
              </div> <br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
    }

    if (!empty($password) && $password !== $confirm_password) {
        echo "<div class='message'>
                <p>Passwords do not match.</p>
              </div> <br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
        exit();
    }

    $profile_pic_folder = '';
    if ($_FILES["profile_pic"]["error"] === UPLOAD_ERR_OK) {
        $profile_pic_name = $_FILES['profile_pic']['name'];
        $profile_pic_tmp_name = $_FILES['profile_pic']['tmp_name'];
        $profile_pic_folder = 'images/' . uniqid() . '_' . $profile_pic_name;
        move_uploaded_file($profile_pic_tmp_name, $profile_pic_folder);
    } else {
        $query = $db->prepare("SELECT pfp FROM user WHERE id=?");
        $query->bindParam(1, $id);
        $query->execute();
        $user = $query->fetch();
        $profile_pic_folder = $user['pfp'];
    }

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = $db->prepare("UPDATE user SET FullName=?, Email=?, Password=?, pfp=? WHERE id=?");
        $query->bindParam(1, $name);
        $query->bindParam(2, $email);
        $query->bindParam(3, $password);
        $query->bindParam(4, $profile_pic_folder);
        $query->bindParam(5, $id);
    } else {
        $query = $db->prepare("UPDATE user SET FullName=?, Email=?, pfp=? WHERE id=?");
        $query->bindParam(1, $name);
        $query->bindParam(2, $email);
        $query->bindParam(3, $profile_pic_folder);
        $query->bindParam(4, $id);
    }

    if ($query->execute()) {
        echo "<div class='message'>
                <p>Profile Updated!</p>
              </div> <br>";
        echo "<a href='profileview.php'><button class='btn'>Go Home</button></a>";
    } else {
        echo "<div class='message'>
                <p>Something went wrong while updating the profile.</p>
              </div> <br>";
        echo "<a href='profileview.php'><button class='btn'>Go Back</button></a>";
    }
}
?>
