<?php
namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Register
{
	public function getAll(Request $request, Application $app) {
		$result = $app['db']->fetchAll("SELECT * FROM registrant");
		return new JsonResponse(array('result' => $result));
	}
	
	public function getOne(Request $request, Application $app, $id) {
		$result = $app['db']->fetchAssoc("SELECT * FROM registrant WHERE id=?", array($id));
		if ($result) {
			$attendees = $app['db']->fetchAll("SELECT * FROM attendee WHERE rid=?", array($id));
			$result['attendees'] = $attendees;
		}
		return new JsonResponse(array('result' => $result));
	}
	
	public function delete(Request $request, Application $app, $id) {
		$result1 = $app['db']->delete('attendee', array('rid' => $id));
		$result2 = $app['db']->delete('registrant', array('id' => $id));
		return new JsonResponse(array('result' => true));
	
	}
	
	public function create(Request $request, Application $app) {
		$data = $request->request->all();
		// Fiddle the attendees
		$attendees = array_key_exists('attendees', $data) && is_array($data['attendees']) ? $data['attendees'] : array();
		unset($data['attendees']);
		$app['db']->insert('registrant', $data);
		$id = $app['db']->lastInsertId();
		foreach ($attendees as $attendee) {
			$attendee['rid'] = $id;
			$app['db']->insert('attendee', $attendee);
		}
		return new JsonResponse(array('id' => $id));
	}
	
	public function update(Request $request, Application $app, $id) {
		/*
		$data = $request->getContent();
		// Fiddle the attendees
		$attendees = $data['attendees'];
		unset($data['attendees']);
		$app['db']->insert('registrant', $data);
		$id = $app['db']->lastInsertId();
		foreach ($attendees as $attendee) {
			$attendee['rid'] = $id;
			$app['db']->insert('attendee', $attendee);
		}
		return new JsonResponse(array('id' => $id));
		*/
	}	
	
}

?>
