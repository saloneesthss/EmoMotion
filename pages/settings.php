<?php
session_start();
require_once '../components/user-navbar.php';
require_once '../connection.php';

$uploadPath = '../assets/users-images';
$id=(int) $_GET['id'];

$sql="select * from users where id=$id";
$stmt=$con->prepare($sql);
$stmt->execute();
$user=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$user) {
    header("Location:user-profile.php?error=No user found with the given ID.");
    die;
}

if($_SERVER['REQUEST_METHOD']==='POST') {
    $name=$_POST['name'];
    $email=$_POST['email'];
    $dob=$_POST['dob'];
    $weight=$_POST['weight'];
    $height=$_POST['height'];
    $country=$_POST['country'];
    $city=$_POST['city'];
    $locality=$_POST['locality'];

    $imageNameOld = $_POST['image_name_old'];
    $imageName = $imageNameOld;
    if(is_uploaded_file($_FILES['image_name']['tmp_name'])) {
        if (!empty($imageNameOld) && file_exists('../assets/users-images/' . $imageNameOld)) {
            unlink('../assets/users-images/' . $imageNameOld);
        }
        $imageName=$_FILES['image_name']['name'];
        move_uploaded_file($_FILES['image_name']['tmp_name'], $uploadPath . "/" . $imageName);
    }
    
    $sql="update users set name='$name', email='$email', image='$imageName', dob='$dob', weight='$weight', height='$height', country='$country', city='$city', locality='$locality' where id=$id";
    $stmt=$con->prepare($sql);
    $stmt->execute();

    header("Location:user-profile.php?success=Profile updated successfully.");
    die;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmoMotion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/settings.css">
    <link rel="stylesheet" href="../styles/user-profile.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h2>EmoMotion</h2>

            <a href="user-profile.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="my-plans.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-dumbbell"></i> My Plans</a>
            <a href="my-workouts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-video"></i> My Workouts</a>
            <a href="my-posts.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-comments"></i> My Posts</a>
            <a href="settings.php?id=<?php echo $user['id'];?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    
        <div class="container">
            <div class="title">Edit Profile</div>
            <form method="POST" enctype="multipart/form-data">
                <div class="grid">
                    <div>
                        <label>Full Name</label>
                        <input type="text" value="<?php echo $user['name'] ?>" name="name" required />
                    </div>

                    <div>
                        <label>Image</label>
                        <input type="file" accept=".webp, .jpg, .jpeg, .png" name="image_name" required />
                        <input type="hidden" name="image_name_old" value="<?php echo $user['image']; ?>">
                        <?php if (!empty($user['image']) && file_exists('../assets/users-images/' . $user['image'])) { ?>
                            <img width="100" src="../assets/users-images/<?php echo $user['image']; ?>" name="image">
                        <?php } ?>
                    </div>
                    
                    <div>
                        <label>Date of Birth</label>
                        <input type="date" value="<?php echo $user['dob'] ?>" name="dob" required />
                    </div>

                    <div>
                        <label>Email Address</label>
                        <input type="email" value="<?php echo $user['email'] ?>" name="email" required />
                    </div>

                    <div>
                        <label>Weight</label>
                        <input type="number" value="<?php echo $user['weight'] ?>" name="weight" required />
                    </div>

                    <div>
                        <label>Height</label>
                        <input type="number" value="<?php echo $user['height'] ?>" name="height" required />
                    </div>

                    <div>
                        <label>Country</label>
                        <input type="text" value="<?php echo $user['country'] ?>" name="country" required />
                    </div>

                    <div>
                        <label>City</label>
                        <input type="text" value="<?php echo $user['city'] ?>" name="city" required />
                    </div>

                    <div>
                        <label>Locality</label>
                        <input type="text" value="<?php echo $user['locality'] ?>" name="locality" required />
                    </div>
                </div>

                <div class="btn-row">
                    <button type="reset" class="btn cancel">Cancel</button>
                    <button type="submit" class="btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
