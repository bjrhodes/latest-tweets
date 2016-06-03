<?php
namespace MML\Models;

class Twitter
{
    protected $Api;

    public function __construct(\TwitterAPIExchange $Api)
    {
        $this->Api = $Api;
    }

    public function latestTweet($username)
    {
        $username = urlencode($username);
        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

        return $this->Api->setGetField("?screen_name=$username&count=1")
                         ->buildOauth($url, 'GET')
                         ->performRequest();
    }
}
