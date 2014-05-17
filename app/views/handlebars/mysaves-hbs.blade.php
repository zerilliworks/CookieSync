@extends('layout')

@section('body')
@include('partials.navbar')
<script type="text/x-handlebars">
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-large">
                <h4 class="stat-title">Cookies Saved:</h4>
                <h1 class="stat-text">{{ NumericHelper::makeRoundedHumanReadable($careerCookies) }} Cookies</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="stat stat-medium">
                <h4 class="stat-title">Saves:</h4>
                <h1 class="stat-text">{{ $saveCount }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Games:</h4>
                <h1 class="stat-text">{{ $gameCount }}</h1>
            </div>
            <div class="stat stat-medium">
                <h4 class="stat-title">Latest Save:</h4>
                <h1 class="stat-text">{{ $latestSaveDate }}</h1>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3">
@include('partials.alerts')
</div>
@if(!count($saves))
<div class="panel text-center">
    <h3>Nothing here yet!</h3>
    <p class="lead">Use the bookmarklet while playing Cookie Clicker or paste your save data in the field below.</p>
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
                <form class="form-inline" action="/mysaves/makepublic" method="post">
                    {{ Form::token() }}
                    {{ Form::hidden('save_id', $save->id) }}
                    <button type="submit" class="btn btn-link" data-toggle="tooltip" data-placement="right" title="Share"
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
<div class="row">
    <div class="col-xs-12">
        <div class="page-header">
            <h1>Add a New Save
                <small>Paste in your save game here...</small>
            </h1>

        </div>
        {{ Form::open(array('url' => 'addsave')) }}
        <textarea class="form-control" name="savedata" id="save-data-field" rows="5"></textarea>
        <button type="submit" class="btn btn-lg btn-block btn-success">Save That Shit</button>
        </form>
    </div>
</div>
</script>
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
