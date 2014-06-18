@extends('widelayout')

@section('css')
<link rel="stylesheet" href="http://cookiesync.zerilliworks.net/css/nv.d3.css"/>
<style type="text/css">
    #buildings-chart {
    }
</style>
@stop

@section('body')
@include('partials.navbar')
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-medium">
                <h4 class="stat-title">Cookies Saved:</h4>
                <h1 class="stat-text">{{ NumericHelper::makeRoundedHumanReadable($careerCookies) }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Prestige:</h4>
                <h1 class="stat-text">{{ prettyNumbers($latestSave->prestige) }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Saves:</h4>
                <h1 class="stat-text">{{ $saveCount }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Buildings:</h4>
                <h1 class="stat-text">{{ $latestSave->building_count }}</h1>
            </div>
        </div>
    </div>
</div>
@include('partials.alerts')
@if(!count($saves))
<div class="panel text-center">
    <h3>Nothing here yet!</h3>
    <p class="lead">Use the bookmarklet while playing Cookie Clicker or paste your save data in the field below.</p>
</div>
@else
<div class="row">
    <div class="col-xs-12 col-lg-6">
        <div class="stat stat-medium" style="width: 100%">
            <h4 class="stat-title">Building History:</h4>
        </div>
        <div class="panel">
            <svg id="buildings-chart" style="width: 100%; height: 300px;"></svg>
        </div>
    </div>
    <div class="col-xs-12 col-lg-6">
        <div class="stat stat-medium" style="width: 100%">
            <h4 class="stat-title">Cookie History:</h4>
        </div>
        <div class="panel">
            <svg id="cookie-chart" style="width: 100%; height: 300px;"></svg>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1 col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-8">
                {{ $saves->links() }}
            </div>
            <div class="col-xs-12 col-md-4">
                {{ Form::open(['action' => 'OptionsController@postSetListLength', 'method' => 'post', 'style' => 'float: right; padding: 20px 0;']); }}
                <div class="input-group">
                    <span class="input-group-addon">Show</span>
                    <select class="form-control" id="list-length-selector" name="list-length" >
                        <option {{ $paginationLength == 10 ? 'selected="selected"' : null }}value="10"> 10 Saves</option>
                        <option {{ $paginationLength == 20 ? 'selected="selected"' : null }}value="20"> 20 Saves</option>
                        <option {{ $paginationLength == 30 ? 'selected="selected"' : null }}value="30"> 30 Saves</option>
                        <option {{ $paginationLength == 50 ? 'selected="selected"' : null }}value="50"> 50 Saves</option>
                        <option {{ $paginationLength == 100 ? 'selected="selected"' : null }}value="100"> 100 Saves</option>
                    </select>
            <span class="input-group-btn">
                <button id="pagination-button" class="btn btn-success" type="submit" data-loading-text="Loading...">Go!</button>
            </span>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Save Date</th>
                    <th>Cookies <span class="text-muted">(current / all-time)</span></th>
                    <th>Actions</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                @foreach($saves as $save)
                <?php
                $allStatsHtml = implode('<br>', explode("\n", $save->allStats()));
                $allStatsHtml .= "<hr>" . $save->heavenlyChips() . " Heavenly Chips";
                ?>
                <tr class="{{ ($save->isGrandmapocalypse()) ? 'danger' : '' }}">
                    <td>
                        {{ $save->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <b>{{ prettyNumbers($save->cookies()) }}</b> / <i class="text-muted">{{ prettyNumbers($save->allTimeCookies()) }}</i>
                    </td>
                    <td>
                        {{ Form::open(['action' => ['SavesController@destroy', $save->id], 'method' => 'DELETE', 'class' => 'form-inline']) }}
                        <!--                <form class="form-inline" action="{{ action('SavesController@destroy', $save->id) }}" method="DELETE">-->
                        {{ Form::token() }}
                        <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                        <a class="btn btn-success btn-xs" href="{{ action('SavesController@show', $save->id) }}">View</a>
                        <a class="btn btn-info btn-xs stat-popover" data-placement="right" data-toggle="popover"
                           data-content="{{ $allStatsHtml }}">Stats</a>
                        </form>
                    </td>
                    <td>
                        <form class="form-inline" action="{{ action('SavesController@makePublic') }}" method="post">
                            {{ Form::token() }}
                            {{ Form::hidden('save_id', $save->id) }}
                            <button type="submit" class="btn btn-link" data-toggle="tooltip" data-placement="right" title="Share"
                                    href="{{ action('SharesController@show', $save->id) }}"><span class="glyphicon glyphicon-share"></span></button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif
{{ $saves->links() }}
<div class="row">
    <div class="col-xs-12">
        <div class="page-header">
            <h1>Add a New Save
                <small>Paste in your save game here...</small>
            </h1>

        </div>
        {{ Form::open(array('action' => 'SavesController@store')) }}
        <textarea class="form-control" name="savedata" id="save-data-field" rows="5"></textarea>
        <button type="submit" class="btn btn-lg btn-block btn-success">Save That Shit</button>
        </form>
    </div>
</div>
@stop

@section('footer-js')
<script src="//d3js.org/d3.v3.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/nvd3/1.1.15-beta/nv.d3.min.js"></script>
<script type="text/javascript">

    @section('override_reloader')
    window.addEventListener('storage', function(e) {
        if(e.key == 'cookiesync.pulse') {
            console.log("New save, reloading...");
            window.localStorage.setItem(e.key, '');
            updateChart();
        }
    });
    @stop

    $('.stat-popover').popover({placement: 'right', title: 'Buildings', trigger: 'hover', container: 'body', html: true});
    $('.btn-link').tooltip();

    $(".stat-large h1.stat-text").slabText({
        // Don't slabtext the headers if the viewport is under 380px
        "viewportBreakpoint":380
    });

    function updateChart() {
        $("#sample-button").button('loading');
        $.getJSON("{{ action('GamesController@getBuildingHistory', ['id' => $game->id, 'page' => Input::get('page', 0)]) }}", {}, function (data, textStats, jqXHR) {
            var buildingData = [];
            var buildingTypes = ['Cursors', 'Grandmas', 'Farms', 'Factories', 'Mines', 'Shipments', 'Labs',
            'Portals', 'Time Machines', 'Condensers', 'Prisms'];

            $(buildingTypes).each(function(index, building) {
                buildingData.push({
                    key: building,
                    values: data[building.toLowerCase().replace(/\s+/g, '_')].map(function (c, idx) {
                        return { x: idx + 1, y: parseInt(c) };
                    })
                });
            });

            nv.addGraph(function () {
                var chart = nv.models.stackedAreaChart()
                        .clipEdge(true)
                        .useInteractiveGuideline(true);

                chart.options({
                    showLegend: true,
                    showControls: false
                });

                chart.xAxis
                    .tickFormat(d3.format(',f'));

                chart.yAxis
                    .tickFormat(d3.format(',f'));

                d3.select('svg#buildings-chart')
                    .datum(buildingData)
                    .transition().duration(500)
                    .call(chart);

                nv.utils.windowResize(chart.update);

                return chart;
            });

        });

        $.getJSON("{{ action('GamesController@getCookieHistory', ['id' => $game->id, 'page' => Input::get('page', 0)]) }}", {}, function (data, textStats, jqXHR) {
            var cookieData = data.map(function (c, idx) {
                return { x: idx + 1, y: Math.max(0.1, parseFloat(c[1])) };
            });

            var timeData = data.map(function (c) {
                return '';
            });

            nv.addGraph(function () {
                var chart = nv.models.lineChart()
                    .clipEdge(true);
                chart.xAxis
                    .tickFormat(d3.format(',f'));

                chart.yAxis
                    .tickFormat(d3.format(',e'));

                chart.yScale(d3.scale.log());
                chart.xScale(d3.scale.linear());

                d3.select('svg#cookie-chart')
                    .datum([
                        { key: 'Cookies', values: cookieData, color: "#0000ff" }
                    ])
                    .transition().duration(500)
                    .call(chart);

                nv.utils.windowResize(chart.update);

                return chart;
            });

        });
    }

    updateChart();
</script>
@stop
