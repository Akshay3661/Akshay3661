<?php
session_start();
include('../config/Database.php');

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['identifier'], $_POST['password'])) {
        $identifier = $_POST['identifier'];
        $password = $_POST['password'];

        $sql = "SELECT user_id, username, email, password FROM usersDB WHERE username = ? OR email = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $identifier, $identifier); // Use the same input for both username and email
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["password"];

            if (password_verify($password, $hashedPassword)) {
                // Regenerate session ID on login
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row["id"];
                $_SESSION['username'] = ($row["username"]) ? $row["username"] : $row["email"]; // Set the session based on available data
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Invalid username or password";
                header("Location: ../View/Login.php"); 
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Invalid username or password";    
            header("Location: ../View/Login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Invalid input. Please try again.";
        header("Location: ../View/Login.php");
        exit();
    }
}
?>


