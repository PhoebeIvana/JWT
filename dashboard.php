<?php
session_start();
include('config.php');
include('jwt.php');

// Mengautentikasi apakah terdapat token JWT yang valid pada cookie
if (!isset($_COOKIE['personal-session']) || !verify_jwt($_COOKIE['personal-session'])) {
    // Jika token tidak valid atau tidak ditemukan, redirect ke login
    header('Location: login.php');
    exit;
}

// Token JWT akan didecode; kemudian mengecek apakah role dari user admin?
$user_data = decode_payload($_COOKIE['personal-session']);
if ($user_data['role'] !== 'admin') {
    // Jikalau tidak, maka tidak diperbolehkan membuka laman dashboard.
    echo "You are not authorized to access this page.";
    exit;
}

// jikalau user melakukan POST?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Title dan content akan disimpan dalam var. di bawah ini.
    $title = $_POST['title'];
    $content = $_POST['content'];
    // Mengecek bilamana title dan content kosong, maka akan ditampilkan pesan error tertentu.
    if (empty($title) || empty($content)) {
        echo "Please fill in all fields.";
    } else {
        // Jikalau tidak kosong, maka title dan content yang diinput akan dimasukkan ke dalam articles table.
        $stmt = $conn->prepare("INSERT INTO articles (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        echo "Article added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="profile.php">My Profile</a>
        </div>
        <div class="nav-links">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Admin Dashboard <br> Add Article</h2>
        <form method="POST" action="dashboard.php">
            <label for="title">Article Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter article title" required>

            <label for="content">Article Content:</label>
            <textarea id="content" name="content" placeholder="Enter article content" rows="6" required 
            style="background-color: transparent; border: 1px solid #ccc; color: #333; font-size: 16px; width: 100%; padding: 10px;">
            </textarea>

            <br>
            <button class="button" type="submit">Add Article</button>
        </form>
    </div>
</body>

</html>