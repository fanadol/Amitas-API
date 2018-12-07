<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function Upload($userPhoto){

    $directory = '../src/images';

    $exploded = explode(',', $userPhoto);
    $decoded = base64_decode($exploded[1]);
    
    if(strpos($exploded[0], 'jpeg')){
        $extension = 'jpg';
    } else if(strpos($exploded[0], 'png')) {
        $extension = 'png';
    } else {
        return "error file is not an image";
    }
    
    $basename = generateRandomString();
    
    $filename = $basename.'.'.$extension;
    $path = $directory.'/'.$filename;
    
    file_put_contents($path,$decoded);

    return $filename;
}

