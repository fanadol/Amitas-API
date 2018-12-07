<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//SHOW TAGS
function Tags(){
	$sql = "SELECT * FROM labels";
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

//SHOW DETAIL TAGS
function DetailTag($id){
	$sql = "SELECT * FROM labels WHERE label_id = $id";
    try {
      $db = new db();
      $db = $db->connect();
      $query = $db->query($sql);
      $result = $query->fetch(PDO::FETCH_OBJ);
      $db = null;
      return $result;
    } catch(PDOException $e) {
      return array("error" => $e->getMessage());
    }
}