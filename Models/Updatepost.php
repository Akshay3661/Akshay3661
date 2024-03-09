<?php
session_start(); // Start the session

require_once '../config/Database.php';

try {
    $db = new Database();

    $conn = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $editTitle = sanitizeInput($_POST['editTitle']);
        $editContent = sanitizeInput($_POST['editContent']);
        $post_id = $_POST['post_id'];

        // Validate form data
        if (empty($editTitle) || empty($editContent)) {
            throw new Exception("Error: Title and content are required.");
        }

        $titleLength = strlen($editTitle);
        $contentLength = strlen($editContent);

        if ($titleLength < 3 || $titleLength > 255) {
            throw new Exception("Error: Title must be between 3 and 255 characters.");
        }

        if ($contentLength < 10) {
            throw new Exception("Error: Content must be at least 10 characters.");
        }

        // Update the post in the database
        $sql = "UPDATE posts SET title = ?, content = ? WHERE post_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $editTitle, $editContent, $post_id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating post: " . $stmt->error);
        }

        echo "Post updated successfully";
        header("Location: ../View/myblogs.php"); 
        $stmt->close();
    }
} catch (Exception $e) {
    // Handle exceptions (log, display an error message, etc.)
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    if ($conn) {
        $db->closeConnection();
    }
}

// Function to sanitize user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>