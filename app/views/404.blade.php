@extends('layout')

<?php View::share('_page_title', 'Out to Lunch') ?>

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
@include('partials.navbar')
    <h1>Page Not Found</h1>
    <h3 style="color: #999">Something's missing... (HTTP 404)</h3>
    <p class="lead">There's no appropriate page or record that can service your request. Bummer. This resource may have once existed, but it's empty here now.</p>
@stop
