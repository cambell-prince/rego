'use strict';

angular.module('rego.service', ['ngResource'])
	.factory('registrantService', ['$resource', function($resource) {
		return $resource('/api/register/:id', {id: '@id'});
	}])
	;
