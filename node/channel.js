var db = require('./db');
var helpers = require('./helpers');
module.exports = {
    init: function (socket, io) {
        socket.on("send-message", function (data) {
            module.exports.send(socket, io, data);
        });
    },
    send: function(socket, io, data){
        db.query("INSERT INTO mv_messages(`channel_id`,`sender_id`,`receiver_id`,`send_at`,`status`) VALUE(?,?,?,?,?)",[global.channel.id,data.sender_id,data.receiver_id,helpers.get_timestamp(),"1"], function(err, result){
            if(err){
                io.in('channel-'+ global.channel.id).emit("send-message-error", err);
            }else{
                io.in('channel-'+ global.channel.id).emit("send-message-success", result);
            }
        });
    }
};