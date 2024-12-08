<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <?php include("profilenav.php"); 
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
        echo "<a href='profile.php'><button class='btn'>Go Back</button></a>";
        exit();
    }
    
    $name = $user["FullName"];
    $email = $user['Email'];
    $profile_pic = $user['pfp'];
    $Type = $user['Type'];
    ?>

    <div class="container">
        <header class="page-title">Profile</header>
        <div class="profile-picture">
            <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
        </div>
        <div class="profile-details">
            <p><strong>Full Name:</strong> <?php echo $name; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Role:</strong> <?php echo $Type; ?></p>
        </div>
        <button class="btn" onclick="toggleEditForm()">Edit Profile</button>

        <!-- Edit Form (Initially hidden) -->
        <div id="edit-profile-form" class="edit-form">
            <form action="updateprofile.php" method="post" enctype="multipart/form-data">
                <div class="field input">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $name; ?>" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                </div>

                <div class="field input">
                    <label for="profile_pic">Profile Picture</label>
                    <input type="file" name="profile_pic" id="profile_pic">
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>

                <div class="field input">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password">
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Save Changes">
                </div>
            </form>
        </div>
    </div>

    <script>
        // Function to toggle the edit profile form visibility
        function toggleEditForm() {
            const form = document.getElementById("edit-profile-form");
            form.style.display = form.style.display === "none" || form.style.display === "" ? "block" : "none";
        }
    </script>
</body>
</html>


<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.nav {
    background-color: #004fa3;
    color: #fff;
    padding: 0px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav .logo p a {
    color: #ffffff;
    text-decoration: none;
    font-size: 24px;
}

.nav .right-links {
    display: flex;
    align-items: center;
}

.nav .right-links a {
    color: #ffffff;
    text-decoration: none;
    padding: 10px 20px;
}

.nav .right-links .btn {
    background-color: #d4472e;
    border: none;
    color: #ffffff;
    padding: 10px 20px;
    cursor: pointer;
}

.nav .right-links .btn:hover,
.nav .right-links a:hover {
    background-color: #004085;
    color: #ffffff;
}

.container {
    max-width: 900px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.page-title {
    font-size: 2em;
    text-align: center;
    color: #003366;
    margin-bottom: 20px;
    font-weight: bold;
}

.profile-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.profile-picture {
    flex: 1;
    text-align: center;
    margin-right: 20px;
}

.profile-picture img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-details {
    flex: 2;
}

.profile-details p {
    font-size: 1.2rem;
}

.edit-form {
    display: none;
    margin-top: 20px;
}

.edit-form .field {
    margin-bottom: 15px;
}

.edit-form .field label {
    display: block;
    margin-bottom: 5px;
    color: #003366;
}

.edit-form .field input[type="text"],
.edit-form .field input[type="email"],
.edit-form .field input[type="password"],
.edit-form .field input[type="file"] {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.edit-form .field input[type="text"]:focus,
.edit-form .field input[type="email"]:focus,
.edit-form .field input[type="password"]:focus,
.edit-form .field input[type="file"]:focus {
    border-color: #004fa3;
    outline: none;
    background-color: #fff;
}

.edit-form .field input[type="submit"] {
    background-color: #003366;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    font-size: 1.1rem;
    transition: background-color 0.3s;
}

.edit-form .field input[type="submit"]:hover {
    background-color: #002244;
}

.message {
    background-color: #ffdddd;
    border-left: 6px solid #f44336;
    padding: 10px;
    margin-bottom: 15px;
    color: #f44336;
    font-size: 1.1rem;
}

@media screen and (max-width: 768px) {
    .container {
        margin: 20px;
        padding: 20px;
    }

    .profile-section {
        flex-direction: column;
        align-items: center;
    }

    .profile-picture {
        margin-bottom: 20px;
    }

    .profile-picture img {
        width: 120px;
        height: 120px;
    }

    .profile-details {
        width: 100%;
    }

    .edit-form .field input[type="text"],
    .edit-form .field input[type="email"],
    .edit-form .field input[type="password"],
    .edit-form .field input[type="file"],
    .edit-form .field input[type="submit"] {
        padding: 12px;
    }
}

</style>