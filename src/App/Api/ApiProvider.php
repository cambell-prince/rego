<?php
namespace App\Api;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ApiProvider implements ControllerProviderInterface
{
	public function connect(Application $app) {
		$api = $app['controllers_factory'];
		
		// Register
		$api->get('/register', 'App\Api\Register::getAll');
		$api->get('/register/{id}', 'App\Api\Register::getOne');
		$api->delete('/register/{id}', 'App\Api\Register::delete');
		$api->post('/register', 'App\Api\Register::create');
		$api->post('/register/{id}', 'App\Api\Register::update');
		
		return $api;
	}
}

?>
