<?php
include('../lib/Config.php');
require_once 'silex/vendor/autoload.php';

header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Methods: GET, POST'); 

$app = new Silex\Application();

$services= $app['controllers_factory'];

$services->get('/list', function () use ($app) {
try {

	$ret = Doctrine_Query::create()
		->from('Categoria')
		->limit(8)
		->execute();

        $response['status'] = true;
    	$response['error'] = null;
        $response['data'] = $ret->toArray();

    	return $app->json($response);
    } catch (Exception $e) {
    	 $response['status'] = false;
    	 $response['error'] = $e->getMessage();
    	 $response['data'] = null;
    	 return $app->json($response);
    } 

});



    
$app->mount('/services', $services);


$app->run();
