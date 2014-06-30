/*
 * Angluarization code for CookieSync
 */

var CSModule = angular.module('cookieSyncModule', []);

CSModule.filter('numeric_separators', function() {
        return function(input) {
            return input.toString().replace(/\B(?=(\d{3})+(?!\d))/g,',');
        };
    });

function SavesListController($scope, $http) {

    $http.get('')

}
