<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!function_exists('is_logged_in')) {
  function is_logged_in() {
    return isset($_SESSION['username']) && $_SESSION['username'] !== '';
  }
}

if (!function_exists('is_admin')) {
  function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
  }
}
?>
