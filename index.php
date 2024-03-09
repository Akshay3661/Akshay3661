<?php
session_start();
include_once 'config/Database.php';

$db = new Database();
$conn = $db->getConnection();

function logout()
{
    $_SESSION = array();

    session_destroy();
    header("Location: ./index.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['logout'])) {
    logout();
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch search term from the form, if provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';


$searchCondition = empty($searchTerm) ? '' : "WHERE posts.title LIKE '%$searchTerm%' OR posts.content LIKE '%$searchTerm%' OR usersDB.username LIKE '%$searchTerm%'";
$sql = "SELECT posts.post_id, posts.title, posts.content, posts.timestamp, usersDB.username 
        FROM posts 
        INNER JOIN usersDB ON posts.user_id = usersDB.user_id 
        $searchCondition";
$result = $conn->query($sql);

// Check if there are posts
if ($result->num_rows > 0) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $posts = []; // If no posts, initialize an empty array
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Hello Blogs</title>
</head>

<body class="bg-light">

    <!-- Navbar -->
    <div class="container bg-black text-light p-3 rounded my-4">
        <div class="d-flex align-items-center justify-content-between px-3">
            <h1><a href="index.php" class="text-white text-decoration-none"><i class="bi bi-shop-window px-2"></i>Hello Blogs</a></h1>

            <form action="index.php" method="get" class="mt-3">
                <div class="input-group" style="width:400px;">
                    <input type="text" class="form-control" placeholder="Search..." name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </form>

            <div>
                <?php if ($isLoggedIn) : ?>
                    <form method="post" class="d-inline">
                        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                    </form>
                <?php else : ?>
                    <a href="./view/Login.php" class="btn btn-primary d-inline">Login/SignUp</a>
                <?php endif; ?>
                <a href="./View/myblogs.php" class="btn btn-primary d-inline"><i class="bi bi-plus-lg pe-2"></i>My Blogs</a>
            </div>
        </div>

    </div>


    <!-- Blog Posts Section -->
    <div class="container mt-4">
        <div class="row">
            <?php foreach ($posts as $post) : ?>
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="mb-0">
                                <?php echo $post['username'] . "'s Blog"; ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="card-text">
                                <p><strong>Post ID:</strong> <?php echo $post['post_id']; ?></p>
                                <p><strong>Title:</strong> <?php echo htmlspecialchars($post['title']); ?></p>
                                <p><strong>Content:</strong> <?php echo htmlspecialchars($post['content']); ?></p>
                                <p><strong>Timestamp:</strong> <?php echo $post['timestamp']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>