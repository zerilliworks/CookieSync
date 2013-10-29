@extends('layout')

@section('body')
        <nav class="navbar navbar-inverse" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="/about">CookieSync</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="/mysaves">My Saves</a></li>
                    <li class=""><a href="/options">Options</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/logout">Log Out</a></li>
            </div><!-- /.navbar-collapse -->
        </nav>
        <div class="jumbotron">
            <div class="row">
                <div class="col-xs-6">
                    <p class="lead">Save Count:</p>
                    <h1>{{ $saveCount }} Saves</h1>
                </div>
                <div class="col-xs-6">
                    <p class="lead">Latest Save:</p>
                    <h2>{{ $latestSaveDate }}</h2>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered">
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
                    $save->decode();
                    $allStatsHtml = implode('<br>', explode("\n", $save->allStats()));
                ?>
                <tr>
                    <td>
                        {{ $save->updated_at->diffForHumans() }}
                    </td>
                    <td>
                        <b>{{ $save->cookies(true) }}</b> / <i class="text-muted">{{ $save->allTimeCookies() }}</i>
                    </td>
                    <td>
                        <form class="form-inline" action="mysaves/nuke/{{ $save->id }}" method="post">
                            {{ Form::token() }}
                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                            <a class="btn btn-success btn-xs" href="mysaves/{{ $save->id }}">View</a>
                            <a class="btn btn-info btn-xs stat-popover" data-placement="right" data-toggle="popover" data-content="{{ $allStatsHtml }}">Stats</a>
                        </form>
                    </td>
                    <td>
                        <form class="form-inline" action="mysaves/makepublic" method="post">
                            {{ Form::token() }}
                            {{ Form::hidden('save_id', $save->id) }}
                            <button type="submit" class="btn btn-link" data-toggle="tooltip" data-placement="right" title="Share" href="share/{{ $save->id }}"><span class="glyphicon glyphicon-share"></span></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
<div class="row">
    <div class="col-xs-12">
        <div class="page-header">
            <h1>Add a New Save <small>Paste in your save game here...</small></h1>

        </div>
        {{ Form::open(array('url' => 'addsave')) }}
            <textarea class="form-control" name="savedata" id="save-data-field"  rows="5"></textarea>
            <button type="submit" class="btn btn-lg btn-block btn-success">Save That Shit</button>
        </form>
    </div>
</div>
@stop

@section('footer-js')
<script type="text/javascript">
    $('.stat-popover').popover({placement: 'right', title: 'Buildings', trigger: 'hover', container: 'body', html: true});
    $('.btn-link').tooltip();
</script>
@stop