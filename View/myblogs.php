<?php
session_start();
include '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

function logout()
{
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
    header("Location: ./view/Login.php");
}

// Check if the logout button is clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['logout'])) {
    logout();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's name for the navbar
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM usersDB WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($username) ?>'s Blogs</title>
</head>

<body class="bg-light">

    <!-- Navbar -->
    <div class="container bg-black text-light p-3 rounded my-4">
        <div class="d-flex align-items-center justify-content-between px-3">
            <h1><a href="index.php" class="text-white text-decoration-none"><i class="bi bi-shop-window px-2"></i><?php echo htmlspecialchars($username) ?>'s Blogs</a></h1>
            <div>
                <a href="../index.php" class="btn btn-primary"><i class="bi bi-plus-lg pe-2"></i>Home</a>
                <a href="CreateBlog.php" class="btn btn-primary"><i class="bi bi-plus-lg pe-2"></i>Create Post</a>
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <form method="post" class="d-inline">
                        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Blog Posts Section -->
    <div class="container mt-4">
        <div class="row">
            <?php
            // Fetch posts created by the user
            $db = new Database();
            $conn = $db->getConnection();
            $sql = "SELECT post_id, title, content, timestamp FROM posts WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) :
            ?>
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="mb-0">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="card-text">
                                <p><strong>Post ID:</strong> <?php echo $row['post_id']; ?></p>
                                <p><strong>Title:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                                <p><strong>Content:</strong> <?php echo htmlspecialchars($row['content']); ?></p>
                                <p><strong>Timestamp:</strong> <?php echo $row['timestamp']; ?></p>
                            </div>
                        </div>
                        <!-- Add an "Edit" button with a link to the edit modal -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['post_id']; ?>">
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#dltModal<?php echo $row['post_id']; ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['post_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form for editing post -->
                                <form action="../Models/Updatepost.php" method="post">
                                    <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                                    <div class="mb-3">
                                        <label for="editTitle" class="form-label">Title:</label>
                                        <input type="text" class="form-control" id="editTitle" name="editTitle" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editContent" class="form-label">Content:</label>
                                        <textarea class="form-control" id="editContent" name="editContent" rows="4" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                                    </div>


                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="dltModal<?php echo $row['post_id']; ?>" tabindex="-1" aria-labelledby="dltModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dltModalLabel">Delete Post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this post?</p>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" onclick="deletePost(<?php echo $row['post_id']; ?>)">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // function confirmDelete(post_id) {
        //     const confirmation = confirm("Are you sure you want to delete this post?");
        //     if (confirmation) {
        //         deletePost(post_id);
        //     }
        // }

        function deletePost(post_id) {
            fetch(`../Models/DeletePost.php?post_id=${post_id}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    if (response.ok) {
                        location.reload(); // Refresh page after successful deletion
                    } else {
                        throw new Error('Failed to delete post');
                    }
                })
                .catch(error => {
                    console.error('Error deleting post:', error);
                    // Handle error scenario
                });
        }
    </script>

</body>

</html>