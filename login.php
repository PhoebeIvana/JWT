<?php
session_start();
include('config.php');
include('jwt.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    // Cek apakah username dan password diisi
    if ($username && $password) {
        // Cek user di database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Validasi password
        if ($user && password_verify($password, $user['password'])) {
            // Buat payload JWT dengan username dan role dari database
            $payload = [
                "username" => $user['username'], // Ambil dari database
                "role" => $user['role'],        // Ambil dari database
            ];

            // Buat token JWT
            $jwt = create_jwt($payload);

            // Simpan token di cookie
            set_jwt_cookie($jwt);

            // Redirect ke halaman utama
            header('Location: index.php');
            exit;
        } else {
            $error_message = "Invalid username or password!";
        }
    } else {
        $error_message = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
        </div>
    </div>

    <div class="container">
        <h2>Login</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button class="button" type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>
