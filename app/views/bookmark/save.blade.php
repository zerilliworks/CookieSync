<!DOCTYPE html>
<html>
<head>
    <title>Save to CookieSync</title>
    <link rel="stylesheet" href="/css/bookmarklet.css"/>
</head>
<body>
<button id="finish">Click to close</button>

<script src="/js/brainsocket.min.js"></script>
<script type="text/javascript">
    window.CSPulse = {};
    var pulseSocket = new WebSocket('ws://localhost:8080');
    CSPulse.BrainSocket = new BrainSocket(
        pulseSocket,
        new BrainSocketPubSub()
    );

    pulseSocket.addEventListener('open', function()
    {
        CSPulse.BrainSocket.message('cookiesync.pulse.{{ $pulseIdentifier }}', {msg: 'New save.'});
        window.close();
    });

</script>
</body>
</html>