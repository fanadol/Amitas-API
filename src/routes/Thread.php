<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET ALL THREAD
function thread(){
	$sql = "SELECT po.post_id, po.title, po.date, COUNT(co.comments_id) AS comment_count, po.user_id, us.name, us.user_photo, la.label_id, la.label_name, atc.atc_id, atc.img_path, po.content_text
          FROM posts po 
		  LEFT JOIN comments co 
          ON po.post_id=co.post_id
          LEFT JOIN user us
          ON po.user_id=us.user_id
          LEFT JOIN post_label pl 
          ON po.post_id=pl.post_id
          LEFT JOIN attachments atc
          ON po.post_id=atc.post_id
          LEFT JOIN labels la
          ON pl.label_id=la.label_id
          WHERE po.title is not null
          GROUP BY po.post_id, po.title, po.date, po.user_id, us.name, us.user_photo, la.label_id , la.label_name, atc.atc_id, atc.img_path, po.content_text
          ORDER BY po.post_id desc";
    try {
      $db = new db();
      $db = $db->connect();
      $query = $db->query($sql);
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      return $result;
    } catch(PDOException $e) {
      return array("error" => $e->getMessage());
    }
}

function detailThread($id){
	$sql = "SELECT po.post_id, po.title, po.date, po.content_text, COUNT(co.post_id) AS comment_count, pl.label_id, la.label_name, us.user_id, us.name, us.user_photo, atc.atc_id, atc.img_path
		FROM posts po 
		LEFT JOIN user us 
		ON us.user_id=po.user_id 
		LEFT JOIN comments co 
		ON po.post_id=co.post_id
		LEFT JOIN post_label pl 
		ON po.post_id=pl.post_id 
		LEFT JOIN labels la 
		ON pl.label_id=la.label_id
        LEFT JOIN attachments atc
        ON po.post_id=atc.post_id
        WHERE po.post_id = '$id'     
		GROUP BY po.post_id, po.title, po.date, po.user_id, us.name, us.user_photo, la.label_id , la.label_name, atc.atc_id, atc.img_path, po.content_text";

    try {
        $db = new db();
        $db = $db->connect();
        $query = $db->query($sql);
        $result = $query->fetch(PDO::FETCH_OBJ);
        $db = null;
        
        $image = $result->user_photo;
        $decoded = file_get_contents('../src/images/'.$image);
        // Read image path, convert to base64 encoding
        $imageData = base64_encode($decoded);
    
        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:image/png;base64,'.$imageData;
        $result->user_photo = $src;
      
        $image = $result->img_path;
        $decoded = file_get_contents('../src/images/'.$image);
        // Read image path, convert to base64 encoding
        $imageData = base64_encode($decoded);
    
        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:image/png;base64,'.$imageData;
        $result->img_path = $src;
      
      return $result;
    } catch(PDOException $e) {
      return array("error" => $e->getMessage());
    }
}

function Single_Comment($id){
	try{
		$db = new db();
		$db = $db->connect();

		$sql = "SELECT *
		        FROM comments
        		WHERE comments_id = $id";

		$query = $db->query($sql);
		$result = $query->fetch(PDO::FETCH_OBJ);
		$db = null;
		return $result;
	} catch(Exception $ex){
		return $response->withJson(array('error'=>$ex->getMessage()),422);
	}
}

function ThreadByTag($label){
	try{
		$db = new db();
		$db = $db->connect();

		$sql = "SELECT po.post_id,po.title,po.date,po.post_view,COUNT(co        .comments_id) AS comment_count,po.user_id, la.label_id , la.label_name
          FROM posts po LEFT JOIN comments co 
          ON po.post_id=co.post_id
          LEFT JOIN user us
          ON po.user_id=us.user_id
          LEFT JOIN post_label pl 
          ON po.post_id=pl.post_id
          LEFT JOIN labels la
          ON pl.label_id=la.label_id
          WHERE po.title is not null and la.label_name='$label'
          GROUP BY po.post_id,po.title,po.date,po.post_view,po.user_id,us.name,us.user_photo, la.label_id , la.label_name";

		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		if($query->rowCount() > 0){
		    return $result;
		} else {
		    return null;
		}

	} catch(Exception $ex){
		return $response->withJson(array('error'=>$ex->getMessage()),422);
	}
}