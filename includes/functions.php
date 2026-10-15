<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function formatPrice($price)
{
    return '₱' . number_format((float) $price, 2);
}
