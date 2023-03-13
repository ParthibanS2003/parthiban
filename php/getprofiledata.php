<?php
// connect to MongoDB
$mongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');

// get the session ID from the POST data
$sessionId = $_POST['sessionId'];

// create a Redis client
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// check if the session ID exists in Redis
if (!$redis->exists($sessionId)) {
	// session ID does not exist, return an error message
	echo json_encode(['status' => 'error', 'message' => 'You are not logged in.']);
	exit;
}

// retrieve the user ID from the session data in Redis
$userId = $redis->hGet($sessionId, 'userId');

// create a query to retrieve the user's username from MySQL
$stmt = $mysqli->prepare('SELECT username FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$username = $result->fetch_assoc()['username'];

// check if the username matches the one in the POST data
if ($username !== $_POST['username']) {
	// username does not match, return an error message
	echo json_encode(['status' => 'error', 'message' => 'You are not authorized to update this profile.']);
	exit;
}

// get the new profile data from the POST data
$age = $_POST['age'];
$dob = $_POST['dob'];
$contact_address = $_POST['contact_address'];

// create a filter to update the user's profile data in MongoDB
$filter = ['username' => $username];

// create an update query with the new profile data
$update = new MongoDB\Driver\BulkWrite();
$update->update(
	$filter,
	['$set' => [
		'age' => $age,
		'dob' => $dob,
		'contact_address' => $contact_address
	]],
	['multi' => false, 'upsert' => false]
);

// execute the update query
$mongo->executeBulkWrite('test.profile', $update);

// return a success message
echo json_encode(['status' => 'success']);
?>