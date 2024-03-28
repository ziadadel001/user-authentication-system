<?php
session_start(); // Start a PHP session to manage user sessions.

require_once 'C:\xampp\htdocs\user authentication system\database\queries.php'; // Include the file containing database queries.

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) { // Check if the request method is POST and if the 'submit' button was clicked.

    // Retrieve username and password from the POST request.
    $username = $_POST["name"];
    $password = $_POST["password"];

    // Retrieve user from the database using the DatabaseOperations class.
    $dbOperations = new DatabaseOperations();
    $user = $dbOperations->select('users', ['NAME' => $username]);

    // Check if a user with the provided username exists.
    if ($user) {
        // Verify the provided password against the hashed password stored in the database.
        if (password_verify($password, $user[0]["PASSWORD"])) {
            // Password is correct, set session variables.
            $_SESSION["user_id"] = $user[0]["ID"];
            $_SESSION["username"] = $user[0]["NAME"];
            // Regenerate session ID for security.
            session_regenerate_id(true);
            // Redirect the user to the dashboard page upon successful login.
            header("Location: index.php");
            exit(); // Terminate script execution.
        } else {
            // Incorrect password, redirect the user to the login page with an error message.
            header("Location: login.php?error=password");
            exit(); // Terminate script execution.
        }
    } else {
        // User not found, redirect the user to the login page with an error message.
        header("Location: login.php?error=username");
        exit(); // Terminate script execution.
    }
}
