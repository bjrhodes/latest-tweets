<?php
namespace MML\Models;

class Twitter
{
    protected $Api;

    public function __construct(\TwitterAPIExchange $Api)
    {
        $this->Api = $Api;
    }

    public function latestTweet($username) {
        return $this->latestTweets($username, 1);
    }

    public function latestTweets($username, $count)
    {
        $username = urlencode($username);
        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

        return $this->Api->setGetField("?screen_name=$username&count=$count")
                         ->buildOauth($url, 'GET')
                         ->performRequest();
    }
}
