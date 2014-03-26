@extends('layout')

@section('body')
@include('partials.navbar')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Options for Your Account</h3>
                </div>
                <div class="panel-body">
                    <a href="{{ action('SharesController@index') }}" class="btn btn-info btn-block">Manage Shared Saves</a>
                    <a href="{{ action('OptionsController@getBookmarklet') }}" class="btn btn-primary btn-block">Use CookieSync in your Browser</a>
                    <a href="/export" class="btn btn-block btn-warning">Export All Your Data</a>
                    <a href="{{ action('OptionsController@getDeleteUserView') }}" class="btn btn-block btn-danger">Delete Your Account</a>
                </div>
            </div>
        </div>
    </div>
@stop