<?php
session_start();
include('config.php');
include('jwt.php');

// mengecek apakah user terautentikasi atau tidak
if (!isset($_COOKIE['personal-session']) || !verify_jwt($_COOKIE['personal-session'])) {
    header('Location: login.php?error=not_logged_in');
    exit;
}

$user_data = decode_payload($_COOKIE['personal-session']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="navbar">
            <h1 class="logo">WikiWow</h1>
            <nav class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>My Profile</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($user_data['role']); ?></p>
    </div>
</body>

</html>
