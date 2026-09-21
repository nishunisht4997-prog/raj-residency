<?php
// admin/logout.php - Admin Logout
require_once __DIR__ . '/../includes/functions.php';

admin_logout();
set_flash_message('success', 'You have been successfully logged out.');
header('Location: login.php');
exit;
