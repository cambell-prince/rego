<?php 

require_once(__DIR__.'/vendor/autoload.php'); 
require_once('Config.php');


function getScripts() {
	$it = new RecursiveDirectoryIterator('client');
	$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);

	$scripts = array();
	foreach ($it as $file) {
		if ($file->isFile()) {
			$ext = $file->getExtension();
			$isMin = (strpos($file->getPathname(), '-min') !== false);
			if (!$isMin && $ext == 'js') {
				$scripts[] = '/' . $file->getPathname();
			}
		}
	}
	$scripts[] = "http://rego.local:35729/livereload.js?snipver=1";
	return $scripts;
}

$app = new Silex\Application(); 
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/views',
));

// Routes
$app->get('/login', function() use($app) {
	return $app['twig']->render('login.twig.html');
});
$app->get('/', function() use($app) {
	return $app['twig']->render('main.twig.html', array('scripts' => getScripts() ));
});

$app->run(); 

?>