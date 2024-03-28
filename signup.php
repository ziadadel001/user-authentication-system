<?php
session_start();
require_once 'C:\xampp\htdocs\user authentication system\database\DBconnector.php'; // Include the file containing database queries.

// Redirect users to the dashboard if they are already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = []; // Array to store validation errors
$registration_error = ''; // Variable to store registration error message

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Validate form data
    if (empty($_POST['username'])) {
        $errors['username'] = "Username is required";
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Password is required";
    }

    // If there are no validation errors, proceed with registration
    if (empty($errors)) {
        // Retrieve form data
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Store the user data in your database 
        $dbOperations = new DatabaseOperations();
        $user = $dbOperations->insert('users', ['NAME' => $username, 'PASSWORD' => $hashedPassword]);

        // Redirect the user to the login page after successful registration
        header("Location: login.php?registration_success=1");
        exit();
    } else {
        // Convert errors array to URL parameters
        $error_string = http_build_query($errors);
        // Redirect the user back to the sign-up page with errors
        header("Location: signup.php?registration_error=1&$error_string");
        exit();
    }
}
