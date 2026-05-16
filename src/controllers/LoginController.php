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

                header("Location: " . $_SERVER['PHP_SELF'] . "?action=admin");
                exit();
            } else {
                $_SESSION['login_error'] = 'Invalid username or password.';
                header("Location: " . $_SERVER['PHP_SELF']);
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
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}
