<?php
// $base_url is set dynamically in public/index.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$assets_base = rtrim(dirname($base_url), '/') . '/assets';
$is_logged_in = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Detail Lab</title>
    <meta charset="utf-8">
    <meta name="description" content="Detail Lab – Mobile Car Detailing">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0B2447">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $assets_base; ?>/styles/style.css">
    <link rel="icon" type="image/x-icon" href="<?php echo $assets_base; ?>/images/favicon.ico">
</head>

<body>
<div id="wrapper">

    <header>
        <div class="header-top-bar">
            <?php if (isset($_SESSION['login_error'])): ?>
                <span class="login-error"><?php echo htmlspecialchars($_SESSION['login_error']); ?></span>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <?php if ($is_logged_in): ?>
                <span class="logged-in-label">Admin</span>
                <form method="post" action="<?php echo $base_url; ?>?action=logout" class="login-form">
                    <button type="submit" name="logout" class="top-bar-btn">Logout</button>
                </form>
            <?php else: ?>
                <form method="post" action="<?php echo $base_url; ?>?action=login" class="login-form">
                    <input type="text"     name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit"  name="login" class="top-bar-btn">Login</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="header-brand">
            <h1><a href="<?php echo $base_url; ?>?action=index">Detail Lab</a></h1>
            <p class="header-tagline">Mobile Detailing That Comes to You</p>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="<?php echo $base_url; ?>?action=index">Home</a></li>
            <li><a href="<?php echo $base_url; ?>?action=services">Services</a></li>
            <li><a href="<?php echo $base_url; ?>?action=book">Book Now</a></li>
            <li><a href="<?php echo $base_url; ?>?action=testimonials">Reviews</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="<?php echo $base_url; ?>?action=admin">Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
