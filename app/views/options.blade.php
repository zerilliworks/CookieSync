@extends('layout')

@section('body')
<div class="col-md-8 col-md-offset-2 col-xs-12">
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
        </div><!-- /.navbar-collapse -->
    </nav>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Options for Your Account</h3>
                </div>
                <div class="panel-body">
                    <a href="/share" class="btn btn-info btn-block">Share a Save</a>
                    <a href="/bookmarklet" class="btn btn-primary btn-block">Use CookieSync in your Browser</a>
                    <a href="/export" class="btn btn-block btn-warning">Export All Your Data</a>
                    <a href="/nukeme" class="btn btn-block btn-danger">Delete Your Account</a>
                </div>
            </div>
        </div>
    </div>
@stop