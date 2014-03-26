@extends('layout')

@section('body')
@include('partials.navbar')
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12">
            <h1>Shared Saves</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-medium">
                <h4 class="stat-title">Saves:</h4>
                <h1 class="stat-text">{{ $saveCount }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Latest Save:</h4>
                <h1 class="stat-text">{{ $latestSaveDate }}</h1>
            </div>
        </div>
    </div>
</div>
@include('partials.alerts')
<div class="col-md-3">

</div>
@if(!count($saves))
<div class="panel text-center">
    <h3>Nothing here yet!</h3>
    <p class="lead">Click the blue sharing icon in your saves list to make a Save public. </p>
</div>
@else
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
                <form class="form-inline" action="/mysaves/nuke/{{ $save->id }}" method="post">
                    {{ Form::token() }}
                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                    <a class="btn btn-success btn-xs" href="/mysaves/{{ $save->id }}">View</a>
                    <a class="btn btn-info btn-xs stat-popover" data-placement="right" data-toggle="popover"
                       data-content="{{ $allStatsHtml }}">Stats</a>
                </form>
            </td>
            <td>
                <form class="form-inline" action="{{ action('SavesController@makePrivate') }}" method="post">
                    {{ Form::token() }}
                    {{ Form::hidden('save_id', $save->id) }}
                    <button type="submit" class="btn btn-link" data-toggle="tooltip" data-placement="right" title="Make Private"
                            href="/share/{{ $save->id }}"><span class="glyphicon glyphicon-share"></span></button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
{{ $saves->links() }}
@stop

@section('footer-js')
<script type="text/javascript">
    $('.stat-popover').popover({placement: 'right', title: 'Buildings', trigger: 'hover', container: 'body', html: true});
    $('.btn-link').tooltip();

    $(".stat-large h1.stat-text").slabText({
        // Don't slabtext the headers if the viewport is under 380px
        "viewportBreakpoint":380
    });
</script>
@stop
