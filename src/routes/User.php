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
       $result = $query->fetch(PDO::FETCH_OBJ);
       $db = null;
       
        $image = $result->user_photo;
        $decoded = file_get_contents('../src/images/'.$image);
        // Read image path, convert to base64 encoding
        $imageData = base64_encode($decoded);
    
        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:image/png;base64,'.$imageData;
        $result->user_photo = $src;
        
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}


//GET ATRIBUTE ID
function attID($id){
  try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT user_id, name, status, prodi, user_photo FROM user WHERE user_id = $id";
       $query = $db->query($sql);
       $result = $query->fetchObject();
       $db = null;
       
        $image = $result->user_photo;
        $decoded = file_get_contents('../src/images/'.$image);
        // Read image path, convert to base64 encoding
        $imageData = base64_encode($decoded);
    
        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:image/png;base64,'.$imageData;
        $result->user_photo = $src;
       
       return $result;
    
   } catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}

//GET ATRIBUTE ID
function attID_nonlink($id){
  try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT user_id, name, status, prodi, user_photo FROM user WHERE user_id = $id";
       $query = $db->query($sql);
       $result = $query->fetchObject();
       $db = null;
       
       return $result;
    
   } catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}

//GET DETAIL ID
function Admin($id){
	try{
       $db = new db();
       $db = $db->connect();

       $sql = "SELECT * FROM super_admin WHERE admin_id = '$id'";
       $query = $db->query($sql);
       $result = $query->fetch(PDO::FETCH_OBJ);
       $db = null;
       
       return $result;
    
   }catch(Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
}