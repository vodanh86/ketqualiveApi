var express = require("express");
var app = express();
var server = require("http").Server(app);
var io = require("socket.io")(server);
server.listen(3000);
app.get("/", function (req, res) {
    res.send("Running....");
});
module.exports = io;