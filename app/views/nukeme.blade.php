@extends('layout')

@section('body')
@include('partials.navbar')
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Nuke Your Account</h3>
        </div>
        <div class="panel-body">
            <p class="text-danger">Be warned, fellow cookieverse vagrant. If you click this button, your account and
                saves will be <em>gone forever.</em> There is no return from this nexus of darkness where recycled
                bits and bytes are reprocessed into breakfast cereal marshmallows.</p>

            <h3 style="text-align: center" class="text-danger">Are you sure about this?</h3>
            {{ Form::open(array('action' => 'OptionsController@postDeleteUserRequest')) }}
            <a class="btn btn-lg btn-block btn-success" href="{{ action('SavesController@index') }}">No, Take Me Back!</a>
            <button class="btn btn-xs btn-block btn-danger" type="submit">Yes, Nuke Me Into Oblivion</button>
            {{ Form::close() }}
            <small class="text-muted">I made this button tiny so you wouldn't click it by mistake. You're welcome,
                dingus.
            </small>
        </div>
    </div>
@stop
