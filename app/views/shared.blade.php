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

    .panel-sized {
        max-height: 20em;
        overflow-y: scroll;
        /* For momentum scrolling on iOS devices */
        -webkit-overflow-scrolling: touch;
    }
</style>
@stop

@section('body')
@include('partials.navbar')
<div class="page-header">
    <h1>View Save Data <small>shared by {{ $save->user->name }}</small></h1>
</div>
<table class="table">
    <thead>
    <th>Game Stats</th>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>Cookies in Bank:</td>
        <td><b>{{ $save->cookies(true) }}</b></td>
    </tr>
    <tr>
        <td>Cookies Baked (All-Time):</td>
        <td>{{ $save->allTimeCookies() }}</td>
    </tr>
    </tbody>
</table>
<div class="row" style="margin-bottom: 30px;">
    <div class="col-sm-12 col-md-6">
        <div class="panel-group" id="achievements">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#achievements" href="#collapse-achievements">
                            Achievements (Show All)
                        </a>
                    </h4>
                </div>
                <div id="collapse-achievements" class="panel-collapse collapse ">
                    <div class="panel-body panel-sized">
                        <ul>
                            @foreach($save->gameStat('achievements') as $achievement)
                            <li>{{ $achievement }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="panel-group" id="upgrades">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#upgrades" href="#collapse-upgrades">
                            Upgrades (Show All)
                        </a>
                    </h4>
                </div>
                <div id="collapse-upgrades" class="panel-collapse collapse ">
                    <div class="panel-body panel-sized">
                        <ul>
                            @foreach($save->gameStat('upgrades') as $upgrade)
                            <li>{{ $upgrade }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($save->isGrandmapocalypse())
<div class="panel wrath">
    <h1>Elder Wrath has Taken Hold...</h1>
</div>
@endif
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
<pre><code>{{ $save->save_data }}</code></pre>
@stop