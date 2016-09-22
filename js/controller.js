app.controller('ctrl', ['$scope', '$http', function($scope, $http) {
    $scope.search_song = '';
    $scope.search_artist = '';
    $scope.song_found = false;
    $scope.song_unfound = false;
    $scope.clean = false;
    $scope.dirty = false;
    $scope.searching = false;
    $scope.warningCollapse = true;
    $scope.errorCollapse = true;
    $scope.profanityProbability = 'Possibly';

    const apiUrl = 'http://api.musixmatch.com/ws/1.1/';
    const trackSearchUrl = 'track.search';
    const testURL = 'searchSong.php';

    $scope.flaggedLyrics = [];
    $scope.fullLyrics = [];

    $scope.search = function() {
        $scope.song_found = false;
        $scope.song_unfound = false;
        $scope.dirty = false;
        $scope.error = false;
        $scope.warningCollapse = true;
        $scope.errorCollapse = true;
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
        //&& $scope.search_artist != ''
        if($scope.search_song != '' ) {
            $scope.searching = true;
            $http.post(testURL, postObj, []).then(function (response) {
                $scope.searching=false;
                console.log(response);
                $scope.flaggedLyrics = response.data.flags;
                $scope.fullLyrics = response.data.lyrics;
                $scope.songTitle = response.data.final_song;
                $scope.artistTitle = response.data.final_artist;

                $scope.searchResult = response.data.search_result;

                $scope.search_song = response.data.final_song;
                $scope.search_artist = response.data.final_artist;

                if ($scope.fullLyrics && $scope.fullLyrics.length > 1){
                    success();
                } else if($scope.searchResult.length > 0){
                    searchSuccess();
                } else {
                    fail();
                }
            }, function (response) {
                $scope.error = true;
                $scope.searching=false;
                console.log(response);
            });
        }

    };

    function success(){
        console.log('success');
        if ($scope.flaggedLyrics && $scope.flaggedLyrics.length > 0) {
            $scope.dirty = true;
            if($scope.flaggedLyrics.length > 40){
                $scope.profanityProbability = 'Almost Certainly';
            } else if($scope.flaggedLyrics.length > 20){
                $scope.profanityProbability = 'Most Likely';
            } else if($scope.flaggedLyrics.length > 10){
                $scope.profanityProbability = 'Probably';
            } else if($scope.flaggedLyrics.length > 1){
                $scope.profanityProbability = 'Possibly';
            } else {
                $scope.profanityProbability = 'Maybe';
            }
        }
        $scope.song_found = true;
        $scope.song_unfound = false;
    }
    function searchSuccess() {
        console.log('Search Success');
        $scope.searchLinkList = [];
        var link = '';
        var artist = '';
        var song = '';
        var colonIndex = 0;
        for(var i = 0; i < $scope.searchResult.length; i++){
            // console.log()
            link = $scope.searchResult[i];
            link = link.replace('http://lyrics.wikia.com/wiki/', '');
            colonIndex = link.indexOf(':');
            artist = decodeURIComponent(link.substring(0, colonIndex)).replace(/_/g, ' ');
            song = decodeURIComponent(link.substr(colonIndex+1)).replace(/_/g, ' ');
            $scope.searchLinkList.push({
                artist: artist,
                song: song
            })
        }
        $scope.song_found = false;
        $scope.song_unfound = true;
    }
    function fail(){
        console.log('fail');
        $scope.song_found = false;
        $scope.song_unfound = true;
    }

    $scope.searchFromLink = function(song, artist){
        $scope.search_song = song;
        $scope.search_artist = artist;
        $scope.search();
    };

    $scope.toggleErrorCollapse = function() {
        $scope.errorCollapse = !$scope.errorCollapse;
    };
    $scope.toggleWarningCollapse = function() {
        $scope.warningCollapse = !$scope.warningCollapse;
    };
}]);