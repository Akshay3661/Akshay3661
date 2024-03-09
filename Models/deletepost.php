<?php
session_start();
include '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;

    if ($post_id) {


        // Delete the post from the posts table
        $sql = "DELETE FROM posts WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Internal Server Error
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(400); // Bad Request
    }
} else {
    http_response_code(405); // Method Not Allowed
}
