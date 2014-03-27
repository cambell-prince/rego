<?php
use App\Api\Register;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

require_once(__DIR__ . '/../src/vendor/autoload.php'); 
require_once(__DIR__ . '/TestConfig.php');

class RegisterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Returns the number of rows in the table via the getAll() method.
	 * @param Register $api
	 * @param Request $request
	 * @param Application $app
	 * @return int
	 */
	private function getRowCount(Register $api, Request $request, Application $app) {
		$response = $api->getAll($request, $app);
		$result = json_decode($response->getContent(), true);
		$rows = $result['result'];
		return count($rows);
	}
	
	/**
	 * Returns a Request for the given $json string
	 * @param string $json
	 * @return Request
	 */
	private function makeRequest($json) {
		$request = new Request();
		$data = json_decode($json, true);
		$request->request->replace(is_array($data) ? $data : array());
		return $request;
	}
	
	public function testCRUD() {
		$app = new Application();
		$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
				'db.options' => array(
					'driver' => 'pdo_mysql',
					'dbname' => DB_NAME,
					'user'   => DB_USER,
					'password' => DB_PASS
				)
		));
		
		$api = new Register();
		
		// API getAll()
		$countStart = $this->getRowCount($api, $this->makeRequest(''), $app);
// 		var_dump("countStart", $countStart);
		
		// API create()
		$json = '{ "email": "email@example.com", "name": "Some Name", "attendees": [{"name":"Attendee 1"}, {"name":"Attendee 2"}] }';
		$response = $api->create($this->makeRequest($json), $app);
		$result = json_decode($response->getContent(), true);
		
		$countCreated = $this->getRowCount($api, $this->makeRequest(''), $app);
// 		var_dump("countCreated", $countCreated);
		$this->assertEquals($countStart + 1, $countCreated);
		$this->assertArrayHasKey('id', $result);
		
		$id = $result['id'];
		
		// API getOne(id)
		$response = $api->getOne($this->makeRequest(''), $app, $id);
		$result = json_decode($response->getContent(), true);
		
// 		var_dump("getOne", $result);
		$this->assertEquals(1, count($result));
		$result = $result['result'];
		$this->assertEquals('email@example.com', $result['email']);
		$this->assertEquals('Some Name', $result['name']);
		$this->assertArrayHasKey('attendees', $result);
		$this->assertEquals(2, count($result['attendees']));
		$this->assertEquals('Attendee 1', $result['attendees'][0]['name']);
		$this->assertNotNull($result['attendees'][0]['id']);
		$this->assertEquals('Attendee 2', $result['attendees'][1]['name']);
		$this->assertNotNull($result['attendees'][1]['id']);
		
		// Update
		$result['email'] = 'other@example.com';
		$result['name'] = 'Other Name';
		$result['attendees'][1]['name'] = 'Other 2';
		$result['attendees'][]['name'] = 'Other 3';
		array_splice($result['attendees'], 0, 1);
		$json = json_encode($result);
		$response = $api->update($this->makeRequest($json), $app, $result['id']);
		$result = json_decode($response->getContent(), true);
// 		var_dump($result);
		
		$response = $api->getOne($this->makeRequest(''), $app, $id);
		$result = json_decode($response->getContent(), true);
// 		var_dump("getOne", $result);

		$this->assertEquals(1, count($result));
		$result = $result['result'];
		$this->assertEquals('other@example.com', $result['email']);
		$this->assertEquals('Other Name', $result['name']);
		$this->assertArrayHasKey('attendees', $result);
		$this->assertEquals(2, count($result['attendees']));
		$this->assertEquals('Other 2', $result['attendees'][0]['name']);
		$this->assertNotNull($result['attendees'][0]['id']);
		$this->assertEquals('Other 3', $result['attendees'][1]['name']);
		$this->assertNotNull($result['attendees'][1]['id']);
		
		// Delete
		$response = $api->delete($this->makeRequest(''), $app, $id);
		$result = json_decode($response->getContent(), true);
		
		// Get all and check count == countStart
		$countDeleted = $this->getRowCount($api, $this->makeRequest(''), $app);
		// 		var_dump("countDeleted", $countDeleted);
		$this->assertEquals($countStart, $countDeleted);
		
	
	}

}

?>
