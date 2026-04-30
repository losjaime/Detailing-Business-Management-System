<?php

// Make sure to include the necessary files for database connection and other classes.
require_once __DIR__ . '/../models/DataLayer.php'; // Adjust path to DataLayer.php

class LoginDB
{
    private $db;

    public function __construct()
    {
        // Initialize the DataLayer (database) connection
        $this->db = new DataLayer();
    }

    // Function to check the username and password from the database
    public function verifyUser($username, $password)
    {
        // Query to fetch the user's hashed password
        $stmt = $this->db->getConnection()->prepare("SELECT username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If the username exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password using password_verify()
            if (password_verify($password, $user['password'])) {
                return $user['username']; // If passwords match, return the username
            }
        }

        return false; // Return false if the username or password is incorrect
    }
}
