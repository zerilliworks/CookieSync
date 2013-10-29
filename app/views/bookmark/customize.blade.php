@extends('layout')

@section('css')
<style type="text/css">
    .bookmarklet,
    .bookmarklet:hover {
        text-transform: uppercase;
        color: white;
        text-decoration: none;
    }
</style>
@stop

@section('body')
<nav class="navbar navbar-inverse" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="/about">CookieSync</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class=""><a href="/mysaves">My Saves</a></li>
            <li class="active"><a href="/options">Options</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Log Out</a></li>
    </div>
    <!-- /.navbar-collapse -->
</nav>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Use CookieSync in Your Browser</h3>
    </div>
    <div class="panel-body">
        <p class="lead">Drag this into your browser's bookmarks bar:
            <span class="label label-primary"><span class="glyphicon glyphicon-bookmark"></span> <a class="bookmarklet"
                                                                                                    href="javascript:window.open('http://cookiesync.zerilliworks.net/external?d=' + Game.WriteSave(1), 'CookieSync_Save', 'toolbar=no,scrollbars=yes,width=200,height=200');">Save
                    to CookieSync</a></span>
        </p>

        <p>After that, just click that bookmark to store your game with CookieSync. When you use it, your game is saved
            immediately; you do not have to wait for an auto-save or use the game's save button.</p>
    </div>
</div>
@stop