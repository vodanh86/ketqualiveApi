var io = require('./io');
var channel = require('./channel');
// Connection event
io.on("connection", function (socket) {
    // Connect event
    socket.on('connect-channel', function(data){
        socket.join('channel-'+ data.channel_id);
        console.log('Join room: channel-'+ data.channel_id);
        socket.emit('channel-connected', data);
    });

    // Disconnect event
    socket.on("disconnect", function () {
        if(global.channel !== undefined){
            console.log('Leave room: '+ 'channel-'+ global.channel.id);
            socket.leave('channel-'+ global.channel.id);
        }
        console.log('Disconnected');
    });

    // channel
    channel.init(socket, io);
});