<?php
// start session
session_start();

// include Redis client library
require_once 'predis/autoload.php';

// create Redis client
$redis = new Predis\Client();

// retrieve username and password from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// check if username and password are valid
// perform database query using prepared statement
$dsn = 'mysql:host=localhost;dbname=mydatabase;charset=utf8mb4';
$username_db = 'myusername';
$password_db = 'mypassword';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

try {
    $pdo = new PDO($dsn, $username_db, $password_db, $options);
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE username = ? AND password = ?');
    $stmt->execute(array($username, $password));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit();
}

// check if user exists
if (!$user) {
    // return error message as JSON
    echo json_encode(array('success' => false, 'message' => 'Invalid username or password.'));
    exit();
}

// set session data using Redis
$session_id = bin2hex(random_bytes(16)); // generate random session ID
$session_data = array('user_id' => $user['id'], 'username' => $user['username']); // store user ID and username
$redis->setex('session:' . $session_id, 3600, json_encode($session_data)); // set session data with expiration time of 1 hour

// set session ID in browser local storage
echo json_encode(array('success' => true, 'session_id' => $session_id));