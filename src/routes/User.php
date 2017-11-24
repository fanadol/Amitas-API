<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET ALL ID
function ID(){
	try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT * FROM user";
       $query = $db->query($sql);
       $result = $query->fetchAll(PDO::FETCH_OBJ);
       $db = null;
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}

//GET DETAIL ID
function detailID($id){
	try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT * FROM user WHERE user_id = $id";
       $query = $db->query($sql);
       $result = $query->fetchObject();
       $db = null;
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}


//UPDATE USER
