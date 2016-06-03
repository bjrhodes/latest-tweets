/*global Promise */
var mml = mml || {};
/**
 * Lightweight AJAX implementation for simple get/post requests as JSON. Requires
 * a Promise implementation such as ES6Promises or native browser promises depending on support.
 *
 * @return {object} Containing methods getJSON and postJSON
 */
mml.ajax = function() {

    var makeRequest = function(client, payload) {

        return function(resolve, reject) {
            function handler() {
                // in the calling context, this will be XMLHTTPRequest
                  if (this.readyState === this.DONE) {
                        if (this.status === 200) {
                            resolve(this.response);
                        } else {
                            reject(this);
                        }
                  }
            }

            client.onreadystatechange = handler;
            client.send(payload);
        };
    };

    var getJSON = function(url) {
        var client = new XMLHttpRequest();
        client.open("GET", url);
        client.setRequestHeader("Accept", "application/json");

        return new Promise(makeRequest(client, '')).then(function(data){
            if (typeof(data) === 'string') {
                return JSON.parse(data);
            }
        });
    };

    var postJSON = function(url, payload) {
        var client = new XMLHttpRequest();
        client.open("POST", url);
        client.responseType = "json";
        client.setRequestHeader("Accept", "application/json");
        client.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        return new Promise(makeRequest(client, payload));
    };

    return {
        getJSON : getJSON,
        postJSON: postJSON
    };
};
