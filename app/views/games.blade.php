@extends('layout')

@section('body')
@include('partials.navbar')
<div class="jumbotron">
    <div class="row">
        <h1>{{ $gameCount }} {{ trans_choice('objects.game', $gameCount) }}</h1>
    </div>
</div>
@include('partials.alerts')
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
        <?php foreach($games as $game): ?>
        <?php
            $latestSave = $game->latestSave();
            if(!$latestSave)
            {
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
                <b>{{ prettyNumbers($latestSave->cookies()) }}</b> / <i class="text-muted">{{ prettyNumbers($latestSave->allTimeCookies()) }}</i>
            </td>
            <td>
                {{ $latestSave->updated_at->diffForHumans() }}
            </td>
            <td>
                <a href="/games/{{ $game->id }}" class="btn btn-block btn-primary btn-sm">Details...</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    {{ $games->links() }}
@stop
