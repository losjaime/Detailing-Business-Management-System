<?php
// Assuming $base_url is set in the controller
$base_url = '/projects/detail_lab/public/index.php';

// Start session to track login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Detail Lab</title>
    <meta charset="utf-8">
    <meta name="description" content="Detail Lab">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/projects/detail_lab/public/assets/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/projects/detail_lab/public/assets/images/favicon.ico">
</head>

<body>
    <div class="header-content">
    <header>
    <div class="login-top-bar">
        <?php 
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error">' . $_SESSION['login_error'] . '</div>';
            unset($_SESSION['login_error']);
        }

        $is_logged_in = isset($_SESSION['user']);
        if ($is_logged_in): ?>
            <form method="post" action="<?php echo $base_url; ?>?action=logout">
                <button type="submit" name="logout">Logout</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?php echo $base_url; ?>?action=login">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        <?php endif; ?>
    </div>

    <h1><a href="<?php echo $base_url; ?>?action=index">Detail Lab</a></h1>
</header>

        <nav>
            <ul>
                <li><a href="<?php echo $base_url; ?>?action=index">Home</a></li>
                <li><a href="<?php echo $base_url; ?>?action=services">Services</a></li>
                <li><a href="<?php echo $base_url; ?>?action=book">Book Now</a></li>
                
                <!-- Only show this link if logged in -->
                <?php if ($is_logged_in): ?>
                    <li><a href="<?php echo $base_url; ?>?action=admin">Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <main>
