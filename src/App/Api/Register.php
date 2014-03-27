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
		$result = $this->_getOne($app, $id);
		return new JsonResponse(array('result' => $result));
	}
	
	private function _getOne(Application $app, $id) {
		$result = $app['db']->fetchAssoc("SELECT * FROM registrant WHERE id=?", array($id));
		if ($result) {
			$attendees = $app['db']->fetchAll("SELECT * FROM attendee WHERE rid=?", array($id));
			$result['attendees'] = $attendees;
		}
		return $result;
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
		// Read the $id
		$registrantExisting = $this->_getOne($app, $id);
		$registrantNew = $request->request->all();
		
		// Strip the attendees
		$attendeesExisting = $registrantExisting['attendees'];
		unset($registrantExisting['attendees']);
		
		$attendeesNew = $registrantNew['attendees'];
		unset($registrantNew['attendees']);
		
		// Update the registrant table
		if (array_key_exists('dtc', $registrantExisting))
			unset($registrantExisting['dtc']);
		
		$app['db']->update('registrant', array_merge($registrantExisting, $registrantNew), array('id' => $id));
		
		// Update or insert each attendee
		$attendeesExistingKeyed = array();
		foreach ($attendeesExisting as $attendee) {
			$attendeesExistingKeyed[$attendee['id']] = $attendee;
		}
		foreach ($attendeesNew as $attendee) {
			$attendee['rid'] = $id;
			if (array_key_exists('id', $attendee)) {
				$aid = $attendee['id'];
				if (!array_key_exists($aid, $attendeesExistingKeyed)) {
					throw new \Exception("Cannot update non-existent attendee $aid");
				}
				$app['db']->update('attendee', array_merge($attendeesExistingKeyed[$aid], $attendee), array('id' => $aid));
				unset($attendeesExistingKeyed[$aid]);
			} else {
				$app['db']->insert('attendee', $attendee);
			}
		}
		// Delete any attendees in the index that are not in the new set
		foreach ($attendeesExistingKeyed as $aid => $attendee) {
			$app['db']->delete('attendee', array('id' => $aid));
		}
		
		return new JsonResponse(array('result' => true));
	}	
	
}

?>
