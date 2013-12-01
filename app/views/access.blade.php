@extends('layout')

<?php View::share('_page_title', 'Sign Up') ?>

@section('css')
<style type="text/css">
    body {
        padding-top: 40px;
        padding-bottom: 0px;
        background-color: #eee;
    }

    h1,
    h2,
    h3,
    h4 {
        font-family: 'Kavoon', 'Georgia', serif;
    }

    p.lead {
        font-family: 'Kavoon', 'Georgia', 'Helvetica', sans-serif;
    }

    .forms-wrapper {
        max-width: 355px;
        padding: 0px;
        margin: 0 auto;
        height: inherit;

        -webkit-transition: all 200ms cubic-bezier(0.165, 0.840, 0.440, 1.000);
        -moz-transition: all 200ms cubic-bezier(0.165, 0.840, 0.440, 1.000);
        -o-transition: all 200ms cubic-bezier(0.165, 0.840, 0.440, 1.000);
        transition: all 200ms cubic-bezier(0.165, 0.840, 0.440, 1.000); /* easeOutQuart */
    }

    .form-container {
        width: 355px;

        padding: 0;
        margin: 0;

    }

    .form-signup {
        position: absolute;
        width: 355px;
        /*padding: 15px;*/
        overflow: hidden;
        -webkit-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
           -moz-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
             -o-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
                transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860); /* easeInOutCirc */

    }

    .form-signup .form-signup-heading,
    .form-signup .checkbox {
        margin-bottom: 10px;
    }

    .form-signup .checkbox {
        font-weight: normal;
    }

    .form-signup .form-control {
        position: relative;
        font-size: 16px;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .form-signup .form-control:focus {
        z-index: 2;
    }

    .form-signup input[type="text"] {
        margin-bottom: -1px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .form-signup input[type="email"] {
        margin-bottom: -1px;
        border-radius: 0;
    }

    .form-signup input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .form-group {
        margin: 0;
    }

    .form-register {
        position: relative;
        width: 0;
        padding: 0;
        overflow: hidden;

        background-color: #eee;
        z-index: 4;
    }

    .available {
        background-color: #89db2c;
        color: white;
    }

    .unavailable {
        background-color: #fa4;
        color: white;
    }

    .btn-link {
        text-align: center;
    }
</style>
@stop

@section('body')

<div class="forms-wrapper">
    <h1 style="text-align: center">CookieSync</h1>
    <h5 style="text-align: center" class="text-muted">For dopes that Cookie Click everywhere.</h5>

    @if( $errors->any() )

    <div class="alert alert-danger">
        <h4>Better double-check that.</h4>

        <p>Whatever you put in there, it made the database groan uncomfortably.</p>
        <ul>
            @foreach( $errors->toArray() as $error )
            <li>{{ $error[0] }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Login form -->
    <form id="login" class="form-signup" method="POST" action="access/login">
        <div class="form-container">
            <h4 style="text-align: center" class="text-muted">Log In to your account</h4>


            {{ Form::token() }}
            <div id="username-field" class="form-group">
                <input id="username-input" type="text" class="form-control" placeholder="User name" name="username"
                       autocapitalize="off" autocorrect="off" spellcheck="false" autofocus>
            </div>
            <input type="password" class="form-control" placeholder="Passkey" name="password">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Let Me In!</button>
            <a id="switch-to-register" class="btn btn-small btn-link btn-block" href="#">Or Create an Account</a>
        </div>


    </form>
    <!-- end login form -->

    <!-- registration form -->
    <form id="registration" class="form-signup form-register" style="width: 0px" method="POST" action="access/register">
        <div class="form-container">
            <h4 style="text-align: center" class="text-muted">Register a new account</h4>

            {{ Form::token() }}
            <div id="username-field" class="form-group">
                <input id="username-input" type="text" class="form-control" placeholder="User name" name="username"
                       autocapitalize="off" autocorrect="off" spellcheck="false" autofocus>
            </div>
            <input type="password" class="form-control" placeholder="Passkey" name="password">
            {{ Form::captcha() }}
            <button class="btn btn-lg btn-warning btn-block" type="submit">Sign Me Up!</button>
            <a id="switch-to-login" class="btn btn-small btn-link btn-block" href="#">Or Log In to an Account</a>
        </div>

    </form>

    <!-- end registration form -->
</div>



<div class="row">
    <div class="col-xs-12">
        <hr>
        <p class="lead" style="text-align: center">Mmkay, Here's the rub.</p>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <p>This thing stores and retrieves <a href="http://orteil.dashnet.org/cookieclicker">Cookie Clicker</a> saves.
            It's really easy. Create an account
            get a bookmarklet to stick in your browser. While you're Cookie Clickin', just mash that bookmark to
            save a game. That's pretty much it, just please do not forget your passkey. If you do, I can't help you.
        </p>

        <p>
            Log in anywhere to get your saves back. Don't worry, nothing is too tricky. You can add and delete saves at
            any time. You can also completely erase your account at any time. It's on the options page. No personal
            information <i>of any kind</i> is collected; only an anonymous analytics cookie (no ads or spam, ever).
        </p>
    </div>
    <div class="col-md-6 col-xs-12">
        <p>It's silly how alpha-level this is. Right now, plenty of stuff is broken and unimplemented. In its current
            state, CookieSync represents about eight hours' work, so I don't anticipate that it will stay super broken
            for long. Features in the works:</p>
        <ol>
            <li>Forked saves - Resuming from a save splits off another path of games going forward</li>
            <li>
                <del>NO SCIENTIFIC NOTATION, MMKAY?</del>
                Proper decimals now.
            </li>
            <li>Better Cookie Clicker save format support (reading achievements, upgrades, etc.)</li>
            <li>Stat tracking and career progress</li>
            <li>Slick, slick graphs</li>
            <li>
                <del>Sharing saved games</del>
                Use the share icon!
            </li>
            <li>Less Shitty interface design</li>
        </ol>
    </div>
    <small class="text-muted">{{ App::environment() }}</small>
</div>
@stop

@section('footer-js')
<!--<script type="text/javascript">-->
<!--    var availability = '';-->
<!---->
<!--    $("#username-field").tooltip({ placement: 'top', trigger: 'manual', title: 'Nope. Try another one.'});-->
<!---->
<!--    $("#username-input").blur(function (e) {-->
<!--        $("#username-input").removeClass('available');-->
<!--        $("#username-input").removeClass('unavailable');-->
<!---->
<!--        if (!$(e.target).val()) {-->
<!--            $("#username-field").tooltip('hide');-->
<!--            return;-->
<!--        }-->
<!--        $.get('jax/nametaken', { name: $(e.target).val() })-->
<!--            .done(function (data) {-->
<!--                availability = data;-->
<!--                if (data == 'available') {-->
<!--                    $("#username-input").addClass('available');-->
<!--                    $("#username-field").tooltip('hide');-->
<!--                }-->
<!--                else {-->
<!--                    $("#username-input").addClass('unavailable');-->
<!--                    $("#username-field").tooltip('show');-->
<!--                }-->
<!--            });-->
<!--    });-->
<!--</script>-->

<script type="text/javascript">
    $('#switch-to-register').click(function()
    {
        $('#registration').css('width', '355px');
//        $('.forms-wrapper').css('height', '360px');
    });

    $('#switch-to-login').click(function()
    {
        $('#registration').css('width', '0px');
//        $('.forms-wrapper').css('height', '240px');

    });
</script>
@stop