<?php 
require_once(__DIR__.'/vendor/autoload.php'); 
require_once('Config.php');

use Silex\Application;
use App\Api\ApiProvider;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
// use Carbon\Carbon;

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
	// Add the LiveReload script
	$scripts[] = "http://rego.local:35729/livereload.js?snipver=1";
	return $scripts;
}

$app = new Application();
$app['debug'] = true;

// TODO Move these into the ApiProvider
// Handling CORS preflight request
$app->before(function (Request $request) {
	if ($request->getMethod() === "OPTIONS") {
		$response = new Response();
		$response->headers->set("Access-Control-Allow-Origin","*");
		$response->headers->set("Access-Control-Allow-Methods","GET,POST,PUT,DELETE,OPTIONS");
		$response->headers->set("Access-Control-Allow-Headers","Content-Type");
		$response->setStatusCode(200);
		$response->send();
	}
}, Application::EARLY_EVENT);

// Handling CORS response with right headers
$app->after(function (Request $request, Response $response) {
	$response->headers->set("Access-Control-Allow-Origin","*");
	$response->headers->set("Access-Control-Allow-Methods","GET,POST,PUT,DELETE,OPTIONS");
});

// Accepting JSON
$app->before(function (Request $request) {
	if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
		$data = json_decode($request->getContent(), true);
		$request->request->replace(is_array($data) ? $data : array());
	}
});

// Service for Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/views',
));

// Service for DBAL
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
		'db.options' => array(
			'driver' => 'pdo_mysql',
			'dbname' => DB_NAME,
			'user'   => DB_USER,
			'password' => DB_PASS
		)
));


// Routes
$app->get('/login', function() use($app) {
	return $app['twig']->render('login.twig.html');
});
$app->get('/', function() use($app) {
	return $app['twig']->render('main.twig.html', array('scripts' => getScripts() ));
});

$app->mount('/api', new ApiProvider());

$app->run();

?>