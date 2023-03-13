<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Include database connection and utility functions
require_once 'db.php';
require_once 'utils.php';

// Get user profile data from MongoDB
$user_id = $_SESSION['user_id'];
$profile_data = get_profile_data($user_id);

// Render HTML for profile page
include 'header.php';
include 'profile.html';
include 'footer.php';
?>