@extends('layout')

@section('css')
<style type="text/css">
    h1,
    h3 {
        font-family: 'Kavoon', 'Georgia', serif;
        text-align: center;
    }

    .headline {
        background-image: url('/images/CookieSync-web.svg'), url('/images/gold_scale.png');
        background-size: contain, auto;
        background-position: center, top;
        background-repeat: no-repeat, repeat;
        width: 100%;
        padding-top: 8em;
        padding-bottom: 8em;
        margin-bottom: 4em;
        box-shadow: inset 0px -20px 30px -20px rgba(0, 0, 0, 0.33);
        color: white;
        text-align: center;
        text-shadow: #000000 0 10px 30px;
    }

    .headline > h1 {
        font-size: 5.3em;
    }
</style>
@stop

@section('upper-body')
<div class="headline">
    <h1>CookieSync</h1>

    <h3>By @zerilliworks</h3>
</div>
@stop

@section('body')
<h1>The <span class="text-muted">(ir)</span>Rationale</h1>
<p class="lead">CookieSync was thrown together one weekend after I had gotten a little tired of emailing cookie clicker
    saves to
    myself. Let me tell you, that game is the cat's pajamas, and I'd be damned if I had to stop playing it.
    In late October of 2013, I let loose this website, which is <b>WHOLLY UNNECESSARY</b> and probably <b>TOTALLY
        STUPID</b> to solve a dumb problem that no one cares about. <b>THAT IS HOW MUCH I CARE.</b></p>
<p class="lead">Mostly, this beast was built for fun. It's a silly program with little
    responsibility. It has only one purpose, amidst a thousand other ways of dealing with this problem: Wrangle Cookie
    Clicker saves.</p>

<h1>The Maker</h1>
<p class="lead">My name is Armand, and I very obviously build software. You can tweet <a href="https://twitter.com/zerilliworks">@zerilliworks</a> or email me: armand
    (at) zerilliworks (dot) net. I also run
    <a href="http://blog.zerilliworks.net">a blog.</a></p>

<h1>The Guts & Magic</h1>
<p class="lead">This bitch is trippin' on Laravel & PHP. Aaroooo! This site uses Greg Gilbert's <a
        href="https://github.com/greggilbert/recaptcha">Laravel 4 reCaptcha package</a> and Loic Sharma's <a
        href="https://github.com/loic-sharma/profiler">Profiler package.</a> The rest is MySQL and Bootstrap, plus some
    unremarkable JavaScript. Hosted with MediaTemple. </p>

<p class="lead">Right now, <b>{{ $userCount }}</b> people use the site, with <b>{{ $saveCount }}</b> games saved.</p>

@stop