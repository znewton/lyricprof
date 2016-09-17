app.controller('ctrl', ['$scope', '$http', function($scope, $http) {
    $scope.search_song = '';
    $scope.search_artist = '';
    $scope.song_found = false;
    $scope.song_unfound = false;

    const apiUrl = 'http://api.musixmatch.com/ws/1.1/';
    const trackSearchUrl = 'track.search';
    const testURL = 'searchSong.php';

    $scope.search = function() {
        $scope.song_found = false;
        $scope.song_unfound = false;

        //https://developer.musixmatch.com/documentation/input-parameters
        var postObj = {
            song: $scope.search_song,
            artist: $scope.search_artist
        };
//apiUrl + trackSearchUrl
        $.post( testURL, postObj, function( data ) {
            console.log( data );
        }, "json");
    }
}]);