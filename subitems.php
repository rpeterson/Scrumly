<?php
require 'config.php';
require 'libs/Requests/library/Requests.php';

Requests::register_autoloader();
$headers = array('Accept' => 'application/json');
$options = array('auth' => array( $config['sprintly']['api_username'], $config['sprintly']['api_key']));
$request = Requests::request('https://sprint.ly/api/products/'.$config['sprintly']['product_id'].'/items/'.$_GET['item_id'].'/children.json', $headers, array(), Requests::GET, $options);

$body = json_decode($request->body, 1);
$body = json_encode($body);
echo $body;
