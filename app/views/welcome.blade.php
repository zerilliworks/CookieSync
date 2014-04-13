@extends('layout')

<?php View::share('_page_title', 'Welcome') ?>

@section('css')
<style type="text/css">
    .bookmarklet,
    .bookmarklet:hover {
        text-transform: uppercase;
        color: white;
        text-decoration: none;
    }
</style>
@stop

@section('body')
@include('partials.navbar')
<h1>Welcome to CookieSync</h1>

<p class="lead">In a couple of minutes, you'll be ready to Cookie Click all over the place!</p>
<p class="">First, drag this bookmarklet into your browser's bookmarks bar:
<span class="label label-primary"><span class="glyphicon glyphicon-bookmark"></span> <a class="bookmarklet"
                                                                                        href="javascript:window.open('http://cookiesync.zerilliworks.net/external?d=' + Game.WriteSave(1), 'CookieSync_Save', 'toolbar=no,scrollbars=yes,width=600,height=700');">Save
        to CookieSync</a></span>
</p>
<p>While playing Cookie Clicker, just click that bookmark to save your game. It'll be uploaded and stored with
    CookieSync and categorized into a <i>Game.</i> A Game is a collection of saves belonging to one continuing game of
    Cookie Clicker. Resetting or wiping your game in Cookie Clicker will create a new game in CookieSync. With this, you
    can store many separate games or sessions and not confuse them.</p>
<p>Alternatively, you can use this box at the bottom of any list:</p>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-10 col-xs-offset-1">
        {{ Form::open(array('action' => 'SavesController@store')) }}
        <textarea class="form-control" name="savedata" id="save-data-field" rows="3"></textarea>
        <button type="submit" class="btn btn-block btn-success">Save That Shit</button>
        </form>
    </div>
</div>
<br>
<p>Use Cookie Clicker to export your save and paste that code into the box to save it.</p>

<p class="lead">Here's an example of a saved game:</p>

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
    <?php
    $save = new Save();
    $save->save_data = "MS4wMzkzfHwxMzg2MDg2MDEzNTE5OzEzODYwODYwMTM1MzM7MTM4NjA4NzA5NzI3MXwxMTExMTF8Njc5Ljg5NzcxMzc0NTc0NTQ7MTcwMC43OTk5OTk5OTk0Nzg0OzMzOTsw
OzQyOTsyOy0xOy0xOzA7MDswOzA7MDswOzA7MDswOzB8OCw4LDY0MywwOzIsMiw1MDgsMDsxLDEsMTE5LDA7MCwwLDAsMDswLDAsMCwwOzAsMCwwLDA7MCwwLDAsMDswLDAs
MCwwOzAsMCwwLDA7MCwwLDAsMDt8MjI1MTc5OTgyNDMzNDg2MzsyMjUxNzk5ODEzNjg1MjQ5OzIyNTE3OTk4MTM2ODUyNDk7MjI1MTc5OTgxMzY4NTI0OTsyMjUxNzk5ODEz
Njg1MjQ5OzEzNzQzODk1MzQ3M3wyMjcxNjk0MTAyMzMxNDA3OzIyNTE3OTk4MTM2ODUyNDk7MTAyNQ%3D%3D%21END%21";
    $save->decode();
    $allStatsHtml = implode('<br>', explode("\n", $save->allStats()));
    ?>
    <tr class="{{ ($save->isGrandmapocalypse()) ? 'danger' : '' }}">
        <td>
            5 minutes ago
        </td>
        <td>
            <b>{{ $save->cookies(true) }}</b> / <i class="text-muted">{{ $save->allTimeCookies() }}</i>
        </td>
        <td>
                <a class="btn btn-xs btn-danger" href="#">Delete</a>
                <a class="btn btn-success btn-xs" href="{{ route('welcome_example') }}">View</a>
                <a class="btn btn-info btn-xs stat-popover" data-placement="right" data-toggle="popover"
                   data-content="{{ $allStatsHtml }}">Stats</a>
        </td>
        <td>
                <a class="btn btn-link" data-toggle="tooltip" data-placement="right" title="Share"
                        href="#"><span class="glyphicon glyphicon-share"></span></a>
        </td>
    </tr>
    </tbody>
</table>

<p>Hover over the <a class="btn btn-info btn-xs">Stats</a> button to see a quick list of the buildings you
own. Click <a class="btn btn-success btn-xs" href="/welcome/example">View</a> to see the full information about a save.
    Give it a spin on the example above.
    <a class="btn btn-xs btn-danger" href="#">Delete</a> is pretty self-explanatory, but don't worry about accidents
    &mdash; you can undo it if you deleted a save by mistake.</p>
<p>You can click the <a class="btn btn-link btn-xs" href="#"><i class="glyphicon glyphicon-share"></i></a> icon to share
a save publicly. Copy the link from your browser and post it anywhere. Your friends don't need an account to view the
data. </p>
<p>Click the options link up top and you can find some tweaks and other features. Notably, you can delete your account
at any time. <em class="text-danger">Deleting an account is permanent and all-encompassing.</em> Your games, saves,
and preferences will be gone forever.</p>
<p class="lead">That's pretty much it! Go click <a href="{{ action('SavesController@index') }}">My Saves</a> to visit your dashboard. Click away,
fella. Grandma is waiting.</p>
@stop

@section('footer-js')
<script type="text/javascript">
    $('.stat-popover').popover({placement: 'right', title: 'Buildings', trigger: 'hover', container: 'body', html: true});
    $('.btn-link').tooltip();
</script>
@stop
