var mml = mml || {};
/**
 * Injects latest tweets into el
 * @param  {object} conf configuration dictionary. Should include dict of urls with porperty latestTweet directing to an endpoint for latest tweet.
 * @param  {DOMElement} el   Where to apend tweet
 * @param  {object} ajax should be some kind of ajax implementation with a getJSON method that takes a URL and returns a promise.
 * @return {object}      instantiated tweet object single method of load.
 */
mml.tweet = function(conf, el, ajax) {

    var insertTweet = function(text) {
        var matches = text.match(/https?:\/\/[^\s]+/g);
        if (!matches) {
            el.appendChild(document.createTextNode(text));
        } else {
            matches.forEach(function(matched) {
                var link, leadPos = text.indexOf(matched);
                // create a link element, set href and text.
                link = document.createElement('a');
                link.href = matched;
                link.appendChild(document.createTextNode(matched));
                // add the text up to the link, then the link
                el.appendChild(document.createTextNode(text.substr(0, leadPos)));
                el.appendChild(link);
                // trim the added sections from the original text
                text = text.substr(leadPos + matched.length);
            });

            if (text.length) {
                el.appendChild(document.createTextNode(text));
            }
        }
    };

    var display = function(data) {
        var tweet = (data && data.details) ? data.details : {text: "No recent tweets founds."};

        while (el.firstChild) {
            el.removeChild(el.firstChild);
        }

        insertTweet(tweet.text);
    };

    return {
        load :function() {
            ajax.getJSON(conf.urls.latestTweet).then(display, display); // handles errors too!
        }
    };
};
