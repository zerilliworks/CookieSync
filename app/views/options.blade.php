@extends('layout')

@section('body')
@include('partials.navbar')
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