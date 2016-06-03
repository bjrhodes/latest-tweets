<?php
namespace MML;

require __DIR__ . '/FileCache.php';
require __DIR__ . '/Twitter.php';
require __DIR__ . '/j7mbo/twitter-api-php/TwitterAPIExchange.php';

/** CONFIG! */
$fileCachePath = __DIR__ . "/tweet-cache.json";
$memcachedServers = [];
$twitterSettings = [
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
];
$twitterHandle = '';
$defaultCount = 1;

/** APP!  */

$count = isset($_GET['count']) ? intval($_GET['count']) : $defaultCount;
$count = ($count > 0) ? $count : $defaultCount;

if (class_exists("Memcached") && count($memcachedServers)) {
    $Cache = new \Memcached();
    $Cache->addServers($memcachedServers);
} else {
    $Cache = new Models\FileCache($fileCachePath);
}
$cacheKey = "latestTweets_{$count}_{$twitterHandle}";
$tweetResponse = $Cache->get($cacheKey);

if (!$tweetResponse) {
    $Api = new \TwitterAPIExchange($twitterSettings);
    $Twitter = new Models\Twitter($Api);
    $tweetResponse = $Twitter->latestTweets($twitterHandle, $count);
    $Cache->set($cacheKey, $tweetResponse, 360);
}

$tweets = json_decode($tweetResponse, true);
$response = (is_array($tweets) && count($tweets) > 0) ? ['details' => $tweets, 'count' => count($tweets)] : ['error' => 'No tweets returned.'];

echo json_encode($response);
