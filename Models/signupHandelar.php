
<?php
session_start();
include "../config/Database.php";

$db = new Database();
$conn = $db->getConnection();

// Validate CSRF token
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['pwd'];
    $confirm_password = $_POST['CFMpwd'];

    // Validate and sanitize password
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit(); 
    }

    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usersDB (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $email);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id; // Store the user_id in the session
        echo "Registration successful!";
        header("Location: ../index.php");
        $conn->close();
        exit(); 
    } else {
        // Log the error for administrator reference
        error_log("Registration Error: " . $conn->error);
        echo "Registration failed. Please try again.";
    }
    
}
// Close database connection
$conn->close();


//use this to get  proper path
// include "";



