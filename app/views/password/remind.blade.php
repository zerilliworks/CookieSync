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
                {{ Form::open(['action' => 'RemindersController@postRemind', 'method' => 'post']) }}
                    <div class="form-group">
                        {{ Form::label('email', 'Email Address') }}
                        {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email Address']) }}
                    </div>
                    <button type="submit" class="btn btn-block btn-success">Send Reminder</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop