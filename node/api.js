require('dotenv').config();
var axios = require('axios');
module.exports = {
    post: function (url, params, success_callback, error_callback) {
        module.exports._call(url, params, success_callback, error_callback, 'post');
    },
    get: function (url, params, success_callback, error_callback) {
        module.exports._call(url, params, success_callback, error_callback, 'get');
    },
    _call: function(url, params, success_callback, error_callback, method){
        var options = {
            url: process.env.API_ENDPOINT + url,
            method: method,
            params: params,
            responseType: 'json',
            headers: {
                'API_CLIENT_ID': process.env.API_CLIENT_ID,
                'API_SECRET_KEY': process.env.API_SECRET_KEY
            }
        };

        axios(options).then(function(res){
            success_callback(res);
        }).catch(function(e){
            error_callback(e);
        });
    }
};