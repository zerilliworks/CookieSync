@extends('layout')

@section('body')
@include('partials.navbar')
<div class="page-header">
    <h1>View Save Data <small>from {{ $save->updated_at }}</small></h1>
</div>
<table class="table">
    <thead>
        <th>Game Stats</th>
    </th>
    </thead>
    <tbody>
    <tr>
        <td>Cookies in Bank:</td>
        <td><b>{{ $cookiesBaked }}</b></td>
    </tr>
    <tr>
        <td>Cookies Baked (All-Time):</td>
        <td>{{ $allTimeCookies }}</td>
    </tr>
    </tbody>
</table>
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