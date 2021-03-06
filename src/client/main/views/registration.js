'use strict';

angular.module('rego.registration', ['rego.service'])
	.controller('RegistrationCtrl', ['$scope', 'registrantService', function($scope, registrantService) {
//		var data = {
//			email: 'me@example.com',
//			name: 'Some Name',
//			paymentMethod: 'bank',
//			attendees: [
//			    {
//			    	name: 'Some Camper',
//			    	age: '14',
//			    	gender: 'm'
//			    }
//			]
//		};

		var data = {};
		
		$scope.genders = [
		    { id: 'm', label: 'Male'},
		    { id: 'f', label: 'Female'}
		];
		
		$scope.payMethods = [
		    { id: 'ib', label: 'Internet Banking'},
		    { id: 'cc', label: 'Credit Card'},
		    { id: 'py', label: 'Pay at Church'}
		];
		
		var reset = function() {
			$scope.registrant = {};
			$scope.addAttendee();
		}
				
		$scope.addAttendee = function() {
			var newAttendee = {
				name: '',
				age: '',
				gender: ''
			};
			if ($scope.registrant.attendees == undefined) {
				$scope.registrant.attendees = [];
			}
			$scope.registrant.attendees.push(newAttendee);
		};
		
		$scope.removeAttendee = function($index) {
			$scope.registrant.attendees.splice($index, 1);
		};
		
		$scope.submit = function() {
			registrantService.save($scope.registrant);
		}
		
		reset();
		
	}]);