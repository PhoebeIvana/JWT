<?php
session_start();
include('config.php');
include('jwt.php');

/*Pengecekan apakah parameter search tersedia pada URL?
1. Jika ya, maka '%' . $_GET['search'] . '%'  akan mencari LIKE dari $_GET['search']
2. Jika tidak, maka seluruh artikel akan direturn tanpa difilter.*/
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

$stmt = $conn->prepare("SELECT * FROM articles WHERE title LIKE ?");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();
/*Mengecek apakah terdapat token JWT yang tersimpan pada session tsb? 
Lalu, token JWT tersebut akan divalidasi eksistensinya. */
$is_logged_in = isset($_SESSION['token']) && verify_jwt($_SESSION['token']);
// Jika ya, maka payload dari token JWT akan didecode, dan nilai key `username` akan diakses.
// Jika tidak, maka dianggap bahwa tidak ada user terkait dengan session tsb., entah karena user tidak melakukan login, atau karena tokennya invalid.
$username = $is_logged_in ? decode_payload($_SESSION['token'])['username'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - WikiWow</title>
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
                <?php if ($is_logged_in): ?>
                    <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($is_logged_in): ?>
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <?php else: ?>
            <h1>Welcome to WikiWow</h1>
        <?php endif; ?>
        <form method="GET" action="index.php">
            <input type="text" name="search"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                placeholder="Search articles">
            <button class="button" type="submit">Search</button>
        </form>

        <div class="articles">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="article">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>
                        <?php
                        $short_content = substr($row['content'], 0, 200);
                        echo htmlspecialchars($short_content) . '...';
                        ?>
                    </p>
                    <a href="read-more.php?id=<?php echo $row['id']; ?>" class="read-more">Read more</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>
