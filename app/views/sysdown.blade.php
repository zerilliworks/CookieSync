@extends('layout')

<?php View::share('_page_title', 'Sign Up') ?>

@section('css')
<style type="text/css">
    h1,
    h2,
    h3,
    h4 {
        font-family: 'Kavoon', 'Georgia', serif;
        text-align: center;
    }

    p.lead {
        text-align: center;
    }
</style>
@stop

@section('body')
    <h1>The System is Down</h1>
    <h3 style="color: #999">CookieSync is out to lunch.</h3>
    <p class="lead">We're doing some work on the site. Sit tight and check back in a few minutes.</p>
@stop