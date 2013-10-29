@extends('layout')

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
            <li class=""><a href="/options">Options</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Log Out</a></li>
    </div><!-- /.navbar-collapse -->
</nav>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Nuke Your Account</h3>
        </div>
        <div class="panel-body">
            <p class="text-danger">Be warned, fellow cookieverse vagrant. If you click this button, your account and
                saves will be <em>gone forever.</em> There is no return from this nexus of darkness where recycled
                bits and bytes are reprocessed into breakfast cereal marshmallows.</p>

            <h3 style="text-align: center" class="text-danger">Are you sure about this?</h3>
            {{ Form::open(array('url' => 'nukeme/doit')) }}
            <a class="btn btn-lg btn-block btn-success" href="/mysaves">No, Take Me Back!</a>
            <button class="btn btn-xs btn-block btn-danger" type="submit">Yes, Nuke Me Into Oblivion</button>
            {{ Form::close() }}
            <small class="text-muted">I made this button tiny so you wouldn't click it by mistake. You're welcome,
                dingus.
            </small>
        </div>
    </div>
@stop