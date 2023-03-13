<?php

session_start();
include("./db.php");
//require("vendor/autoload.php");
$manager = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
$redis = new Redis();
$redis->connect(REDIS_HOST, 6379);
// $client = new MongoClient();
//$client = new MongoDB\Client('mongodb://127.0.0.1:27017');
//$mdb = $cli->webappdb;
$collection = MONGODB_NAME;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Handle GET request
  $session_key = $_GET['session_key'];
  $session_value = $redis->get('session:' . $session_key);
  // check if session data is not empty and valid
  if (!empty($session_value)) {
    // update session expiry time
    $redis->expire('session:' . $session_key, 3600);
    $session_data = json_decode($session_value);
    // Set up query criteria
    $filter = ['username' => $session_data->username];
    // Set up options
    $options = ['limit' => 1];
    // Set up query object
    $query = new MongoDB\Driver\Query($filter, $options);

    // Execute query and get cursor
    $cursor = $manager->executeQuery($collection , $query);

    // Get matching document
    $document = current($cursor->toArray());

    // Print document
    //print_r($document);


    echo json_encode(array('success' => true, 'username' => $session_data->username,'dob'=>$document->dob,'contactAddress'=>$document->contactAddress));
    // session is valid
    
  } else {
    // destroy session
    //$redis->del($session_key);
    echo json_encode(array('success' => false,'message' => 'Session expired.','session_key' => $session_key));
  }  
  
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //echo json_encode(array('success' => true));
  $session_key = $_POST['session_key'];
  $username =$_POST['username'];
  $dob_str=$_POST['dob'];
  $contactAddress=$_POST['contactAddress'];
  $date_timestamp = strtotime($dob_str);
  $date_obj = date('Y-m-d H:i:s', $date_timestamp);
  
  //$query = new MongoDB\Driver\Query(array('username' => $username));
  //$cursor = $manager->executeQuery('mydb.profiles', $query);  

  $filter = ['username' => $username];
  $update = ['$set' => ['dob' => $dob_str,'contactAddress' => $contactAddress]];

  $options = [
    'multi' => false,   // update only the first matching document
    'upsert' => true   // do not insert a new document if no matching document is found
  ];
  // specify the collection to update


  // build the update query object
  $query = new MongoDB\Driver\BulkWrite();
  $query->update($filter, $update, $options);

  // execute the update query
  $result = $manager->executeBulkWrite($collection, $query);
  echo json_encode(array('success' => false,'message' => 'Successfully Updated1...'));
  // check if any documents were updated
  //if ($result->getModifiedCount() > 0) {
  //    echo json_encode(array('success' => false,'message' => 'Successfully Updated1...'));
  //} else {
  //  echo json_encode(array('success' => false,'message' => 'Successfully Updated2...'));
 // }

  //if($cursor->isDead())
  
  //$profile =  $mdb->customerprofiles->findone(['username' => $username]);
  //if (!empty($profile )) {
//    echo json_encode(array('success' => true, 'message'=>'Customer not found...'));
//  }else{
    //echo json_encode(array('success' => true, 'message'=>'Customer found...'));
  //}
  
  // Handle GET request

}

?>
