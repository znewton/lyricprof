app.controller('ctrl', ['$scope', '$http', function($scope, $http) {
    $scope.search_song = '';
    $scope.search_artist = '';
    $scope.song_found = false;
    $scope.song_unfound = false;
    $scope.clean = false;
    $scope.dirty = false;
    $scope.searching = false;

    const apiUrl = 'http://api.musixmatch.com/ws/1.1/';
    const trackSearchUrl = 'track.search';
    const testURL = 'searchSong.php';

    $scope.flaggedLyrics = [];
    $scope.fullLyrics = [];

    $scope.search = function() {
        $scope.song_found = false;
        $scope.song_unfound = false;
        console.log('search');
        //https://developer.musixmatch.com/documentation/input-parameters
        var postObj = {
            song: $scope.search_song,
            artist: $scope.search_artist
        };
//apiUrl + trackSearchUrl
//         $.post( testURL, postObj, function( data ) {
//             console.log('success');
//             success();
//             console.log( data );
//         }).fail(function(data) {
//             console.log('fail');
//             $scope.song_found = false;
//             $scope.song_unfound = true;
//             console.log(data);
//         });
        if($scope.search_song != '' && $scope.search_artist != '') {
            $scope.searching = true;
            $http.post(testURL, postObj, []).then(function (response) {
                success();
                $scope.searching=false;
                console.log(response);
                $scope.flaggedLyrics = response.data.flags;
                $scope.fullLyrics = response.data.lyrics;
                if ($scope.flaggedLyrics.length > 0) {
                    $scope.dirty = true;
                }
            }, function (response) {
                fail();
                $scope.searching=false;
                console.log(response);
            });
        }

    };

    function success(){
        console.log('success');
        $scope.song_found = true;
        $scope.song_unfound = false;
    }
    function fail(){
        console.log('fail');
        $scope.song_found = false;
        $scope.song_unfound = true;
    }
}]);