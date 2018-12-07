<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET ALL REPORT
function report(){
	try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT re.report_id, us1.user_id, us1.name, us2.name AS reported_by, re.report_message AS description FROM reports re 
        JOIN user us1 ON re.user_id = us1.user_id
        JOIN user us2 ON re.reporter_id = us2.user_id
        GROUP BY re.report_id, us1.user_id, us1.name, us2.name, re.report_message";
       $query = $db->query($sql);
       $result = $query->fetchAll(PDO::FETCH_OBJ);
       $db = null;
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}