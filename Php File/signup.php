<?php
session_start();
$fullNameRegex = '/^[a-zA-Z\s]{3,15}$/';
$semailRegex = '/^[0-9]{3,12}@stu\.uob\.edu\.bh$|^[a-zA-Z]{3,20}@uob\.edu\.bh$/';
$iemailregex ='/^[a-zA-Z]{3,20}@uob\.edu\.bh$/';
$passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,24}$/";
require("conn.php");

if(isset($_SESSION['Type'])){
if ($_SESSION['Type'] =='student' | $_SESSION["Type"]== "staff") {
    if($_SESSION['Type']=="student") {
    header("Location: HomePage.php");
    }
else if($_SESSION["Type"]== "Admin")
header("location:AdminPanel.php");
    exit();
} 
}
$msg = "";

if (isset($_POST['signup'])) {
    $fullName = $_POST['name']; 
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $confirmPassword = $_POST['re_pass'];

    $valid = true;

    if (!preg_match($fullNameRegex, $fullName)) {
        $msg = "Please enter a valid full name (3-15 alphabetic characters and spaces)";
        $valid = false;
    }

    if (preg_match($iemailregex, $email)) {
        $user = "staff";  // If the email matches the staff pattern
    } elseif (preg_match($semailRegex, $email)) {
        $user = "student";  // If the email matches the student pattern
    } else {
        $msg = "Invalid Email Please try to make the domain name is correct for example (123@uob.edu.bh for intructur 
        , 123@stu.uob.edu.bh for student";
        $valid = false;
    }
    


    if (!preg_match($passwordRegex, $password)) {
        $msg = "Password must be 8-25 characters long and include at least one lowercase letter, one uppercase letter, one digit, and one special character";
        $valid = false;
    }

    if ($password !== $confirmPassword) {
        $msg = "Confirm password does not match the password";
        $valid = false;
    }

    if ($valid) {
        $verify_email_query = $db->prepare("SELECT count(*) FROM user WHERE Email=?");
        $verify_email_query->bindParam(1, $email);
        $verify_email_query->execute();
        $count = $verify_email_query->fetchColumn();

        if ($count != 0) {
            $msg = "This email is already used. Please try another one.";
            $valid=false;
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $db->prepare("INSERT INTO user VALUES (NULL, ?, ?, ?,'$user','images/default.png')");
            $insert->bindParam(1, $fullName);
            $insert->bindParam(2, $email);
            $insert->bindParam(3, $hashedPassword);
            $insert->execute();
            $msg = "Registration successful!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Added Font Awesome link -->
</head>
<body>
<div class="main">
    <nav class="navbar">
        <h1>Welcome to UOB Students</h1>
    </nav>
    <section class="signup">
        <div class="container">
            <div class="signup-content">
                <div class="signup-form">
                    <h2 class="form-title">Sign up</h2>
                    <form method="POST" class="register-form" id="register-form" action="signup.php">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i></label>
                            <input type="text" name="name" id="name" placeholder="Your Name"/>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i></label>
                            <input type="email" name="email" id="email" placeholder="Your Email"/>
                        </div>
                        <div class="form-group">
                            <label for="pass"><i class="fas fa-lock"></i></label>
                            <input type="password" name="pass" id="pass" placeholder="Password"/>
                        </div>
                        <div class="form-group">
                            <label for="re_pass"><i class="fas fa-lock"></i></label>
                            <input type="password" name="re_pass" id="re_pass" placeholder="Repeat your password"/>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
                        </div>
                    </form>
                    <?php 
                    if(isset($valid)){ if ($valid) { ?>
    <div class="message-container sucess-container">
        <p class="sucess"><?php echo $msg; ?></p>
    </div>
<?php } else { ?>
    <div class="message-container fail-container">
        <p class="fail"><?php echo $msg; ?></p>
    </div>
<?php } }?>
                    <a href="signin.php" class="signup-image-link">I am already member</a>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>