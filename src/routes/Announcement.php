<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//SHOW ANNOUNCEMENT
function Announcement(){
	$sql = "SELECT po.post_id, atc.img_path 
          FROM posts po 
          JOIN attachments atc 
          ON po.post_id = atc.post_id
          WHERE atc.img_type='banner'";
    try {
      $db = new db();
      $db = $db->connect();
      $query = $db->query($sql);
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      
      for($i=0; $i<count($result); $i++){
		    $image = $result[$i]->img_path;
            $decoded = file_get_contents('../src/images/'.$image);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode($decoded);
        
            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:image/png;base64,'.$imageData;
            $result[$i]->img_path = $src;
		}
      
      return $result;
    } catch(PDOException $e) {
      return array("error" => $e->getMessage());
    }
}