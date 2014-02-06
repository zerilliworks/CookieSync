@extends('layout')

<?php View::share('_page_title', 'Changelog') ?>

@section('css')
<style type="text/css">
    h1,
    h2,
    h3 {
        font-family: 'Kavoon', 'Georgia', serif;
        text-align: center;
    }

    .headline {
        background-image: url('/images/CookieSync-web.svg'), url('/images/gold_scale.png');
        background-size: cover, auto;
        background-position: center, top;
        background-repeat: no-repeat, repeat;
        width: 100%;
        padding-top: 3em;
        padding-bottom: 3em;
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
    <h1>Change Log</h1>
</div>
@stop

@section('body')

@if($changes)

    @foreach($changes as $change)
    <div class="page-header" style="text-align: left;">
        <h1>
            Version {{ $change->version }}<small> {{ with(new \Carbon\Carbon($change->release_date, 'America/New_York'))->toDayDateTimeString() }}</small>
        </h1>
    </div>

    <p>{{ $change->description }}</p>

    @endforeach

@endif

@stop