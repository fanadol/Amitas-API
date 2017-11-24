<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

include('User.php');
include('Thread.php');
include('Announcement.php');

//GET ALL ANNOUNCEMENT
$app->get('/announcement', function(Request $request, Response $response) {
	$announ = Announcement();
	$stack = array();

	foreach ($announ as $value) {
		array_push($stack,$value);
	}
	echo json_encode($stack);
});

//GET ALL THREAD
$app->get('/thread', function(Request $request, Response $response) {
	$postingan = thread();
	$stack = array();

	foreach ($postingan as $post) {
		$post->user_id = detailID($post->user_id);
		array_push($stack,$post);
	}
	echo json_encode($stack);
});

//GET DETAIL THREAD
$app->get('/thread/{id}', function(Request $request, Response $response) {
	//MENGAMBIL ATRIBUT ID
	$id = $request->getAttribute('id');
	$det_thread = detailThread($id);
	$comm = Comment($id);
	$stack = array();

	foreach ($det_thread as $value) {
		$value->comments_id = $comm;
		array_push($stack,$value);
	}

	echo json_encode($stack);
});

//GET ALL USER
$app->get('/user', function(Request $request, Response $response) {
	$id = id();
	$stack = array();

	foreach ($id as $value) {
		array_push($stack,$value);
	}
	echo json_encode($stack);
});