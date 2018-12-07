<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

function Groups(){
	$sql = "SELECT gr.group_id, gr.logo, gr.name, us.name AS admin, gr.date_created, gr.description, gr.web_url FROM groups gr 
			JOIN group_member gm
 			ON gr.group_id=gm.group_id 
			JOIN user us 
 			ON gm.user_id=us.user_id
    		WHERE gm.role='admin'
    		GROUP BY gr.group_id, gr.logo, gr.name, gr.date_created, gr.description, gr.web_url";

	try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		for($i=0; $i<count($result); $i++){
		    $image = $result[$i]->logo;
            $decoded = file_get_contents('../src/images/'.$image);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode($decoded);
        
            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:image/png;base64,'.$imageData;
            $result[$i]->logo = $src;
		}
		
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}
}

function detailGroup($id){

	$sql = "SELECT gr.group_id, gr.logo, gr.name, us.name AS admin, gr.date_created, gr.description, gr.web_url FROM groups gr 
			JOIN group_member gm
 			ON gr.group_id=gm.group_id 
			JOIN user us 
 			ON gm.user_id=us.user_id
    		WHERE gr.group_id=$id AND gm.role='admin'
    		GROUP BY gr.group_id, gr.logo, gr.name, gr.date_created, gr.description, gr.web_url";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetch(PDO::FETCH_OBJ);
		$db = null;
		
		$image = $result->logo;
        $decoded = file_get_contents('../src/images/'.$image);
        // Read image path, convert to base64 encoding
        $imageData = base64_encode($decoded);
    
        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:image/png;base64,'.$imageData;
        $result->logo = $src;
		
		return $result;
		
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}

}

function groupMember($id){

	$sql = "SELECT * FROM group_member WHERE user_id = '$id'";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}

}


function countAnggota($id){

	$sql = "SELECT gr.group_id, gr.name, COUNT(gm.mem_id) AS Jumlah_Anggota 
			FROM groups gr 
			JOIN group_member gm
 			ON gr.group_id=gm.group_id 
    		WHERE gr.group_id=$id
    		GROUP BY gr.group_id, gr.name";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetch(PDO::FETCH_OBJ);
		$db = null;
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}

}

function groupPost($id){

	$sql = "SELECT gp.group_id, po.post_id,po.content_text,po.date,po.post_view,COUNT(co.comments_id) AS comment_count,po.user_id, la.label_id , la.label_name
          FROM posts po LEFT JOIN comments co 
          ON po.post_id=co.post_id
          LEFT JOIN user us
          ON po.user_id=us.user_id
          LEFT JOIN post_label pl 
          ON po.post_id=pl.post_id
          LEFT JOIN labels la
          ON pl.label_id=la.label_id
          JOIN group_post gp
          ON po.post_id=gp.post_id
          WHERE gp.group_id = '$id'
          GROUP BY po.post_id,po.date,po.post_view,po.user_id,us.name,us.user_photo, la.label_id , la.label_name
          ORDER BY po.date DESC";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}

}

function myGroup($id){

	$sql = "SELECT g.group_id, g.name, gm.user_id, g.logo
            FROM `groups` g JOIN `group_member` gm
            ON g.group_id = gm.group_id
            WHERE gm.user_id = '$id'";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		for($i=0; $i<count($result); $i++){
		    $image = $result[$i]->logo;
            $decoded = file_get_contents('../src/images/'.$image);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode($decoded);
        
            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:image/png;base64,'.$imageData;
            $result[$i]->logo = $src;
		}
		
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}

}


function Comment($id){
	try{
		$db = new db();
		$db = $db->connect();

		$sql = "SELECT co.comments_id, co.user_id, co.comment, co.date, us.name, us.user_photo
 				FROM posts po
     			JOIN comments co 
 				ON po.post_id=co.post_id
        		JOIN user us 
  				ON us.user_id=co.user_id 
        		WHERE po.post_id= $id
        		group by co.comments_id, co.user_id, co.comment, us.name, us.user_photo";

		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		for($i=0; $i<count($result); $i++){
		    $image = $result[$i]->user_photo;
            $decoded = file_get_contents('../src/images/'.$image);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode($decoded);
        
            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:image/png;base64,'.$imageData;
            $result[$i]->user_photo = $src;
		}
		
		return $result;
	} catch(Exception $ex){
		return $response->withJson(array('error'=>$ex->getMessage()),422);
	}
}

function groupMemberList($idgroup){
    
    $sql = "SELECT gm.group_id,us.user_id, us.name, us.prodi ,gm.role, gm.join_date 
            FROM group_member gm
            JOIN user us
            ON gm.user_id = us.user_id  
            WHERE gm.group_id=$idgroup 
            GROUP BY gm.group_id, us.name";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetchAll(PDO::FETCH_OBJ);
		$db = null;
	
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}
}

function locatePost($idpost){
    
    $sql = "SELECT * from group_post WHERE post_id = '$idpost'";

    try{
		$db = new db();
		$db = $db->connect();
		$query = $db->query($sql);
		$result = $query->fetch(PDO::FETCH_OBJ);
		$db = null;
	
		return $result;
	} catch(PDOException $e){
		return array("error :" => $e->getMessage());
	}
}