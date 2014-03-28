<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CookieSync{{ (isset($_page_title)) ? ' - ' . $_page_title : '' }}</title>
    @if(isset($_page_description))
    <meta name="description" content="{{ $_page_description }}">
    @else
    <meta name="description" content="CookieSync - keep your Cookie Clicker saves updated wherever you go">
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/css/cookiesync.css"/>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Kavoon' rel='stylesheet' type='text/css'>
    @yield('css')
</head>
<body>
<!--[if lt IE 8]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
@yield('upper-body')
<div class="container" style="margin-bottom: 3em">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-12">
            @yield('body')
        </div>
    </div>
</div>

@include('partials.footer')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/slabText/2.3/jquery.slabtext.min.js"></script>
<script src="/js/cs-async.js"></script>
<script src="/js/brainsocket.min.js"></script>

<script type="text/javascript">
    var _gauges = _gauges || [];
    (function() {
        var t   = document.createElement('script');
        t.type  = 'text/javascript';
        t.async = true;
        t.id    = 'gauges-tracker';
        t.setAttribute('data-site-id', '526c9764108d7b49f6000021');
        t.src = '//secure.gaug.es/track.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(t, s);
    })();

    setUpAsync('{{ $pulseIdentifier }}');

</script>
@yield('footer-js')
</body>
</html>