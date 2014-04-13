@extends('layout')

@section('css')
<link href='http://fonts.googleapis.com/css?family=Underdog' rel='stylesheet' type='text/css'>
<style type="text/css">
    .wrath {
        text-align: center;
        color: white;
        background-color: darkred;
        box-shadow: inset 0 0 10px black;
        background-image: url('{{ Config::get('app.url') }}/images/WrathGlasses.png');
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

    .cookie-count {
        font-family: "Kavoon", "Helvetica", "Arial", "sans-serif";
        text-align: center;
        text-shadow: ;
    }
</style>
@stop

@section('body')
@include('partials.navbar')
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-large">
                <h4 class="stat-title">Cookies:</h4>
                <h1 class="stat-text">{{ NumericHelper::makeRoundedHumanReadable($save->cookies()) }} Cookies</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-medium">
                <h4 class="stat-title">Saved At:</h4>
                <h1 class="stat-text">{{ $save->updated_at->toFormattedDateString() }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Buildings:</h4>
                <h1 class="stat-text">{{ $save->building_count }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Achievements:</h4>
                <h1 class="stat-text">{{ count($save->achievements) }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Upgrades:</h4>
                <h1 class="stat-text">{{ count($save->upgrades) }}</h1>
            </div>
        </div>
    </div>
</div>
<div class="page-header">
    <h1>View Save Data <small>from {{ $save->updated_at }}</small></h1>
</div>
<h1 class="cookie-count">

</h1>
<table class="table">
    <thead>
        <th>Game Stats</th>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>Cookies in Bank:</td>
        <td><b>{{ prettyNumbers($cookiesBaked) }}</b></td>
    </tr>
    <tr>
        <td>Cookies Baked (All-Time):</td>
        <td>{{ prettyNumbers($allTimeCookies) }}</td>
    </tr>
    <tr>
        <td>Game Started on:</td>
        <td>{{ $save->gameStat('date_started')->toFormattedDateString() }}</td>
    </tr>
    <tr>
        <td>Heavenly Chips:</td>
        <td>{{ $save->heavenlyChips() }}</td>
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
<textarea id="data-field" class="form-control" rows="6" readonly>{{ $save->data }}</textarea>
@stop

@section('footer-js')
<script type="text/javascript">
    $("#data-field").hover(function(e)
    {
        e.target.focus();
        e.target.select();
    });

    $(".stat-large h1.stat-text").slabText({
        // Don't slabtext the headers if the viewport is under 380px
        "viewportBreakpoint":380,
        "maxFontSize" : 120
    });
</script>
@stop
