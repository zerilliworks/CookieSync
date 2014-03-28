<!DOCTYPE html>
<html>
<head>
    <title>Save to CookieSync</title>
    <link rel="stylesheet" href="/css/bookmarklet.css"/>
</head>
<body>

<script src="/js/brainsocket.min.js"></script>
<script type="text/javascript">
    window.CSPulse = {};
    var pulseSocket = new WebSocket('{{ Config::get('cookiesync.pulse_server') }}');
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