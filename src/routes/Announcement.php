<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//SHOW ANNOUNCEMENT
function Announcement(){
	$sql = "SELECT po.post_id, atc.img_path 
          FROM post po 
          JOIN attachments atc 
          ON po.post_id = atc.post_id";
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