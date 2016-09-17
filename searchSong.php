<?php

//$song = $_POST['song'];
//$artist = $_POST['artist'];

$song = 'chainsmokers';
$artist = 'closer';

$url = 'http://www.azlyrics.com/lyrics/'.$artist.'/'.$song.'.html';

$url="http://lyrics.wikia.com/wiki/The_Chainsmokers:Closer";
$html = file_get_contents($url);

$lyrics = strstr($html, '<div class="lyricbox">');

//$lyrics = strstr($lyrics, '</div>', true);




$data = [
    'recieved' => [
        'song' => $song,
        'artist' => $artist,
    ],
    'scrapedHTML' => $lyrics,
];
//
//header('Content-Type:application/json');
//echo json_encode($data);