@extends('layout')

@section('css')
<style type="text/css">
    body,
    html {
        width: 100%;
        height: 100%;
        background-image: url('/images/gold_scale.png');
    }

    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        padding-top: 30px;
        height: 0;
        overflow: hidden;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
    }
    .video-container iframe,
    .video-container object,
    .video-container embed {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        max-height: 720px;

        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    h1,
    h2,
    h3 {
        font-family: 'Kavoon','Helvetica','Arial', sans-serif;
        text-align: center;
    }

</style>
@stop

@section('upper-body')
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sm-12">
        <div class="jumbotron" style="box-shadow: 0 0 30px rgba(0,0,0,0.35); border-radius: 0 0 10px 10px">
            <h1>Well, off you go.</h1>
            <h2 class="text-muted">Come back any time.</h2>
            <h3><a href="/">Back to CookieSync</a></h3>
        </div>

    </div>
</div>

@stop