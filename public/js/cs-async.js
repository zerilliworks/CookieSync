window.CSPulse = {};

function setUpAsync(identifier, server) {
    CSPulse.BrainSocket = new BrainSocket(
        new WebSocket(server),
        new BrainSocketPubSub()
    );

    CSPulse.BrainSocket.Event.listen('cookiesync.pulse.' + identifier, function(data){
        console.log(data);
        window.location.reload(true);
    });
}
