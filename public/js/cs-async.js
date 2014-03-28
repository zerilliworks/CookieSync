window.CSPulse = {};

function setUpAsync(identifier) {
    CSPulse.BrainSocket = new BrainSocket(
        new WebSocket('ws://localhost:8080'),
        new BrainSocketPubSub()
    );

    CSPulse.BrainSocket.Event.listen('cookiesync.pulse.' + identifier, function(data){
        console.log(data);
        window.location.reload(true);
    });
}
