@extends('layout')

@section('body')
@include('partials.navbar')
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        @include('partials.alerts')
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Reset Your Password</h3>
            </div>
            <div class="panel-body">
                <form action="{{ action('RemindersController@postReset') }}" method="POST">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        {{ Form::label('email', 'Email Address') }}
                        {{ Form::email('email', $emailAddress, ['class' => 'form-control', 'placeholder' => 'Email Address']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('password', 'New Password') }}
                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('password_confirmation', 'Confirm New Password') }}
                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Password Confirmation']) }}
                    </div>
                    <button type="submit" class="btn btn-block btn-success">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop