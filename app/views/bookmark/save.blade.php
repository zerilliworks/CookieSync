<!DOCTYPE html>
<html>
    <head>
        <title>Save to CookieSync</title>
        <link rel="stylesheet" href="/css/bookmarklet.css"/>
        <link href='http://fonts.googleapis.com/css?family=Kavoon' rel='stylesheet' type='text/css'>
    </head>
    <body>
        @if($didSave)
        <script type="text/javascript">
            window.close();
        </script>
        @else
        <h1 class="failure">Woops, Something broke.</h1>
        <h3>Your game could not be saved to CookieSync... Rats.</h3>
        @endif
    </body>
</html>