<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET ALL REPORT
function report(){
	try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT * FROM reports";
       $query = $db->query($sql);
       $result = $query->fetchAll(PDO::FETCH_OBJ);
       $db = null;
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}