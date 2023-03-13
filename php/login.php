<?php
// start session
session_start();
include("./db.php");

#require_once 'vendor/autoload.php';
// include Redis client library
#require_once 'predis/autoload.php';

// create Redis client
$redis = new Redis();
$redis->connect(REDIS_HOST, 6379);
#$redis->auth('REDIS_PASSWORD');

// retrieve username and password from POST request
$username = $_POST['username'];
$password = $_POST['password'];
// perform database query using prepared statement

try {

    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $stmt = $conn->prepare('SELECT id, username,password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


// check if user exists
if (!$user) {
    // return error message as JSON
    echo json_encode(array('success' => false, 'message' => 'Invalid username or password1.'));
    exit();
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(array('success' => false, 'message' => 'Invalid username or password2.'));
    exit();
}

// set session data using Redis
$session_id = bin2hex(random_bytes(16)); // generate random session ID
$session_data = array('user_id' => $user['id'], 'username' => $user['username']); // store user ID and username
$redis->setex('session:' . $session_id, 3600, json_encode($session_data)); // set session data with expiration time of 1 hour

// set session ID in browser local storage
echo json_encode(array('success' => true, 'session_id' => $session_id));
//echo json_encode(array('success' => true, 's1' => password_hash($password, PASSWORD_DEFAULT), 's2' =>$password));

$stmt->close();
$conn->close();
} catch (mysqli_sql_exception $e) {    
    echo json_encode(array('success' => false, 'message' => 'Database error !!!'));
    exit();
}