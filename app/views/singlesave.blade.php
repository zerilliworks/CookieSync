@extends('layout')

@section('css')
<link href='http://fonts.googleapis.com/css?family=Underdog' rel='stylesheet' type='text/css'>
<style type="text/css">
    .wrath {
        text-align: center;
        color: white;
        background-color: darkred;
        box-shadow: inset 0 0 10px black;
        background-image: url('/images/WrathGlasses.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-origin: padding-box;
        padding: 2em 0;
    }

    .wrath > h1 {
        font-family: 'Underdog', cursive;
    }

    #data-field {
        font-family: monospace;
    }
</style>
@stop

@section('body')
@include('partials.navbar')
<div class="page-header">
    <h1>View Save Data <small>from {{ $save->updated_at }}</small></h1>
</div>
<table class="table">
    <thead>
        <th>Game Stats</th>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>Cookies in Bank:</td>
        <td><b>{{ $cookiesBaked }}</b></td>
    </tr>
    <tr>
        <td>Cookies Baked (All-Time):</td>
        <td>{{ $allTimeCookies }}</td>
    </tr>
    <tr>
        <td>Game Started on:</td>
        <td>{{ $save->gameStat('date_started')->toFormattedDateString() }}</td>
    </tr>
    </tbody>
</table>
@if($save->isGrandmapocalypse())
    <div class="panel wrath">
        <h1>Elder Wrath has Taken Hold...</h1>
    </div>
@endif
<h2>Building Stats:</h2>
<table class="table-bordered table table-condensed">
    <tbody>
    <tr>
        <td>{{ $save->gameData['buildings.cursors'] }} Cursors</td>
        <td>{{ $save->gameData['buildings.grandmas'] }} Grandmas</td>
        <td>{{ $save->gameData['buildings.farms'] }} Farms</td>
        <td>{{ $save->gameData['buildings.factories'] }} Factories</td>
        <td>{{ $save->gameData['buildings.mines'] }} Mines</td>
    </tr>
    <tr>
        <td>{{ $save->gameData['buildings.shipments'] }} Shipments</td>
        <td>{{ $save->gameData['buildings.labs'] }} Alchemy Labs</td>
        <td>{{ $save->gameData['buildings.portals'] }} Portals</td>
        <td>{{ $save->gameData['buildings.time_machines'] }} Time Machines</td>
        <td>{{ $save->gameData['buildings.condensers'] }} Antimatter Condensers</td>
    </tr>
    </tbody>
</table>
<textarea id="data-field" class="form-control" rows="6" readonly>{{ $save->save_data }}</textarea>
@stop

@section('footer-js')
<script type="text/javascript">
    $("#data-field").hover(function(e)
    {
        e.target.focus();
        e.target.select();
    });
</script>
@stop