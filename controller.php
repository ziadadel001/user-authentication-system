<?php
session_start();

// Include the DBoperations class
require_once 'C:\xampp\htdocs\user authentication system\database\DBConnector.php';

class UserController
{
    private $dbOperations;

    public function __construct()
    {
        // Initialize DBoperations object
        $this->dbOperations = new DatabaseOperations();
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
            // Retrieve username and password from the POST request
            $username = $_POST["name"];
            $password = $_POST["password"];

            // Retrieve user from the database
            $user = $this->dbOperations->select('users', ['NAME' => $username]);

            if ($user && password_verify($password, $user[0]["PASSWORD"])) {
                // Password is correct, set session variables
                $_SESSION["user_id"] = $user[0]["ID"];
                $_SESSION["username"] = $user[0]["NAME"];
                session_regenerate_id(true);
                header("Location: index.php");
                exit();
            } else {
                header("Location: login.php?error=password");
                exit();
            }
        }
    }

    public function signup()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
            // Validate form data
            if (empty($_POST['username']) || empty($_POST['password'])) {
                header("Location: signup.php?error=empty_fields");
                exit();
            }

            // Retrieve form data
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Store the user data in the database 
            $user = $this->dbOperations->insert('users', ['NAME' => $username, 'PASSWORD' => $hashedPassword]);

            // Redirect the user to the login page after successful registration
            header("Location: login.php?registration_success=1");
            exit();
        }
    }

    public function logout()
    {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header("Location: login.php"); // Redirect to login page
        exit();
    }
}

// Usage example:

$userController = new UserController();

// Determine which action to take based on the requested page
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'login':
            $userController->login();
            break;
        case 'signup':
            $userController->signup();
            break;
        case 'logout':
            $userController->logout();
            break;
        default:
            // Invalid action
            echo "Invalid action";
    }
}
