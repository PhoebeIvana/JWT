<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read More</title>
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
        <?php
        include('config.php');

        if (isset($_GET['id'])) {
            $article_id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT title, content FROM articles WHERE id = ?");
            $stmt->bind_param("i", $article_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $article = $result->fetch_assoc();
                echo "<h2>" . htmlspecialchars($article['title']) . "</h2>";
                echo "<p>" . nl2br(htmlspecialchars($article['content'])) . "</p>";
            } else {
                echo "<h2>Article Not Found</h2>";
                echo "<p>The article you are looking for does not exist.</p>";
            }
        } else {
            echo "<h2>No Article Selected</h2>";
            echo "<p>Please go back and select an article to read more.</p>";
        }
        ?>
    </div>
</body>

</html>