@extends('layout')

@section('body')
@include('partials.navbar')
<div class="jumbotron">
    <div class="row">
        <div class="col-xs-12"><h1>{{ $gameCount }} {{ trans_choice('objects.game', $gameCount) }}</h1></div>
    </div>
</div>
@include('partials.alerts')
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
        <th>Game Start Date</th>
        <th>Saves</th>
        <th>Cookies <span class="text-muted">(current / all-time)</span></th>
        <th>Latest Save</th>
        <th></th>
        </thead>
        <tbody>
        <?php foreach ($games as $game): ?>
            <?php
            $latestSave = $game->latestSave();
            if (!$latestSave) {
                continue;
            }
            ?>
            <tr>
                <td>
                    {{ with(new Carbon\Carbon($game->date_started))->toFormattedDateString() }}
                </td>
                <td>
                    {{ $game->saves()->count() }}
                </td>
                <td>
                    <b>{{ prettyNumbers($latestSave->cookies()) }}</b> / <i class="text-muted">{{
                        prettyNumbers($latestSave->allTimeCookies()) }}</i>
                </td>
                <td>
                    {{ $latestSave->updated_at->diffForHumans() }}
                </td>
                <td>
                    <a href="{{ action('GamesController@show', $game->id) }}" class="btn btn-block btn-primary btn-sm">Details...</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

{{ $games->links() }}
@stop

@section('footer-js')
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.8/d3.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/nvd3/1.1.15-beta/nv.d3.min.js"></script>

<script type="text/javascript">

</script>

@stop
