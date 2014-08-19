@extends('layout')

@section('body')
@include('partials.navbar')
<div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3">
        @include('partials.alerts')
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Reset Your Password</h3>
            </div>
            <div class="panel-body">
                {{ Form::open(['action' => 'OptionsController@postResetPassword', 'method' => 'post']) }}
                    <div class="form-group">
                        {{ Form::label('password', 'New Password') }}
                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('password_confirmation', 'Confirm New Password') }}
                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm']) }}
                    </div>

                    <button type="submit" class="btn btn-block btn-success">Submit</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop
