@extends('layout')

@section('body')
@include('partials.navbar')
@include('partials.alerts')
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Options for Your Account</h3>
    </div>
    <div class="panel-body">
        <a href="{{ action('SharesController@index') }}" class="btn btn-info btn-block btn-lg">Manage Shared Saves</a>
        <a href="{{ action('OptionsController@getBookmarklet') }}" class="btn btn-primary btn-block btn-lg">Use CookieSync in
            your Browser</a>
        <a href="{{ action('OptionsController@getResetPassword') }}" class="btn btn-primary btn-block btn-lg">Reset Your
            Password</a>
        <a href="{{ action('OptionsController@getEmailOptions') }}" class="btn btn-primary btn-block btn-lg">Edit your Email Address</a>
        <a href="{{ action('OptionsController@getDeleteUserView') }}" class="btn btn-block btn-danger btn-lg">Delete Your
            Account</a>
    </div>
</div>
@stop
