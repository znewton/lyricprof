app.controller('ctrl', ['$scope', '$http', function($scope, $http) {
    $scope.search_song = 'closer';
    $scope.search_artist = 'The_Chainsmokers';
    $scope.resultTest = '';

    const wikiaSearchURL = 'http://lyrics.wikia.com/wiki/';

    $scope.search = function() {
        searchURL = wikiaSearchURL + $scope.search_artist + ':' + $scope.search_song;

        $http.get(searchURL, []).then(function(response) {
            $scope.resultTest = 'Success';
            console.log(response);
        }, function(response) {
            $scope.resultTest = 'Failure';
            console.log(response);
        })
    }
}]);