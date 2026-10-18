<?php

session_start();

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard/index.php');
        exit;
    }

    if ($_SESSION['role'] === 'pedagang') {
        header('Location: pedagang/dashboard/index.php');
        exit;
    }
}

header('Location: login.php');
exit;