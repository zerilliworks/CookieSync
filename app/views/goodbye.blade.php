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

</style>
@stop

@section('upper-body')
<div class="video-container">
    <iframe width="1280" height="720" src="//www.youtube-nocookie.com/embed/VzC3ly2YMgE?rel=0" frameborder="0" allowfullscreen></iframe>
</div>
@stop