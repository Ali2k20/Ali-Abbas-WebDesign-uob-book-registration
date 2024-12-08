<?php
session_start();
if (isset($_SESSION['Type'])) {
    if($_SESSION['Type']=="student" || $_SESSION["Type"]== "staff") {
    header("Location: HomePage.php");
    exit();
    }
else if($_SESSION["Type"]== "Admin")
header("location:AdminPanel.php");
    exit();
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" type="text/css" href="css/signin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Added Font Awesome link -->
</head>
<body>
<?php 
require("conn.php");

$msg = ""; // Initialize the message variable

if (isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $result = $db->prepare("SELECT * FROM user WHERE Email=?");
    $result->bindParam(1, $email);
    $result->execute();
    $row = $result->fetch();

    if ($row && password_verify($password, $row['Password'])) {
        $_SESSION['valid'] = $row['Email'];
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['userid'] = $row['id'];
        $_SESSION['Type'] = $row['Type'];

        if ($_SESSION['Type'] == 'student' ||$_SESSION['Type'] == 'staff'  ) {
            $_SESSION['activeUser'] = $row['Email'];
            $_SESSION['presearch'] = "";
            header("Location: HomePage.php");
            exit();
        } else if ($_SESSION['Type'] == 'Admin') {
            header("Location: AdminPanel.php");
            exit();
        }
    } else {
        $msg = "Incorrect Email and password";
    }
}
?>
<div class="main">
    <nav class="navbar">
        <h1>Welcome to UOB Students</h1>
    </nav>
    <section class="sign-in">
        <div class="container">
            <div class="signin-content">
                <div class="signin-form">
                    <h2 class="form-title">Sign in</h2>
                    <form method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="your_name"><i class="fas fa-user"></i></label>
                            <input type="text" name="email" id="your_name" placeholder="Your Email" />
                        </div>
                        <div class="form-group">
                            <label for="your_pass"><i class="fas fa-lock"></i></label>
                            <input type="password" name="pass" id="your_pass" placeholder="Password" />
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signin" id="signin" class="form-submit" value="Log in" />
                        </div>
                    </form>
                    <a href="signup.php" class="signup-image-link">Create an account</a>
                </div>
                <?php if ($msg != ""){?>
                <div class="message-container">
                    <p class="message"><?php echo $msg; ?></p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<?php $db = null; ?>
</body>
</html>