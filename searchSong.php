<?php
require_once('lib/simple_html_dom.php');
require_once('lib/naughtyWords.php');

set_time_limit(0);// to infinity and beyond

function myUrlEncode($string) {
    $entities = array('_', '%26', '%27', '%3f');
    $replacements = array(' ', "&", "'", '?');
    return str_replace($replacements, $entities, $string);
}
function myUrlDecode($string) {
    $entities = array(' ', "&", "'", '?');
    $replacements = array('_', '%26', '%27', '%3f');
    return str_replace($replacements, $entities, $string);
}

$input = file_get_contents("php://input");
$postdata = json_decode($input);
$song = $postdata->song;
$song = myUrlEncode($song);
$artist = $postdata->artist;
$artist = myUrlEncode($artist);

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
    if ($attemptNum > 3*$attemptMultiplier) {
        //TODO: replace " and " with " & " in song and artist
    }
    elseif ($attemptNum > 2*$attemptMultiplier) {
        //TODO: replace " and " with " & " in song
    }
    elseif ($attemptNum > 1*$attemptMultiplier) {
        //TODO: replace " and "  with " & "  in artist
    }

    //default return statement. Unformatted search
     return [
         'song' => $song,
          'artist' => $artist,
     ];
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
//
//$url = 'http://www.azlyrics.com/lyrics/'.$artist.'/'.$song.'.html';
//
error_reporting(E_ERROR);
//$html = file_get_html($url);
$attempts = 0;
$lyricDiv = null;
$songTitleDiv = null;
$attemptMultiplier = 3;
$numFormatChecks = 0; // number of additional formatting attempt checks to try. 0 is base format only

while($attempts < ($attemptMultiplier*($numFormatChecks+1)) && !$lyricDiv){

    $searchFormat = alterSearchFormat($attempts, $attemptMultiplier, $song, $artist);
    $search_song = $searchFormat['song'];
    $search_artist = $searchFormat['artist'];
    $url="http://lyrics.wikia.com/wiki/" . $search_artist . ":" . $search_song;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 5);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); //timeout in seconds
    $str = curl_exec($curl);
    // Create a DOM object
    $html = new simple_html_dom();

    // Load HTML from a string
    $html->load($str);
    if($html && $html->find('div[class=lyricbox]')) {
        $lyricDiv = $html->find('div[class=lyricbox]')[0]->plaintext;
    }
    $html->clear();
    unset($html);
    curl_close($curl);
    $attempts++;
}



$lyric= html_entity_decode($lyricDiv);
$lyric = str_replace( '&#39;', "'", $lyric);
$lyric_lines = preg_split('/\n|\r\n?/', $lyric);

$finalSong = ucwords(myUrlDecode($song));
$finalArtist = ucwords(myUrlDecode($artist));

$data = [
    'recieved' => [
        'input' => $input,
        'post' => $postdata,
    ],
    'lyrics' => $lyric_lines,
    'flags' => flagLyrics($lyric_lines, naughtyWords),
    'final_song' => $finalSong,
    'final_artist' => $finalArtist,
];

header('Content-Type:application/json');
echo json_encode($data);
