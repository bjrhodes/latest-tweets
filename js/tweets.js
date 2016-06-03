var mml = mml || {};
/**
 * Injects latest tweets into el
 * @param  {object} conf configuration dictionary. Should include dict of urls with porperty latestTweets directing to an endpoint for latest tweet.
 * @param  {DOMElement} el   Where to apend tweet
 * @param  {object} ajax should be some kind of ajax implementation with a getJSON method that takes a URL and returns a promise.
 * @return {object}      instantiated tweet object single method of load.
 */
mml.tweets = function(conf, el, ajax) {

    var insertTweet = function(text, tweetEl) {
        var matches = text.match(/https?:\/\/[^\s]+/g);
        if (!matches) {
            tweetEl.appendChild(document.createTextNode(text));
        } else {
            matches.forEach(function(matched) {
                var link, leadPos = text.indexOf(matched);
                // create a link element, set href and text.
                link = document.createElement('a');
                link.href = matched;
                link.appendChild(document.createTextNode(matched));
                // add the text up to the link, then the link
                tweetEl.appendChild(document.createTextNode(text.substr(0, leadPos)));
                tweetEl.appendChild(link);
                // trim the added sections from the original text
                text = text.substr(leadPos + matched.length);
            });

            if (text.length) {
                tweetEl.appendChild(document.createTextNode(text));
            }
        }
    };

    var display = function(data) {
        // confirm we've got an array of stuff or set a default.
        var tweets = (data && data.details && data.details.length && data.details.forEach) ? data.details : [{text: "No recent tweets founds."}];

        while (el.firstChild) {
            el.removeChild(el.firstChild);
        }
        tweets.forEach(function(tweet) {
            var tweetEl = document.createElement('div');
            insertTweet(tweet.text, tweetEl);
            tweetEl.classList = 'tweet-single';
            el.appendChild(tweetEl);
        })
    };

    return {
        load :function(count) {
            var url = conf.urls.latestTweets;
            count = parseInt(count, 10);
            // if no count specified, allow server to use default.
            url = (count > 0) ? url + '?count=' + count : url;

            ajax.getJSON(url).then(display, display); // display cheekily handles errors too
        }
    };
};
