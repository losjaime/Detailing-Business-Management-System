<?php
// Assuming the necessary classes are included or autoloaded
require_once __DIR__ . '/../models/LoginDB.php'; // Path to LoginDB
require_once __DIR__ . '/../models/DataLayer.php'; // Path to DataLayer (if needed elsewhere)

class LoginController
{
    private $loginDB;

    public function __construct()
    {
        // Initialize the LoginDB class (which contains database-related methods)
        $this->loginDB = new LoginDB();
    }

    public function handleLoginRequest()
    {
        // First, check if the form was submitted via POST
        //if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //    // Debugging: Show the form data to ensure submission is correct
        //    var_dump($_POST); 
        //    exit(); // Remove this line after you finish debugging
        //}

        // Proceed with login if the "login" form is submitted
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Verify user credentials against the database
            $user = $this->loginDB->verifyUser($username, $password);

            if ($user) {
                // If user is found, regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user'] = $username; // Store the username in session

                // Redirect to the admin page after successful login
                header("Location: /projects/detail_lab/public/index.php?action=admin");
                exit();
            } else {
                // If credentials are invalid, set an error message and redirect to the login page
                $_SESSION['login_error'] = 'Invalid username or password.';
                header("Location: /projects/detail_lab/public/index.php");
                exit();
            }
        }
    }

    public function handleLogoutRequest()
    {
        // Check if logout request is made
        if (isset($_POST['logout'])) {
            // Destroy session and redirect to homepage
            session_unset();
            session_destroy();
            header("Location: /projects/detail_lab/public/index.php");
            exit();
        }
    }
}
