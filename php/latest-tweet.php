<?php
namespace MML;

require __DIR__ . '/FileCache.php';
require __DIR__ . '/Twitter.php';
require __DIR__ . '/j7mbo/twitter-api-php/TwitterAPIExchange.php';

$fileCachePath = __DIR__ . "/tweet-cache.json";
$memcachedServers = [];
$twitterSettings = [
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
];

if (class_exists("Memcached") && count($memcachedServers)) {
    $Cache = new \Memcached();
    $Cache->addServers($memcachedServers);
} else {
    $Cache = new Models\FileCache($fileCachePath);
}

$tweetResponse = $Cache->get('latestTweet');

if (!$tweetResponse) {
    $Api = new \TwitterAPIExchange($twitterSettings);
    $Twitter = new Models\Twitter($Api);
    $tweetResponse = $Twitter->latestTweet('mymedialabuk');
    $Cache->set('latestTweet', $tweetResponse, 360);
}

$tweets = json_decode($tweetResponse, true);
$response = (is_array($tweets) && count($tweets) > 0) ? ['details' => $tweets[0]] : ['error' => 'No tweets returned.'];
echo json_encode($response);
