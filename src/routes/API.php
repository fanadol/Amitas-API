<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

include('User.php');
include('Thread.php');
include('Announcement.php');
include('Tag.php');
include('Groups.php');
include('Upload.php');
include('Report.php');

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:*');


//GET MAIN PAGE THREAD
$app->get('/api/thread', function(Request $request, Response $response) {
	$postingan = thread();
	$stack = array();

	foreach ($postingan as $post) {
		$post->user_id = attID_nonlink($post->user_id);
		array_push($stack,$post);
	}
	echo json_encode($stack);
});

//GET TAGGED PAGE THREAD
$app->get('/api/label_thread/{label}', function(Request $request, Response $response) {
    $label_name = $request->getAttribute('label');
	$postingan = ThreadByTag($label_name);
	if($postingan == null){
	    return $response->withJson("No Thread Found");
	} else {
	    $stack = array();
	    foreach ($postingan as $post) {
		    $post->user_id = attID_nonlink($post->user_id);
		    array_push($stack,$post);
    	}
	    return $response->withJson($stack);
	}
});

//GET DETAIL THREAD
$app->get('/api/thread/{id}', function(Request $request, Response $response) {
	//MENGAMBIL ATRIBUT ID
	$id = $request->getAttribute('id');
	$det_thread = detailThread($id);
	if($det_thread){
	    echo json_encode($det_thread);
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
	echo json_encode($stack);
});

//GET DETAIL USER
$app->get('/api/user/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$detail_user = detailID($id);

	echo json_encode($detail_user);
});

//GET ALL TAGS
$app->get('/api/tags', function(Request $request, Response $response) {
	$val = Tags();
	$stack = array();

	foreach ($val as $value) {
		array_push($stack,$value);
	}
	echo json_encode($stack);
});

//GET DETAIL TAGS
$app->get('/api/tag/{id}', function(Request $request, Response $response) {
    
    $id = $request -> getAttribute('id');
	$val = DetailTag($id);

	echo json_encode($val);
});

//GET ALL ANNOUNCEMENT
$app->get('/api/announcement', function(Request $request, Response $response) {
	$announ = Announcement();
	$stack = array();

	foreach ($announ as $value) {
		array_push($stack,$value);
	}
	echo json_encode($stack);
});

//GET COMMENT IN POST
$app->get('/api/comment/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$comment = Comment($id);

	echo json_encode($comment);
});

//GET A COMMENT 
$app->get('/api/singlecomment/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$comment = Single_Comment($id);

	echo json_encode($comment);
});

//GET ALL REPORT
$app->get('/api/report', function(Request $request, Response $response) {
	$val = report();

	echo json_encode($val);
});

//GET ALL GROUPS
$app->get('/api/groups', function(Request $request, Response $response) {
	$UKM = Groups();
	$stack = array();

	foreach ($UKM as $value) {
		array_push($stack,$value);
	}
	echo json_encode($stack);
});

//GET MY GROUP
$app->get('/api/mygroup/{id}', function(Request $request, Response $response) {
	
	$id = $request->getAttribute('id');
	$groupku = myGroup($id);

	echo json_encode($groupku);
});

//GET DETAIL GROUP
$app->get('/api/group/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$detGroup = detailGroup($id);

	echo json_encode($detGroup);
});

//GET CHECK GROUP MEMBER
$app->get('/api/group/check/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$checkGroup = groupMember($id);

	echo json_encode($checkGroup);
});

//GET COUNT GROUP MEMBER
$app->get('/api/group/member/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$totalMember = countAnggota($id);

	echo json_encode($totalMember);
});

//GET POST GROUP
$app->get('/api/group/post/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$totalMember = groupPost($id);
	$stack = array();
	
	foreach($totalMember as $value){
	   $value->user_id = attID($value->user_id);
	   array_push($stack,$value);
	}

	echo json_encode($stack);
});

//GET LOCATE POST IN WHICH GROUP
$app->get('/api/group/locatepost/{idpost}', function(Request $request, Response $response) {
	$id = $request->getAttribute('idpost');
	$locate = locatePost($id);
	echo json_encode($locate);
});

//GET GROUP MEMBER
$app->get('/api/group/member_list/{group_id}', function(Request $request, Response $response) {
	$group_id = $request->getAttribute('group_id');
	$totalMember = groupMemberList($group_id);
	echo json_encode($totalMember);
});

//GET ADMIN
$app->get('/api/admin/{id}', function(Request $request, Response $response) {
	$id = $request->getAttribute('id');
	$dataAdmin = Admin($id);

	echo json_encode($dataAdmin);
});

//POST LOGIN ADMIN
$app->post('/api/admin/login', function(Request $request, Response $response){
    
    $email = $request->getParam('email');
    $password = $request->getParam('password');
    
    $sql = "SELECT * FROM super_admin WHERE email = '$email' and password = '$password'";
    
    try {
        $db = new db();
        $db = $db->connect();
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_OBJ);
        $db = null;
        if(count($result) > 0){
            return $response->withJson($result);
        } else {
            $result = false;
            return $response->withJson($result);
        }
        
    } catch(PDOException $e){
        $result = [
            'notice' => $e->getMessage()
        ];
        
        return $response->withJson($result);
    }
    
});

//POST LOGIN USER
$app->post('/api/user/login', function(Request $request, Response $response){
    
    $user_id = $request->getParam('user_id');
    $password = $request->getParam('password');
    
    $sql = "SELECT * FROM user WHERE user_id = '$user_id' and password = '$password'";
    
    try {
        $db = new db();
        $db = $db->connect();
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_OBJ);
        $db = null;
        if(count($result) > 0){
            return $response->withJson($result);
        } else {
            $result = false;
            return $response->withJson($result);
        }
        
    } catch(PDOException $e){
        $result = [
            'notice' => $e->getMessage()
        ];
        
        return $response->withJson($result);
    }
    
});


/*
*
*************
METHOD CREATE
*************
*
*/

//CREATE ANNOUNCEMENT
$app->post('/api/announcement/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $title = $request->getParam('title');
  $content_text = $request->getParam('content_text');
  $label_id = $request->getParam('label_id');
  $image = $request->getParam('img_path');
  $banner = $request->getParam('banner');
  
  $filename = Upload($image);
  $filenamebanner = Upload($banner);

  $sql = 
  "
  START TRANSACTION;
  INSERT INTO posts VALUES ('',:user_id,:title,:content_text,CURRENT_TIMESTAMP(),0);
  SELECT LAST_INSERT_ID() INTO @post_id;
  INSERT INTO post_label VALUES ('',@post_id,:label_id);
  INSERT INTO attachments VALUES ('',@post_id,:image,'banner','$filenamebanner');
  COMMIT;
  ";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content_text', $content_text);
    $stmt->bindParam(':label_id',$label_id);
    $stmt->bindParam(':image',$filename);

	$stmt->execute();

	echo '{"notice" : {"text": "Your Posting Has Been Posted"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});


//CREATE THREAD
$app->post('/api/thread/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $title = $request->getParam('title');
  $content_text = $request->getParam('content_text');
  $label_id = $request->getParam('label_id');
  $image = $request->getParam('img_path');
  
  $filename = Upload($image);

  $sql = 
  "
  START TRANSACTION;
  INSERT INTO posts VALUES ('',:user_id,:title,:content_text,CURRENT_TIMESTAMP(),0);
  SELECT LAST_INSERT_ID() INTO @post_id;
  INSERT INTO post_label VALUES ('',@post_id,:label_id);
  INSERT INTO attachments VALUES ('',@post_id,:image,'img','$filename');
  COMMIT;
  ";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content_text', $content_text);
    $stmt->bindParam(':label_id',$label_id);
    $stmt->bindParam(':image',$filename);

	$stmt->execute();

	echo '{"notice" : {"text": "Your Posting Has Been Posted"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE USER
$app->post('/api/user/add', function(Request $request, Response $response){
    
    $userPhoto = $request->getParam('user_photo');
    $user_id = $request->getParam('user_id');
    $password = $request->getParam('password');
    $name = $request->getParam('name');
    $status = $request->getParam('status');
    $prodi = $request->getParam('prodi');
    $social_link = $request->getParam('social_link');
    
    $filename = Upload($userPhoto);

    $sql = "INSERT INTO user 
          VALUES (:user_id,:password,:name,:status,:prodi,CURRENT_TIMESTAMP(),:user_photo,:social_link)";

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
    $stmt->bindParam(':social_link', $social_link);
    $stmt->bindParam(':user_photo', $filename);

	$stmt->execute();

	echo '{"notice" : {"text": "User Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE COMMENT
$app->post('/api/comment/add', function(Request $request, Response $response){

  $user_id = $request->getParam('user_id');
  $post_id = $request->getParam('post_id');
  $comment = $request->getParam('comment');

  $sql = "INSERT INTO comments
          VALUES ('',:user_id,:post_id,:comment,CURRENT_TIMESTAMP())";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->bindParam(':comment', $comment);

	$stmt->execute();

	echo '{"notice" : {"text": "Tag Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE TAG
$app->post('/api/tag/add', function(Request $request, Response $response){

  $label_name = $request->getParam('label_name');
  $label_color = $request->getParam('label_color');

  $sql = "INSERT INTO labels
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
  $report_message = $request->getParam('report_message');

  $sql = "INSERT INTO reports 
          VALUES ('',:user_id,:reporter_id,:report_message)";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':reporter_id', $reporter_id);
    $stmt->bindParam(':report_message', $report_message);

	$stmt->execute();

	echo '{"notice" : {"text": "Report Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE GROUP
$app->post('/api/group/add', function(Request $request, Response $response){
    
    $user_id = $request->getParam('user_id');
    $logo = $request->getParam('logo');
    $name = $request->getParam('group_name');
    $group_info = $request->getParam('group_info');
    $website_link = $request->getParam('website_link');
    
    $filename = Upload($logo);

    $sql = 
    "
    START TRANSACTION;
        INSERT INTO groups VALUES ('',:name,CURRENT_TIMESTAMP(),:group_info,:logo,:website_link);
        INSERT INTO group_member VALUES ('',LAST_INSERT_ID(),
            :user_id,CURRENT_TIMESTAMP(),'Admin');
    COMMIT;
    ";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':logo', $filename);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':group_info', $group_info);
    $stmt->bindParam(':website_link', $website_link);


	$stmt->execute();

	echo '{"notice" : {"text": "Group Added"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//CREATE POST GROUP
$app->post('/api/group/posting', function(Request $request, Response $response){

    $user_id = $request->getParam('user_id');
    $group_id = $request->getParam('group_id');
    $postStatus = $request->getParam('postStatus');
    
    $sql = 
    "
    START TRANSACTION;
    INSERT INTO posts VALUES ('',:user_id,NULL,:postStatus,CURRENT_TIMESTAMP(),0);
    SELECT LAST_INSERT_ID() INTO @post_id;
    INSERT INTO group_post VALUES ('',:group_id,@post_id);
    COMMIT;
    ";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':group_id', $group_id);
    $stmt->bindParam(':postStatus', $postStatus);

	$stmt->execute();

	echo '{"notice" : {"text": "Your Posting Has Been Posted"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//JOIN GROUP
$app->post('/api/group/join', function(Request $request, Response $response){

    $group_id = $request->getParam('group_id');
    $user_id = $request->getParam('user_id');

    $sql = "INSERT INTO group_member
          VALUES ('',:group_id,:user_id,CURRENT_TIMESTAMP(),'Member')";

    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':group_id', $group_id);

	$stmt->execute();

	echo '{"notice" : {"text": "Member Added"}}';


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
  	$label_id = $request->getParam('label_id');
  	$img_path = $request->getParam('img_path');
  	
    $filename = Upload($img_path);

	$sql = 
	    "
	        UPDATE posts po
	        LEFT JOIN post_label pl
	        ON po.post_id = pl.post_id
	        LEFT JOIN attachments at
	        on po.post_id = at.post_id
	        SET 
	        po.title = :title,
	        po.content_text = :content_text,
	        pl.label_id = :label_id,
	        at.img_path = '$filename',
	        at.img_name = '$filename'
	        WHERE po.post_id = '$id';
		";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':title', $title);
    	$stmt->bindParam(':content_text', $content_text);
    	$stmt->bindParam(':label_id', $label_id);

		$stmt->execute();

		echo '{"notice" : {"text": "Thread Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE POST GROUP
$app->put('/api/group/post/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

  	$content_text = $request->getParam('content_text');

	$sql = 
	    "
	        UPDATE posts
	        SET content_text = :content_text
	        WHERE post_id = '$id';
		";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':content_text', $content_text);

		$stmt->execute();

		echo '{"notice" : {"text": "Post Updated"}}';


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
  	$userPhoto = $request->getParam('user_photo');
  	$social_link = $request->getParam('social_link');
  	
  	$filename = Upload($userPhoto);


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
    	$stmt->bindParam(':user_photo', $filename);
    	$stmt->bindParam(':social_link', $social_link);

		$stmt->execute();

		echo '{"notice" : {"text": "User Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE PROFILE USER
$app->put('/api/user/profile/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$password = $request->getParam('password');
  	$name = $request->getParam('name');
  	$userPhoto = $request->getParam('user_photo');
  	$social_link = $request->getParam('social_link');
  	
  	$filename = Upload($userPhoto);


	$sql = "UPDATE user SET 
			password = :password,
			name = :name,
			user_photo = :user_photo,
			social_link = :social_link
			WHERE user_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':password', $password);
    	$stmt->bindParam(':name', $name);
    	$stmt->bindParam(':user_photo', $filename);
    	$stmt->bindParam(':social_link', $social_link);

		$stmt->execute();

		echo '{"notice" : {"text": "User Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE GROUP
$app->put('/api/group/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$group_name = $request->getParam('group_name');
	$logo = $request->getParam('logo');
  	$description = $request->getParam('description');
  	$web_url = $request->getParam('web_url');
  	
  	$filename = Upload($logo);


	$sql = "UPDATE groups 
	        SET 
			name = :group_name,
			description = :description,
			logo = :logo,
			web_url = :web_url
			WHERE group_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);

    	$stmt->bindParam(':group_name', $group_name);
    	$stmt->bindParam(':logo', $filename);
    	$stmt->bindParam(':description', $description);
    	$stmt->bindParam(':web_url', $web_url);

		$stmt->execute();

		echo '{"notice" : {"text": "Group Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE TAG
$app->put('/api/tag/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$label_name = $request->getParam('label_name');
	$label_color = $request->getParam('label_color');


	$sql = "UPDATE labels SET 
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

//UPDATE COMMENT
$app->put('/api/comment/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$comment = $request->getParam('comment');


	$sql = "UPDATE comments 
	        SET 
			comment = :comment
			WHERE comments_id = $id";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		
    	$stmt->bindParam(':comment', $comment);

		$stmt->execute();

		echo '{"notice" : {"text": "Comment Updated"}}';


	} catch(PDOException $e){
		echo '{"Error": {"text": }'.$e->getMessage().'}';
	}
});

//UPDATE ADMIN
$app->put('/api/admin/update/{id}', function(Request $request, Response $response){

	$id = $request->getAttribute('id');

	$name = $request->getParam('name');
	$email = $request->getParam('email');
	$password = $request->getParam('password');


	$sql = "UPDATE super_admin SET 
			name = :name,
			email = :email,
			password = :password
			WHERE admin_id = '$id'";

	try{
		// Get DB Object
		$db = new db();
		// Connect
		$db = $db->connect();

		$stmt = $db->prepare($sql);
		
    	$stmt->bindParam(':name', $name);
    	$stmt->bindParam(':email', $email);
    	$stmt->bindParam(':password', $password);

		$stmt->execute();

		echo '{"notice" : {"text": "Admin Updated"}}';


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

	$sql = "DELETE FROM posts WHERE post_id = $id";

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

	$sql = "DELETE FROM labels WHERE label_id = $id";

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
$app->delete('/api/report/delete/{report_id}', function(Request $request, Response $response){

	$id = $request->getAttribute('report_id');

	$sql = "DELETE FROM reports WHERE report_id = $id";

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

//DELETE A GROUP
$app->delete('/api/group/delete/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');

	$sql = "DELETE from groups WHERE group_id = $id";

	try{
		$db = new db();
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute();
		$db = null;
		echo '{"notice" : {"text": "User Deleted"}}';
	} catch(PDOException $e){
		return array("Error: "=>$e.getMessage());
	}
});

//DELETE A COMMENT
$app->delete('/api/comment/delete/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');

	$sql = "DELETE from comments WHERE comments_id = $id";

	try{
		$db = new db();
		$db = $db->connect();
		$query = $db->prepare($sql);
		$query->execute();
		$db = null;
		echo '{"notice" : {"text": "Comment Deleted"}}';
	} catch(PDOException $e){
		return array("Error: "=>$e.getMessage());
	}
});

//DELETE GROUP MEMBER OR LEAVE GROUP
$app->delete('/api/group/leave/{group_id}/u/{user_id}', function(Request $request, Response $response){
	
	$user_id = $request->getAttribute('user_id');
	$group_id = $request->getAttribute('group_id');

	$sql = "DELETE from group_member WHERE user_id = $user_id AND group_id = $group_id";

	try{
		$db = new db();
		$db = $db->connect();
		$query = $db->prepare($sql);
		
		$query->execute();
		$db = null;
		echo '{"notice" : {"text": "Comment Deleted"}}';
	} catch(PDOException $e){
		return array("Error: "=>$e.getMessage());
	}
});