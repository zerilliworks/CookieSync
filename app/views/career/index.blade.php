@extends('layout')

@section('css')
<link rel="stylesheet" href="https://zeril.li/assets/cookiesync/css/nv.d3.css"/>
<style type="text/css">
    #career-chart {
        padding-left: 30px;
    }
</style>
@stop

@section('body')
@include('partials.navbar')

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="input-group">
            <span class="input-group-addon">
                    Show
                </span>
            <select class="form-control" id="career-sample" name="sample">
                <option value="10"> 10 Saves</option>
                <option value="25"> 25 Saves</option>
                <option value="50"> 50 Saves</option>
                <option value="75"> 75 Saves</option>
                <option value="100"> 100 Saves</option>
                <option value="150"> 150 Saves</option>
                <option value="200"> 200 Saves</option>
                <option value="300"> 300 Saves</option>
                <option value="400"> 400 Saves</option>
                <option value="500"> 500 Saves</option>
            </select>
                <span class="input-group-btn">
                    <button id="sample-button" class="btn btn-success" type="button" data-loading-text="Loading...">Go!</button>
                </span>
        </div>
        <!-- /input-group -->
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="panel">
            <svg id="career-chart" style="width: 100%; height: 400px;"></svg>
            <p class="text-muted text-center">Click and drag on the smaller graph to zoom in on a region.</p>
        </div>
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

    function updateChart() {
        $("#sample-button").button('loading');
        $.getJSON('/cookiesync/career/history?sample=' + $("#career-sample").val(), {}, function (data, textStats, jqXHR) {

            $("#sample-button").button('reset');

            var cookieData = data.map(function (c, idx) {
//                console.log(c[1]);
                return { x: idx + 1, y: Math.max(0.1, parseFloat(c[1])) };
            });

//            console.debug(cookieData);

            var timeData = data.map(function (c) {
//            return c[0].date;
                return '';
            });

            nv.addGraph(function () {
                var chart = nv.models.lineChart();
                chart.xAxis
                  .tickFormat(d3.format(',f'));

                chart.yAxis
                  .tickFormat(d3.format(',e'));

                chart.yScale(d3.scale.log());
                chart.xScale(d3.scale.linear());

                d3.select('svg#career-chart')
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

    $("#sample-button").click(function()
    {
        updateChart();
    });

</script>
@stop
