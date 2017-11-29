<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

include('User.php');
include('Thread.php');
include('Announcement.php');
include('Tag.php');
include('Report.php');

//GET ALL ANNOUNCEMENT
$app->get('/api/announcement', function(Request $request, Response $response) {
	$announ = Announcement();
	$stack = array();

	foreach ($announ as $value) {
		array_push($stack,$value);
	}
	echo json_encode(array('result'=>$stack));
});

//GET MAIN PAGE THREAD
$app->get('/api/thread', function(Request $request, Response $response) {
	$postingan = thread();
	$stack = array();

	foreach ($postingan as $post) {
		$post->user_id = attID($post->user_id);
		array_push($stack,$post);
	}
	echo json_encode(array('result'=>$stack));
});

//GET DETAIL THREAD
$app->get('/api/thread/{id}', function(Request $request, Response $response) {
	//MENGAMBIL ATRIBUT ID
	$id = $request->getAttribute('id');
	$det_thread = detailThread($id);
	$stack = array();
	if($det_thread){
		foreach ($det_thread as $value) {
		$value->comments_id = Comment($id);
		array_push($stack,$value);
	}
		echo json_encode(array('result'=>$stack));
	} else {
		echo json_encode(array('status'=>'Thread Not Found'));
	}
	
});

//GET ALL USER
$app->get('/api/user', function(Request $request, Response $response) {
	$id = ID();
	$stack = array();

	foreach ($id as $value) {
		array_push($stack,$value);
	}
	echo json_encode(array('result'=>$stack));
});

//GET ALL TAGS
$app->get('/api/tags', function(Request $request, Response $response) {
	$val = Tags();
	$stack = array();

	foreach ($val as $value) {
		array_push($stack,$value);
	}
	echo json_encode(array('result'=>$stack));
});

//GET ALL REPORT
$app->get('/api/report', function(Request $request, Response $response) {
	$val = report();
	$stack = array();

	foreach ($val as $value) {
		$value->reporter_id = attID($value->reporter_id);
		array_push($stack,$value);
	}
	echo json_encode(array('result'=>$stack));
});

/*
*
*************
METHOD CREATE
*************
*
*/

//CREATE THREAD
$app->post('/api/thread/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $title = $request->getParam('title');
  $content_text = $request->getParam('content_text');

  $sql = "INSERT INTO post 
          VALUES ('',:user_id,:title,:content_text,CURRENT_TIMESTAMP(),0)";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content_text', $content_text);

	$stmt->execute();

	echo '{"notice" : {"text": "Your Posting Has Been Posted"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE USER
$app->post('/api/user/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $password = $request->getParam('password');
  $name = $request->getParam('name');
  $status = $request->getParam('status');
  $prodi = $request->getParam('prodi');

  $sql = "INSERT INTO user 
          VALUES (:user_id,:password,:name,:status,:prodi,CURRENT_TIMESTAMP(),'http://www.canterbury.ac.nz/media/images/people/Default-Staff-Profile-Image-Placeholder_6881327390178250815.jpg','')";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':prodi', $prodi);

	$stmt->execute();

	echo '{"notice" : {"text": "User Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE TAG
$app->post('/api/tag/add', function(Request $request, Response $response){

  $label_name = $request->getParam('label_name');
  $label_color = $request->getParam('label_color');

  $sql = "INSERT INTO label 
          VALUES ('',:label_name,:label_color)";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':label_name', $label_name);
    $stmt->bindParam(':label_color', $label_color);

	$stmt->execute();

	echo '{"notice" : {"text": "Tag Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE REPORT
$app->post('/api/report/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $reporter_id = $request->getParam('reporter_id');
  $report_type_id = $request->getParam('report_type_id');
  $report_message = $request->getParam('report_message');

  $sql = "INSERT INTO reports 
          VALUES ('',:user_id,:reporter_id,:report_type_id,:report_message)";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':reporter_id', $reporter_id);
    $stmt->bindParam(':report_type_id', $report_type_id);
    $stmt->bindParam(':report_message', $report_message);

	$stmt->execute();

	echo '{"notice" : {"text": "Report Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});


/*
*
*************
METHOD UPDATE
*************
*
*/

//UPDATE THREAD
$app->put('/api/thread/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

  	$title = $request->getParam('title');
  	$content_text = $request->getParam('content_text');

	$sql = "UPDATE post SET 
			title = :title,
			content_text = :content_text
			WHERE post_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':title', $title);
    	$stmt->bindParam(':content_text', $content_text);

		$stmt->execute();

		echo '{"notice" : {"text": "Thread Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE USER
$app->put('/api/user/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$user_id = $request->getParam('user_id');
	$password = $request->getParam('password');
  	$name = $request->getParam('name');
  	$status = $request->getParam('status');
  	$prodi = $request->getParam('prodi');
  	$user_photo = $request->getParam('user_photo');
  	$social_link = $request->getParam('social_link');


	$sql = "UPDATE user SET 
			user_id = :user_id,
			password = :password,
			name = :name,
			status = :status,
			prodi = :prodi,
			user_photo = :user_photo,
			social_link = :social_link
			WHERE user_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':user_id', $user_id);
    	$stmt->bindParam(':password', $password);
    	$stmt->bindParam(':name', $name);
    	$stmt->bindParam(':status', $status);
    	$stmt->bindParam(':prodi', $prodi);
    	$stmt->bindParam(':user_photo', $user_photo);
    	$stmt->bindParam(':social_link', $social_link);

		$stmt->execute();

		echo '{"notice" : {"text": "User Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE TAG
$app->put('/api/tag/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$label_name = $request->getParam('label_name');
	$label_color = $request->getParam('label_color');


	$sql = "UPDATE label SET 
			label_name = :label_name,
			label_color = :label_color
			WHERE label_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		
    	$stmt->bindParam(':label_name', $label_name);
    	$stmt->bindParam(':label_color', $label_color);

		$stmt->execute();

		echo '{"notice" : {"text": "Tag Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE REPORT
$app->put('/api/report/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$user_id = $request->getParam('user_id');
	$reporter_id = $request->getParam('reporter_id');
	$report_type_id = $request->getParam('report_type_id');
	$report_message = $request->getParam('report_message');


	$sql = "UPDATE reports SET 
			user_id = :user_id,
			reporter_id = :reporter_id,
			report_type_id = :report_type_id,
			report_message = :report_message
			WHERE report_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		
    	$stmt->bindParam(':user_id', $user_id);
    	$stmt->bindParam(':reporter_id', $reporter_id);
    	$stmt->bindParam(':report_type_id', $report_type_id);
    	$stmt->bindParam(':report_message', $report_message);

		$stmt->execute();

		echo '{"notice" : {"text": "Report Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

/*
*
*************
METHOD DELETE
*************
*
*/

//DELETE A THREAD
$app->delete('/api/thread/delete/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$sql = "DELETE FROM post WHERE post_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$db = null;
		echo '{"notice" : {"text": "Thread Deleted"}}';
	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//DELETE A USER
$app->delete('/api/user/delete/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$sql = "DELETE FROM user WHERE user_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$db = null;
		echo '{"notice" : {"text": "User Deleted"}}';
	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//DELETE A TAG
$app->delete('/api/tag/delete/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$sql = "DELETE FROM label WHERE label_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$db = null;
		echo '{"notice" : {"text": "Tag Deleted"}}';
	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//DELETE A REPORT
$app->delete('/api/report/delete/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$sql = "DELETE FROM reports WHERE post_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$db = null;
		echo '{"notice" : {"text": "Report Deleted"}}';
	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});