'use strict';

// Declare app level module which depends on filters, and services
angular.module('sgwRego', [
	   'ngRoute',
	   'ngResource',
	   'ui.bootstrap',
	   'rego.registration',
	   'rego.service'
    ])
	.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.when(
    		'/registration', 
    		{
    			templateUrl: '/client/main/views/registration.html', 
    			controller: 'RegistrationCtrl'
    		}
	    );
	    $routeProvider.otherwise({redirectTo: 'registration'});
	}])
	.controller('MainCtrl', ['$scope', '$route', '$routeParams', '$location',
	                         function($scope, noticeService, $route, $routeParams, $location) {
		$scope.route = $route;
		$scope.location = $location;
		$scope.routeParams = $routeParams;
		
//		noticeService.push(noticeService.ERROR, 'Oh snap! Change a few things up and try submitting again.');
//		noticeService.push(noticeService.SUCCESS, 'Well done! You successfully read this important alert message.');
//		noticeService.push(noticeService.WARN, 'Oh snap! Change a few things up and try submitting again.');
//		noticeService.push(noticeService.INFO, 'Well done! You successfully read this important alert message.');
		
	}])
	;
