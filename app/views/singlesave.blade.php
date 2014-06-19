@extends('layout')

@section('css')
<link href='//fonts.googleapis.com/css?family=Underdog' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="//zeril.li/assets/cookiesync/css/nv.d3.css"/>

<style type="text/css">
    .wrath {
        text-align: center;
        color: white;
        background-color: darkred;
        box-shadow: inset 0 0 10px black;
        background-image: url('https://zeril.li/assets/cookiesync/images/WrathGlasses.png');
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
        text-shadow:;
    }

    .swatch {
        width: 1em;
        height: 1em;
        display: inline-block;
    }
</style>
@stop

@section('body')
@include('partials.navbar')
@if(isset($sharedView) && $sharedView == true)
<h2>Data shared by <span class="text-info">{{ $save->user->name }}</span></h2>
@endif
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-large">
                <h4 class="stat-title">Cookies In Bank:</h4>

                <h1 class="stat-text">{{ round_num($save->cookies()) }}</h1>
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
<!--<table class="table">-->
<!--    <thead>-->
<!--    <th>Game Stats</th>-->
<!--    </th>-->
<!--    </thead>-->
<!--    <tbody>-->
<!--    <tr>-->
<!--        <td>Cookies in Bank:</td>-->
<!--        <td><b>{{ prettyNumbers($cookiesBaked) }}</b></td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>Cookies Baked (All-Time):</td>-->
<!--        <td>{{ prettyNumbers($allTimeCookies) }}</td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>Game Started on:</td>-->
<!--        <td>{{ $save->gameStat('date_started')->toFormattedDateString() }}</td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>Heavenly Chips:</td>-->
<!--        <td>{{ $save->heavenlyChips() }}</td>-->
<!--    </tr>-->
<!--    </tbody>-->
<!--</table>-->


<h2>Buildings Owned:</h2>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <svg id="buildings-chart" style="width: auto; height: 500px;"></svg>
    </div>
    <div class="col-xs-9 col-sm-6">
        <table class="table-bordered table table-condensed">
            <tbody>
            <tr>
                <td>{{ $save->gameData['buildings']['cursors'] }} Cursors</td>
                <td>{{ $save->gameData['buildings']['grandmas'] }} Grandmas</td>
            </tr>
            <tr>
                <td>{{ $save->gameData['buildings']['farms'] }} Farms</td>
                <td>{{ $save->gameData['buildings']['factories'] }} Factories</td>
            </tr>
                <td>{{ $save->gameData['buildings']['mines'] }} Mines</td>
                <td>{{ $save->gameData['buildings']['shipments'] }} Shipments</td>
            <tr>
                <td>{{ $save->gameData['buildings']['labs'] }} Alchemy Labs</td>
                <td>{{ $save->gameData['buildings']['portals'] }} Portals</td>
            </tr>
            <tr>
                <td>{{ $save->gameData['buildings']['time_machines'] }} Time Machines</td>
                <td>{{ $save->gameData['buildings']['condensers'] }} Antimatter Condensers</td>
            </tr>
            <tr>
                <td>{{ $save->gameData['buildings']['prisms'] }} Prisms</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<h2>Income Report:</h2>
<div class="stat stat-medium">
    <h4 class="stat-title">Cookies Earned:</h4>

    <h1 class="stat-text">{{ round_num($save->allTimeCookies()) }}</h1>
</div>
<div class="stat stat-medium">
    <h4 class="stat-title">Cookies Spent:</h4>

    <h1 class="stat-text">{{ round_num(bcsub($save->allTimeCookies(), $save->cookies())) }}</h1>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-7">
                <svg id="income-chart" style="width: auto; height: 500px;"></svg>
            </div>
            <div class="col-xs-12 col-md-5">
                <dl class="dl-horizontal">
                    <dt><span class="swatch" style="background-color: #68071c;"></span> Cursors</dt>
                    <dd>{{ round_num($buildingIncome['cursors']) }}</dd>
                    <dt><span class="swatch" style="background-color: #ff3e67;"></span> Grandmas</dt>
                    <dd>{{ round_num($buildingIncome['grandmas']) }}</dd>
                    <dt><span class="swatch" style="background-color: #b41a3a;"></span> Farms</dt>
                    <dd>{{ round_num($buildingIncome['farms']) }}</dd>
                    <dt><span class="swatch" style="background-color: #006811;"></span> Factories</dt>
                    <dd>{{ round_num($buildingIncome['factories']) }}</dd>
                    <dt><span class="swatch" style="background-color: #1ab433;"></span> Mines</dt>
                    <dd>{{ round_num($buildingIncome['mines']) }}</dd>
                    <dt><span class="swatch" style="background-color: #1977e7;"></span> Shipments</dt>
                    <dd>{{ round_num($buildingIncome['shipments']) }}</dd>
                    <dt><span class="swatch" style="background-color: #303d4e;"></span> Labs</dt>
                    <dd>{{ round_num($buildingIncome['labs']) }}</dd>
                    <dt><span class="swatch" style="background-color: #26b1b4;"></span> Portals</dt>
                    <dd>{{ round_num($buildingIncome['portals']) }}</dd>
                    <dt><span class="swatch" style="background-color: #e72c19;"></span> Time Machines</dt>
                    <dd>{{ round_num($buildingIncome['time_machines']) }}</dd>
                    <dt><span class="swatch" style="background-color: #b326b4;"></span> Condensers</dt>
                    <dd>{{ round_num($buildingIncome['condensers']) }}</dd>
                    <dt><span class="swatch" style="background-color: #f9c600;"></span> Prisms</dt>
                    <dd>{{ round_num($buildingIncome['prisms']) }}</dd>
                    <dt><span class="swatch" style="background-color: #e58600;"></span> Hand-made</dt>
                    <dd>{{ round_num($clickedCookies) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Building</th>
                    <th>Spent</th>
                    <th>Earned</th>
                    <th>ROI</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Cursor</td>
                    <td>{{ round_num($buildingExpenses['cursors']) }}</td>
                    <td>{{ round_num($buildingIncome['cursors']) }}</td>
                    <td>{{ $buildingROI['cursors'] }}%</td>
                </tr>
                <tr>
                    <td>Grandma</td>
                    <td>{{ round_num($buildingExpenses['grandmas']) }}</td>
                    <td>{{ round_num($buildingIncome['grandmas']) }}</td>
                    <td>{{ $buildingROI['grandmas'] }}%</td>
                </tr>
                <tr>
                    <td>Farm</td>
                    <td>{{ round_num($buildingExpenses['farms']) }}</td>
                    <td>{{ round_num($buildingIncome['farms']) }}</td>
                    <td>{{ $buildingROI['farms'] }}%</td>
                </tr>
                <tr>
                    <td>Factory</td>
                    <td>{{ round_num($buildingExpenses['factories']) }}</td>
                    <td>{{ round_num($buildingIncome['factories']) }}</td>
                    <td>{{ $buildingROI['factories'] }}%</td>
                </tr>
                <tr>
                    <td>Mine</td>
                    <td>{{ round_num($buildingExpenses['mines']) }}</td>
                    <td>{{ round_num($buildingIncome['mines']) }}</td>
                    <td>{{ $buildingROI['mines'] }}%</td>
                </tr>
                <tr>
                    <td>Shipment</td>
                    <td>{{ round_num($buildingExpenses['shipments']) }}</td>
                    <td>{{ round_num($buildingIncome['shipments']) }}</td>
                    <td>{{ $buildingROI['shipments'] }}%</td>
                </tr>
                <tr>
                    <td>Lab</td>
                    <td>{{ round_num($buildingExpenses['labs']) }}</td>
                    <td>{{ round_num($buildingIncome['labs']) }}</td>
                    <td>{{ $buildingROI['labs'] }}%</td>
                </tr>
                <tr>
                    <td>Portal</td>
                    <td>{{ round_num($buildingExpenses['portals']) }}</td>
                    <td>{{ round_num($buildingIncome['portals']) }}</td>
                    <td>{{ $buildingROI['portals'] }}%</td>
                </tr>
                <tr>
                    <td>Time Machine</td>
                    <td>{{ round_num($buildingExpenses['time_machines']) }}</td>
                    <td>{{ round_num($buildingIncome['time_machines']) }}</td>
                    <td>{{ $buildingROI['time_machines'] }}%</td>
                </tr>
                <tr>
                    <td>Condenser</td>
                    <td>{{ round_num($buildingExpenses['condensers']) }}</td>
                    <td>{{ round_num($buildingIncome['condensers']) }}</td>
                    <td>{{ $buildingROI['condensers'] }}%</td>
                </tr>
                <tr>
                    <td>Prism</td>
                    <td>{{ round_num($buildingExpenses['prisms']) }}</td>
                    <td>{{ round_num($buildingIncome['prisms']) }}</td>
                    <td>{{ $buildingROI['prisms'] }}%</td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong>{{ round_num($totalBuildingExpenses )}}</strong></td>
                    <td><strong>{{ round_num($totalBuildingIncome) }}</strong></td>
                    <td><strong>{{ $totalROI }}%</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
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
                            <li>{{ Lang::get("achievements.$achievement") }}</li>
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
                            <li>{{ Lang::get("upgrades.$upgrade") }}</li>
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

<textarea id="data-field" class="form-control" rows="6" readonly>{{ $save->data }}</textarea>
@stop

@section('footer-js')
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.8/d3.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/nvd3/1.1.15-beta/nv.d3.min.js"></script>
<script type="text/javascript">
    $("#data-field").hover(function (e) {
        e.target.focus();
        e.target.select();
    });

    $(".stat-large h1.stat-text").slabText({
        // Don't slabtext the headers if the viewport is under 380px
        "viewportBreakpoint": 380,
        "maxFontSize": 120
    });

    nv.addGraph(function() {
        var chart = nv.models.pieChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .showLabels(true)
          .labelThreshold(0.06)
          .labelType("value")
          .donut(true)
          .donutRatio(0.35);

        d3.select("svg#buildings-chart")
          .datum(buildingsData.sort(function(a, b) {
              return parseFloat(a.value) - parseFloat(b.value);
          }).reverse())
          .transition().duration(350)
          .call(chart);

        return chart;
    });

    nv.addGraph(function() {
        var chart = nv.models.pieChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .showLabels(true)
          .showLegend(true)
          .labelThreshold(0.06)
          .labelType("percent")
          .donut(true)
          .donutRatio(0.35);

        d3.select("svg#income-chart")
          .datum(incomeData.sort(function(a, b) {
              return parseFloat(a.value) - parseFloat(b.value);
          }).reverse())
          .transition().duration(350)
          .call(chart);

        return chart;
    });

    var buildingsData = [
        {
            value: {{ $buildings['cursors'] }},
            label: 'Cursors'
        },
        {
            value: {{ $buildings['grandmas'] }},
            label: 'Grandmas'
        },
        {
            value: {{ $buildings['farms'] }},
            label: 'Farms'
        },
        {
            value: {{ $buildings['factories'] }},
            label: 'Factories'
        },
        {
            value: {{ $buildings['mines'] }},
            label: 'Mines'
        },
        {
            value: {{ $buildings['shipments'] }},
            label: 'Shipments'
        },
        {
            value: {{ $buildings['labs'] }},
            label: 'Labs'
        },
        {
            value: {{ $buildings['portals'] }},
            label: 'Portals'
        },
        {
            value: {{ $buildings['time_machines'] }},
            label: 'Time machines'
        },
        {
            value: {{ $buildings['condensers'] }},
            label: 'Condensers'
        },
        {
            value: {{ $buildings['prisms'] }},
            label: 'Prisms'
        }
    ];

    incomeData = [
        {
            value: {{ $clickedCookies }},
            label: 'Handmade Cookies'
        },
        {
            value: {{ $buildingIncome['cursors'] }},
            label: 'Cursor Income'
        },
        {
            value: {{ $buildingIncome['grandmas'] }},
            label: 'Grandma Income'
        },
        {
            value: {{ $buildingIncome['farms'] }},
            label: 'Farm Income'
        },
        {
            value: {{ $buildingIncome['factories'] }},
            label: 'Factory Income'
        },
        {
            value: {{ $buildingIncome['mines'] }},
            label: 'Mine Income'
        },
        {
            value: {{ $buildingIncome['shipments'] }},
            label: 'Shipment Income'
        },
        {
            value: {{ $buildingIncome['labs'] }},
            label: 'Lab Income'
        },
        {
            value: {{ $buildingIncome['portals'] }},
            label: 'Portal Income'
        },
        {
            value: {{ $buildingIncome['time_machines'] }},
            label: 'Time Machine Income'
        },
        {
            value: {{ $buildingIncome['condensers'] }},
            label: 'Condenser Income'
        },
        {
            value: {{ $buildingIncome['prisms'] }},
            label: 'Prism Income'
        }
    ];
</script>
@stop
