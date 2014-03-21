<?php 

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application(); 
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/views',
));

// Routes
$app->get('/hello/{name}', function($name) use($app) { 
    return 'Hello '.$app->escape($name); 
}); 
$app->get('/login', function() use($app) {
	return $app['twig']->render('login.twig.html');
});
$app->get('/', function() use($app) {
	return $app['twig']->render('main.twig.html');
});

$app->run(); 

?>