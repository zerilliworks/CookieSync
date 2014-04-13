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
@include('partials.navbar')

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Use CookieSync in Your Browser</h3>
    </div>
    <div class="panel-body">
        <p class="lead">Drag this into your browser's bookmarks bar:
            <span class="label label-primary"><span class="glyphicon glyphicon-bookmark"></span> <a class="bookmarklet"
                                                                                                    href="javascript:window.open('http://zeril.li/cookiesync/external?d=' + Game.WriteSave(1), 'CookieSync_Save', 'toolbar=no,scrollbars=yes,width=700,height=500');">Save
                    to CookieSync</a></span>
        </p>

        <p>After that, just click that bookmark to store your game with CookieSync. When you use it, your game is saved
            immediately; you do not have to wait for an auto-save or use the game's save button.</p>
    </div>
</div>
@stop
