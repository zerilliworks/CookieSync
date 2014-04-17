<!DOCTYPE html>
<html>
<head>
    <title>Save to CookieSync</title>
</head>
<body>

<script type="text/javascript">
    window.localStorage.setItem('cookiesync.pulse', 'new save ' + '{{ time() }}');
    window.close();
</script>
</body>
</html>
