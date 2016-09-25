<?php
require_once('lib/simple_html_dom.php');
require_once('lib/naughtyWords.php');

set_time_limit(0);// to infinity and beyond

function myUrlEncode($string, $search = false) {
    $entities = array('_', '%26', '%27', '%3f');
    $replacements = array(' ', "&", "'", '?');
    if($search){
        $entities[0] = '+';
    }
    return str_replace($replacements, $entities, $string);
}
function myUrlDecode($string) {
    $entities = array(' ', "&", "'", '?');
    $replacements = array('_', '%26', '%27', '%3f');
    return str_replace($replacements, $entities, $string);
}
function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

/**
 * @param $attemptNum
 * @param $attemptMultiplier
 * @param $song
 * @param $artist
 *
 * @return array
 */
function alterSearchFormat($attemptNum, $attemptMultiplier,  $song, $artist) {
    //TODO: try camelcase song,
    //TODO: add a the to beginning of song, artist, both
//    if ($attemptNum > 3*$attemptMultiplier) {
        //TODO: replace " and " with " & " in song and artist
//    }
//    elseif ($attemptNum > 2*$attemptMultiplier) {
        //TODO: replace " and " with " & " in song
//    }
//    elseif ($attemptNum > 1*$attemptMultiplier) {
        //TODO: replace " and "  with " & "  in artist
//    }

    //default return statement. Unformatted search
     return [
         'song' => $song,
          'artist' => $artist,
     ];
}

function generatePossibilities($query){
    $possibilities = [];
    $possibilities[] = myUrlEncode($query);
    $possibilities[] = myUrlEncode(str_ireplace('and', '%26', $query));
    $possibilities[] = myUrlEncode('the ' . $query);
//    $possibilities[] = myUrlEncode(str_ireplace(',', '', $query));
    return $possibilities;
}

function flagLyrics($lyrics, $naughty){
    $flags = [];
    foreach ($lyrics as $line) {
        foreach ($naughty as $naughtyWord){
            if (strpos(strtolower($line), strtolower($naughtyWord))) {
                $flaggedLine = $line;
                foreach ($naughty as $badWord) {
                    $flaggedLine = str_replace($badWord, '<strong>'.$badWord.'</strong>', $flaggedLine);
                    $flaggedLine = str_replace(ucfirst($badWord), '<strong>'.ucfirst($badWord).'</strong>', $flaggedLine);
                }
                $flags[] = $flaggedLine;
                break;
            }
        }
    }
    return $flags;
}

$input = file_get_contents("php://input");
$postdata = json_decode($input);
if($postdata->song) {
    $song = $postdata->song;
    $song = myUrlEncode($song);
    $song_possibilities = generatePossibilities($song);
} else {
    $song = null;
    $song_possibilities = null;
}
if($postdata->artist) {
    $artist = $postdata->artist;
    $artist = myUrlEncode($artist);
    $artist_possibilities = generatePossibilities($artist);
} else {
    $artist = null;
    $artist_possibilities = null;
}
//
//$url = 'http://www.azlyrics.com/lyrics/'.$artist.'/'.$song.'.html';
//
error_reporting(E_ERROR);
//$html = file_get_html($url);
$attempts = 0;
$lyricDiv = null;
$attemptMultiplier = 1;
$numFormatChecks = 0; // number of additional formatting attempt checks to try. 0 is base format only
$finalSong = null;
$finalArtist = null;

$searchLog = [];

//Scrape Lyrics
if($artist_possibilities && $song_possibilities) {
    $song_pos_counter = 0;
    $artist_pos_counter = 0;
    for($s = 0; $s < count($song_possibilities); $s++){
        $search_song = $song_possibilities[$s];
        for($a = 0; $a < count($artist_possibilities); $a++) {
            $search_artist = $artist_possibilities[$a];
            $url = "http://lyrics.wikia.com/wiki/" . $search_artist . ":" . $search_song;
            $searchLog[] = $url;
            $attempts = 0;
            while ($attempts < $attemptMultiplier && !$lyricDiv) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_REFERER, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl, CURLOPT_TIMEOUT, 10); //timeout in seconds
                $str = curl_exec($curl);
                // Create a DOM object
                $html = new simple_html_dom();

                // Load HTML from a string
                $html->load($str);
                if ($html && $html->find('div[class=lyricbox]')) {
                    $lyricDiv = $html->find('div[class=lyricbox]')[0]->plaintext;
                    $finalSong = ucwords(myUrlDecode($search_song));
                    $finalArtist = ucwords(myUrlDecode($search_artist));
                }
                $html->clear();
                unset($html);
                curl_close($curl);
                $attempts++;
            }
        }
    }
}

$attempts = 0;
$searchDiv = null;
//Scrape Search
if(!$lyricDiv){
    while ($attempts < $attemptMultiplier && !$searchDiv) {

        $search_song = myUrlEncode($postdata->song, true);
        if($postdata->artist){
            $search_artist = myUrlEncode($postdata->artist, true);
        } else {
            $search_artist = '';
        }
//        $surl = "http://lyrics.wikia.com/wiki/Special:Search?search=" . $search_song . "+" . $search_artist . '&fulltext=Search&page=1&ns0=1';
        $surl = "http://lyrics.wikia.com/wiki/Special:Search?search=" .$search_artist . '%3A' . $search_song . '&fulltext=Search';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $surl);
        curl_setopt($curl, CURLOPT_REFERER, $surl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); //timeout in seconds
        $str = curl_exec($curl);
        // Create a DOM object
        $html = new simple_html_dom();

        // Load HTML from a string
        $html->load($str);
        $searchDiv = [];
        //For Lyric Wikia Search
        if ($html && $html->find('a[class=result-link]')) {
            $list = $html->find('a[class=result-link]');
            for($i = 0; $i < count($list); $i++){
                if($i>0 && $list[$i]->href != $list[$i-1]->href)
                    $searchDiv[] = $list[$i]->href;
                elseif ($i==0)
                    $searchDiv[] = $list[$i]->href;
            }
            $searchDiv = array_slice($searchDiv, 0, 10);
        }
        $html->clear();
        unset($html);
        curl_close($curl);
        $attempts++;
    }
}

$lyric= html_entity_decode($lyricDiv);
$lyric = str_replace( '&#39;', "'", $lyric);
$lyric_lines = preg_split('/\n|\r\n?/', $lyric);

if(!$finalArtist){
    $finalArtist = ucwords(myUrlDecode($artist));
}
if(!$finalSong){
    $finalSong = ucwords(myUrlDecode($song));
}

$data = [
    'recieved' => [
        'input' => $input,
        'post' => $postdata,
    ],
    'lyrics' => $lyric_lines,
    'flags' => flagLyrics($lyric_lines, naughtyWords),
    'searchLog' => $searchLog,
    'final_song' => $finalSong,
    'final_artist' => $finalArtist,
    'search_result' => $searchDiv,
];

header('Content-Type:application/json');
echo json_encode($data);
