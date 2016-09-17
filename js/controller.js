app.controller('ctrl', ['$scope', '$http', function($scope, $http) {
    $scope.search_song = '';
    $scope.search_artist = '';
    $scope.resultTest = '';
    $scope.song_found = false;
    $scope.song_unfound = false;

    const apiUrl = 'http://api.musixmatch.com/ws/1.1/';
    const trackSearchUrl = 'track.search';
    const testURL = 'http://lyrics.wikia.com/api.php?func=getSong&artist=' + $scope.search_artist + '&song=' + $scope.search_song+'&fmt=xml';

    $scope.search = function() {
        $scope.song_found = false;
        $scope.song_unfound = false;

        //https://developer.musixmatch.com/documentation/input-parameters
        var postObj = {
            apikey: 'b4d35084f95ba7ab1c29f553523fe610',
            q_track: $scope.search_song,
            q_artist: $scope.search_artist,
            f_has_lyrics: true,
            s_artist_rating: 'DESC'
        };
//apiUrl + trackSearchUrl
        $http.get(testURL, postObj).then(function(response) {
            $scope.resultTest = 'Success';
            $scope.song_found = true;
            $scope.song_unfound = false;
            console.log(response);

        }, function(response) {
            $scope.resultTest = 'Failure';
            $scope.song_unfound = true;
            $scope.song_found = false;
            console.log(response);

        })
    }
}]);