@extends('layout')

@section('body')
@include('partials.navbar')

<div class="row">
    <div class="col-xs-12">
        <div class="panel">
            <canvas id="career-chart" width="960" height="300"></canvas>
        </div>
    </div>
</div>

@stop

@section('footer-js')
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/0.2.0/Chart.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var ctx = document.getElementById("career-chart").getContext("2d");
    var careerChart = new Chart(ctx);

    $.getJSON('/cookiesync/career/history', {}, function(data, textStats, jqXHR) {

        var cookieData = data.map(function(c)
        {
            console.log(c[1]);
            return parseFloat(c[1]);
        });

        var timeData = data.map(function(c)
        {
//            return c[0].date;
            return '';
        });


        careerChart.Line({
            labels : timeData,
            datasets : [
                {
                    fillColor : "rgba(151,187,205,0.5)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    data : cookieData
                }
            ]
        }, {});
    });
</script>
@stop
