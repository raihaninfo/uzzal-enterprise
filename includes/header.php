<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php'];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['user_id']) && $current_page === 'login.php') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Income Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom Toggle Switch CSS */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #3b82f6;
        }

        .toggle-checkbox {
            right: 0;
            z-index: 1;
            transition: all 0.3s;
        }

        .toggle-label {
            width: 2.5rem;
            height: 1.25rem;
        }
    </style>
</head>

<body>
