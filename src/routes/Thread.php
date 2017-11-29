<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET ALL THREAD
function thread(){
	$sql = "SELECT po.post_id,po.title,po.date,po.post_view,COUNT(co.comments_id) AS comment_count,po.user_id, la.label_id , la.label_name
          FROM post po LEFT JOIN comments co 
          ON po.post_id=co.post_id
          LEFT JOIN user us
          ON po.user_id=us.user_id
          LEFT JOIN post_label pl 
          ON po.post_id=pl.post_id
          LEFT JOIN label la
          ON pl.label_id=la.label_id
          WHERE po.title is not null
          GROUP BY po.post_id,po.title,po.date,po.post_view,po.user_id,us.name,us.user_photo, la.label_id , la.label_name";
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
	$sql = "SELECT po.post_id, title, po.date, po.content_text, COUNT(co.post_id) AS comment_count, pl.label_id, label_name, us.user_id, us.name, us.user_photo, COUNT(li.post_id) AS like_count, atc.atc_id, atc.img_path, co.comments_id, co.user_id,us2.name, us2.user_photo
		FROM post po LEFT JOIN user us 
		ON us.user_id=po.user_id 
		LEFT JOIN comments co 
		ON po.post_id=co.post_id
        LEFT JOIN user us2
        ON co.user_id=us2.user_id
		LEFT JOIN post_label pl 
		ON po.post_id=pl.post_id 
		LEFT JOIN label la 
		ON pl.label_id=la.label_id
        LEFT JOIN post_like li
        ON po.post_id=li.post_id
        LEFT JOIN attachments atc
        ON po.post_id=atc.post_id
        WHERE po.post_id = $id       
		GROUP BY po.post_id";

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

function Comment($id){
	try{
		$db = new db();
		$db = $db->connect();

		$sql = "SELECT co.comments_id, co.user_id, co.comment, us.name, us.user_photo
 				FROM post po
     			JOIN comments co 
 				ON po.post_id=co.post_id
        		JOIN user us 
  				ON us.user_id=co.user_id 
        		WHERE po.post_id= $id
        		group by co.comments_id, co.user_id, co.comment, us.name, us.user_photo";

		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		return $result;
	} catch(Exception $ex){
		return $response->withJson(array('error'=>$ex->getMessage()),422);
	}
}