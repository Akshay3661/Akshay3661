<?php
session_start();

require_once '../config/Database.php';

try {
    $db = new Database();

    $conn = $db->getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = sanitizeInput($_POST["title"]);
        $content = sanitizeInput($_POST["content"]);

    
        if (empty($title) || empty($content)) {
            throw new Exception("Error: Title and content are required.");
        }

        $titleLength = strlen($title);
        $contentLength = strlen($content);

        if ($titleLength < 3 || $titleLength > 255) {
            throw new Exception("Error: Title must be between 3 and 255 characters.");
        }

        if ($contentLength < 10) {
            throw new Exception("Error: Content must be at least 10 characters.");
        }

        if (isset($_SESSION['user_id'])) {
            // Use the user ID from the session
            $user_id = $_SESSION['user_id'];
        } else {
            header("Location: ../View/Login.php");
        }


        $postInsertQuery = "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)";
        $postInsertStmt = $conn->prepare($postInsertQuery);

        if (!$postInsertStmt) {
            throw new Exception("Error in preparing statement: " . $conn->error);
        }

        $postInsertStmt->bind_param("iss", $user_id, $title, $content);

        if (!$postInsertStmt->execute()) {
            throw new Exception("Error in executing statement: " . $postInsertStmt->error);
        }

        echo "New post created successfully";
        header("Location: ../View/myblogs.php"); 
        $postInsertStmt->close();
        exit();
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







