<?php
require_once __DIR__ . '/functions.php';
$pageTitle = isset($pageTitle) ? $pageTitle : 'Home Haven';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escapeHtml($pageTitle); ?> | Home Haven</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark">H</span> Home Haven</a>
    <nav class="main-nav" aria-label="Main navigation">
        <a href="index.php">Browse homes</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="my-listings.php">My listings</a>
            <a href="add-property.php">Sell a property</a>
            <span class="nav-user">Hi, <?php echo escapeHtml($_SESSION['user_name']); ?></span>
            <a class="nav-button" href="logout.php">Log out</a>
        <?php else: ?>
            <a href="login.php">Log in</a>
            <a class="nav-button" href="register.php">Create account</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page-shell">
