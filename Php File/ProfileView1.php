<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="css/profile1.css">
    
    <script>
        // Function to toggle the visibility of the edit profile form
        function toggleEditForm() {
            var form = document.getElementById('editProfileForm');
            var profile = document.getElementById('profileView');
            var button = document.getElementById('editProfileButton');

            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block"; // Show the form
                profile.style.display = "none"; // Hide the profile view
                button.style.display = "none"; // Hide the "Edit Profile" button
            } else {
                form.style.display = "none"; // Hide the form
                profile.style.display = "block"; // Show the profile view
                button.style.display = "block"; // Show the "Edit Profile" button again
            }
        }
    </script>
</head>
<body>
<?php
include("profilenav.php");
$_SESSION['page'] = 'view';

if (!isset($_SESSION['userid'])) {
    header("Location: signin.php");
    exit();
}

$id = $_SESSION['userid'];
$query = $db->prepare("SELECT FullName, Email, pfp, Type FROM user WHERE id=?");
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

$name = $user["FullName"];
$email = $user['Email'];
$profile_pic = $user['pfp'];
$Type = $user['Type'];
?>
    <div class="container">
        <header class="page-title">Profile</header>
        
        <!-- Profile View (Initially Visible) -->
        <div id="profileView" class="profile-container">
            <div class="profile-picture">
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
            </div>
            <div class="profile-details">
                <p><strong>Full Name:</strong> <?php echo $name; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Role:</strong> <?php echo $Type; ?></p>
            </div>
            <button class="btn" id="editProfileButton" onclick="toggleEditForm()">Edit Profile</button>
        </div>

        <!-- Edit Profile Form (Initially Hidden) -->
        <div id="editProfileForm" class="edit-profile-form">
            <form action="profileupdate.php" method="post" enctype="multipart/form-data">
                <header class="page-title">Edit Profile</header>
                <div class="edit-profile-picture">
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
                </div>
                <div class="field input profile-picture-update">
                    <label for="profile_pic">Update Profile Picture</label>
                    <input type="file" name="profile_pic" id="profile_pic">
                </div>
                <div class="field input">
                    <label for="name">Change Full Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $name; ?>" autocomplete="off">
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" value="<?php echo $email; ?>" autocomplete="off">
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
            <button class="btn back-btn" onclick="toggleEditForm()">Go Back</button>
        </div>
    </div>
</body>
</html>
